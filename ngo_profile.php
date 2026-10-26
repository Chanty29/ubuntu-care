<?php
session_start();
require 'db.php';
require 'lookups.php';
require_once 'helpers.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ngo_admin") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";
$isError = false;

// Load the saved profile (if any)
$q = $conn->prepare("SELECT ngoID, org_name, category, area, status FROM ngo_profiles WHERE userID = ?");
$q->bind_param("i", $userID);
$q->execute();
$existing = $q->get_result()->fetch_assoc();

$org  = $existing["org_name"] ?? "";
$cat  = $existing["category"] ?? "";
$area = $existing["area"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $org  = trim($_POST["org_name"] ?? "");
    $cat  = $_POST["category"] ?? "";
    $area = $_POST["area"] ?? "";

    if (mb_strlen($org) < 3 || mb_strlen($org) > 100) {
        $message = "Organisation name must be 3 to 100 characters.";
        $isError = true;
    } elseif (!preg_match("/^[A-Za-z0-9 .,&'()\\-]+$/u", $org)) {
        $message = "Organisation name can only contain letters, numbers and basic punctuation.";
        $isError = true;
    } elseif (!isset($CATEGORIES[$cat])) {
        $message = "Please choose a service category.";
        $isError = true;
    } elseif (!isset($AREAS[$area])) {
        $message = "Please choose the area you operate in.";
        $isError = true;
    } else {
        $lat = $AREAS[$area][0];
        $lng = $AREAS[$area][1];

        if ($existing) {
            $stmt = $conn->prepare("UPDATE ngo_profiles SET org_name=?, category=?, area=?, latitude=?, longitude=? WHERE ngoID=?");
            $stmt->bind_param("sssddi", $org, $cat, $area, $lat, $lng, $existing["ngoID"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO ngo_profiles (userID, org_name, category, area, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdd", $userID, $org, $cat, $area, $lat, $lng);
        }

        if ($stmt->execute()) {
            if ($existing) {
                $message = "Profile updated.";
            } else {
                $message = "Profile saved. An admin will review it before you appear in matching.";
                log_action($conn, $userID, "NGO application submitted", $org);
            }
            // Reload so the status badge is correct
            $q = $conn->prepare("SELECT ngoID, org_name, category, area, status FROM ngo_profiles WHERE userID = ?");
            $q->bind_param("i", $userID);
            $q->execute();
            $existing = $q->get_result()->fetch_assoc();
        } else {
            $message = "Something went wrong. Please try again.";
            $isError = true;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NGO Profile - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <h2>Your organisation</h2>

  <?php if ($existing) { ?>
    <p>Status: <?php echo ngo_badge($existing["status"]); ?></p>
    <?php if ($existing["status"] === "pending") echo "<p class='muted'>An admin is reviewing your organisation. You will get a notification when it is approved.</p>"; ?>
    <?php if ($existing["status"] === "rejected") echo "<p class='muted'>Your application was not approved. Update your details and contact the admin.</p>"; ?>
  <?php } ?>

  <?php if ($message) {
      $style = $isError ? "background:#FEE2E2;color:#991B1B;" : "background:#DCFCE7;color:#166534;";
      echo "<p style='$style padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>";
  } ?>

  <div class="panel">
    <form method="post" novalidate>
      <label>Organisation name</label>
      <input type="text" name="org_name" required minlength="3" maxlength="100"
             value="<?php echo htmlspecialchars($org); ?>">

      <label>Main service category</label>
      <select name="category" required>
        <option value="">Choose a category</option>
        <?php foreach ($CATEGORIES as $key => $label) {
          $sel = ($cat === $key) ? "selected" : "";
          echo "<option value='" . htmlspecialchars($key) . "' $sel>" . htmlspecialchars($label) . "</option>";
        } ?>
      </select>

      <label>Area you operate in</label>
      <select name="area" required>
        <option value="">Choose an area</option>
        <?php foreach ($AREAS as $name => $coords) {
          $sel = ($area === $name) ? "selected" : "";
          echo "<option value='" . htmlspecialchars($name) . "' $sel>" . htmlspecialchars($name) . "</option>";
        } ?>
      </select>

      <button type="submit"><?php echo $existing ? "Update profile" : "Save profile"; ?></button>
    </form>
  </div>

  <p><a href="dashboard.php">Back to dashboard</a></p>
<?php include 'footer.php'; ?>
</body>
</html>