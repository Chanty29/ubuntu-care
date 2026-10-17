<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$role = $_SESSION["role"];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Ubuntu Care</title>
  <style>body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; }</style>
</head>
<body>
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?></h2>
  <p>You are logged in as: <strong><?php echo htmlspecialchars(str_replace('_', ' ', $role)); ?></strong></p>

  <?php if ($role === "beneficiary") { ?>
  <p><a href="request.php">Request support</a></p>
  <?php } elseif ($role === "ngo_admin") { ?>
 <p><a href="ngo_profile.php">Create / edit your organisation profile</a></p>
  <?php } elseif ($role === "donor") { ?>
    <p>Donor area (pledges coming next).</p>
  <?php } elseif ($role === "volunteer") { ?>
    <p>Volunteer area (task sign-ups coming next).</p>
  <?php } ?>

  <p><a href="logout.php">Log out</a></p>
</body>
</html>