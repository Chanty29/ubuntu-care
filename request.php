<?php
session_start();
require 'db.php';
require 'lookups.php';
require 'matcher.php';
require 'ai_matcher.php';
require_once 'helpers.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "beneficiary") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";
$matches = null;
$detected = null;
$source = "";
$desc = "";
$area = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $desc = trim($_POST["description"] ?? "");
    $area = $_POST["area"] ?? "";

    if (mb_strlen($desc) < 10 || mb_strlen($desc) > 1000 || !isset($AREAS[$area])) {
        $message = "Please describe your need in 10 to 1000 characters and pick your area.";
    } else {
        // AI first, keyword matcher as the safety net
        $aiNote = null;
        [$detected, $source] = smart_detect($desc, $aiNote);
        $stmt = $conn->prepare("INSERT INTO beneficiary_requests (userID, description, category, area) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userID, $desc, $detected, $area);
        $stmt->execute();
        $requestID = $conn->insert_id;

        $matches = rank_ngos($conn, $detected, $AREAS[$area][0], $AREAS[$area][1]);

        $ins = $conn->prepare("INSERT INTO match_history (requestID, ngoID, match_score) VALUES (?, ?, ?)");
        $own = $conn->prepare("SELECT userID FROM ngo_profiles WHERE ngoID = ?");
        foreach ($matches as $m) {
            $ins->bind_param("iid", $requestID, $m["ngoID"], $m["score"]);
            $ins->execute();

            // Tell the organisation they have a new matched request
            $own->bind_param("i", $m["ngoID"]);
            $own->execute();
            $owner = $own->get_result()->fetch_assoc();
            if ($owner) {
                notify($conn, $owner["userID"], "New matched request", "A new request in " . $area . " matches your organisation (" . $m["score"] . "% match).");
            }
        }

        log_action($conn, $userID, "Help request submitted", ($detected ?? "uncategorised") . ", " . $area);
        $desc = "";
        $area = "";
    }
}

$usedAI = strpos($source, "AI") === 0;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Request Support - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>What do you need help with?</h2>
    <?php if ($message) echo "<p style='background:#FEE2E2;color:#991B1B;padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>"; ?>
    <form method="post" id="reqForm">
      <label>Describe your need in your own words</label>
      <textarea name="description" rows="4" required minlength="10" maxlength="1000"><?php echo htmlspecialchars($desc); ?></textarea>
      <label>Your area</label>
      <select name="area" required>
        <option value="">Choose your area</option>
        <?php foreach ($AREAS as $name => $coords) {
          $sel = ($area === $name) ? "selected" : "";
          echo "<option value='" . htmlspecialchars($name) . "' $sel>" . htmlspecialchars($name) . "</option>";
        } ?>
      </select>
      <button type="submit" id="reqBtn">Find help</button>
    </form>
  </div>

  <?php if ($matches !== null) { ?>
    <h3>Best matches for you</h3>
    <?php if ($detected) { ?>
      <p>We understood your need as: <strong><?php echo htmlspecialchars($CATEGORIES[$detected]); ?></strong><br>
         <small>Detected by: <strong><?php echo $usedAI ? "AI" : "keyword matching"; ?></strong>
         <?php if (!$usedAI) echo "(the AI was busy, so the backup matcher was used)"; ?>
         <?php if (!$usedAI && !empty($aiNote)) echo "<br><small style='color:#64748B'>Note: " . htmlspecialchars($aiNote) . "</small>"; ?></small></p>
    <?php
     } else { ?>
      <p>We couldn't tell the exact type of help, so we're showing the closest organisations.</p>
    <?php } ?>

    <?php if (count($matches) === 0) echo "<p>No organisations registered yet.</p>"; ?>
    <?php foreach ($matches as $m) { ?>
      <div class="card">
        <span class="score"><?php echo $m["score"]; ?>%</span>
        <strong><?php echo htmlspecialchars($m["org_name"]); ?></strong><br>
        <?php echo htmlspecialchars($CATEGORIES[$m["category"]] ?? $m["category"]); ?> &middot;
        <?php echo htmlspecialchars($m["area"]); ?> &middot; <?php echo $m["distance"]; ?> km away
      </div>
    <?php } ?>
    <p><a href="my_activity.php">Track your request</a></p>
  <?php } ?>

  <p><a href="dashboard.php">Back to dashboard</a></p>
<script>
  var f = document.getElementById("reqForm");
  if (f) f.addEventListener("submit", function () {
    var b = document.getElementById("reqBtn");
    if (f.checkValidity()) { b.textContent = "Understanding your request..."; b.disabled = true; }
  });
</script>
<?php include 'footer.php'; ?>
</body>
</html>