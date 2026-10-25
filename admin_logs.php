<?php
session_start();
require 'db.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "logs";
$rows = $conn->query("SELECT l.action, l.details, l.created_at, u.name, u.email FROM activity_log l LEFT JOIN users u ON l.userID = u.userID ORDER BY l.logID DESC LIMIT 100");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Activity log - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; include 'admin_side.php'; ?>
  <h2 style="margin-top:0">Activity log</h2>
  <p class="muted">Logins, registrations, approvals and status updates (latest 100).</p>
  <div class="scroll"><table>
    <tr><th>When</th><th>User</th><th>Action</th><th>Details</th></tr>
    <?php if ($rows->num_rows === 0) echo "<tr><td colspan='4'>No activity yet.</td></tr>"; ?>
    <?php while ($l = $rows->fetch_assoc()) { ?>
      <tr>
        <td><?php echo $l["created_at"]; ?></td>
        <td><?php echo htmlspecialchars($l["name"] ?? "System"); ?><br><span class="muted"><?php echo htmlspecialchars($l["email"] ?? ""); ?></span></td>
        <td><strong><?php echo htmlspecialchars($l["action"]); ?></strong></td>
        <td><?php echo htmlspecialchars($l["details"]); ?></td>
      </tr>
    <?php } ?>
  </table></div>
</section></div>
<?php include 'footer.php'; ?>
</body>
</html>