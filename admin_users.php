<?php
session_start();
require 'db.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "users";
$q = trim($_GET["q"] ?? "");
$like = "%" . $q . "%";
$stmt = $conn->prepare("SELECT u.name, u.email, u.location, r.role_name FROM users u JOIN roles r ON u.roleID = r.roleID WHERE u.name LIKE ? OR u.email LIKE ? ORDER BY u.userID DESC LIMIT 200");
$stmt->bind_param("ss", $like, $like);
$stmt->execute();
$rows = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Users - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; include 'admin_side.php'; ?>
  <h2 style="margin-top:0">Users</h2>
  <form method="get" class="inline" style="margin-bottom:14px">
    <input type="text" name="q" placeholder="Search by name or email" value="<?php echo htmlspecialchars($q); ?>" style="margin:0;max-width:340px">
    <button class="sm" type="submit">Search</button>
  </form>
  <div class="scroll"><table>
    <tr><th>Name</th><th>Email</th><th>Area</th><th>Role</th></tr>
    <?php if ($rows->num_rows === 0) echo "<tr><td colspan='4'>No users found.</td></tr>"; ?>
    <?php while ($u = $rows->fetch_assoc()) { ?>
      <tr>
        <td><?php echo htmlspecialchars($u["name"]); ?></td>
        <td><?php echo htmlspecialchars($u["email"]); ?></td>
        <td><?php echo htmlspecialchars($u["location"]); ?></td>
        <td><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $u["role_name"]))); ?></td>
      </tr>
    <?php } ?>
  </table></div>
</section></div>
<?php include 'footer.php'; ?>
</body>
</html>