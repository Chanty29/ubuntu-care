<?php
session_start();
require 'db.php';
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT title, body, is_read, created_at FROM notifications WHERE userID = ? ORDER BY notifID DESC LIMIT 50");
$stmt->bind_param("i", $uid);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$mark = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE userID = ?");
$mark->bind_param("i", $uid);
$mark->execute();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Notifications - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <h2>Notifications</h2>
  <?php if (!$items) echo "<p class='muted'>You have no notifications yet.</p>"; ?>
  <?php foreach ($items as $n) { ?>
    <div class="card <?php echo $n["is_read"] ? '' : 'unread'; ?>">
      <div class="row">
        <strong><?php echo htmlspecialchars($n["title"]); ?></strong>
        <?php if (!$n["is_read"]) echo "<span class='pill'>New</span>"; ?>
      </div>
      <div><?php echo htmlspecialchars($n["body"]); ?></div>
      <span class="muted"><?php echo $n["created_at"]; ?></span>
    </div>
  <?php } ?>
<?php include 'footer.php'; ?>
</body>
</html>