<?php
session_start();
require 'db.php';
require 'lookups.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ngo_admin") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $org  = trim($_POST["org_name"]);
    $cat  = $_POST["category"];
    $area = $_POST["area"];

    if ($org === "" || !isset($CATEGORIES[$cat]) || !isset($AREAS[$area])) {
        $message = "Please fill in all fields.";
    } else {
        $lat = $AREAS[$area][0];
        $lng = $AREAS[$area][1];

        $check = $conn->prepare("SELECT ngoID FROM ngo_profiles WHERE userID = ?");
        $check->bind_param("i", $userID);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            $stmt = $conn->prepare("UPDATE ngo_profiles SET org_name=?, category=?, area=?, latitude=?, longitude=? WHERE ngoID=?");
            $stmt->bind_param("sssddi", $org, $cat, $area, $lat, $lng, $existing["ngoID"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO ngo_profiles (userID, org_name, category, area, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssdd", $userID, $org, $cat, $area, $lat, $lng);
        }
        $message = $stmt->execute() ? "Profile saved." : "Something went wrong.";
    }
}

$q = $conn->prepare("SELECT org_name, category, area FROM ngo_profiles WHERE userID = ?");
$q->bind_param("i", $userID);
$q->execute();
$profile = $q->get_result()->fetch_assoc();
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
  <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
  <form method="post">
    <label>Organisation name</label>
    <input type="text" name="org_name" required value="<?php echo htmlspecialchars($profile["org_name"] ?? ""); ?>">

    <label>Main service category</label>
    <select name="category">
      <?php foreach ($CATEGORIES as $key => $label) {
        $sel = (($profile["category"] ?? "") === $key) ? "selected" : "";
        echo "<option value='$key' $sel>$label</option>";
      } ?>
    </select>

    <label>Area you operate in</label>
    <select name="area">
      <?php foreach ($AREAS as $name => $coords) {
        $sel = (($profile["area"] ?? "") === $name) ? "selected" : "";
        echo "<option value='$name' $sel>$name</option>";
      } ?>
    </select>

    <button type="submit">Save profile</button>
  </form>
  <p><a href="dashboard.php">Back to dashboard</a></p>
     <?php include 'footer.php'; ?>
</body>
</html>