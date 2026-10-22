<?php
session_start();
require 'db.php';
require 'lookups.php';
require 'matcher.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "beneficiary") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";
$matches = null;
$detected = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $desc = trim($_POST["description"]);
    $area = $_POST["area"];

    if ($desc === "" || !isset($AREAS[$area])) {
        $message = "Please describe your need and pick your area.";
    } else {
        $detected = detect_category($desc, $KEYWORDS);

        $stmt = $conn->prepare("INSERT INTO beneficiary_requests (userID, description, category, area) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $userID, $desc, $detected, $area);
        $stmt->execute();
        $requestID = $conn->insert_id;

        $matches = rank_ngos($conn, $detected, $AREAS[$area][0], $AREAS[$area][1]);

        $ins = $conn->prepare("INSERT INTO match_history (requestID, ngoID, match_score) VALUES (?, ?, ?)");
        foreach ($matches as $m) {
            $ins->bind_param("iid", $requestID, $m["ngoID"], $m["score"]);
            $ins->execute();
        }
    }
}
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
    <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
    <form method="post">
      <label>Describe your need in your own words</label>
      <textarea name="description" rows="4" required></textarea>
      <label>Your area</label>
      <select name="area">
        <?php foreach ($AREAS as $name => $coords) echo "<option value='" . htmlspecialchars($name) . "'>" . htmlspecialchars($name) . "</option>"; ?>
      </select>
      <button type="submit">Find help</button>
    </form>
  </div>

  <?php if ($matches !== null) { ?>
    <h3>Best matches for you</h3>
    <?php if ($detected) echo "<p>We understood your need as: <strong>" . htmlspecialchars($CATEGORIES[$detected]) . "</strong></p>";
          else echo "<p>We couldn't tell the exact type of help, so we're showing the closest organisations.</p>"; ?>
    <?php if (count($matches) === 0) echo "<p>No organisations registered yet.</p>"; ?>
    <?php foreach ($matches as $m) { ?>
      <div class="card">
        <span class="score"><?php echo $m["score"]; ?>%</span>
        <strong><?php echo htmlspecialchars($m["org_name"]); ?></strong><br>
        <?php echo htmlspecialchars($CATEGORIES[$m["category"]] ?? $m["category"]); ?> &middot;
        <?php echo htmlspecialchars($m["area"]); ?> &middot; <?php echo $m["distance"]; ?> km away
      </div>
    <?php } ?>
  <?php } ?>

  <p><a href="dashboard.php">Back to dashboard</a></p>
<?php include 'footer.php'; ?>
</body>
</html>