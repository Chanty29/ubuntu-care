<?php
session_start();
require 'db.php';
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$role = $_SESSION["role"];
$userID = $_SESSION["user_id"];

$ACTIONS = [
    "beneficiary" => [
        ["request.php", "Request support", "Describe what you need and get matched to nearby organisations."],
        ["my_activity.php", "Track my requests", "See the status of every request you have made."],
    ],
    "ngo_admin" => [
        ["ngo_profile.php", "Organisation profile", "Create or edit your category and area."],
        ["ngo_dashboard.php", "Overview", "Matches, pledges and volunteers at a glance."],
        ["ngo_manage.php", "Manage statuses", "Update requests, confirm pledges and volunteers."],
    ],
    "donor" => [
        ["donor.php", "Pledge support", "Choose an organisation and pledge an amount."],
        ["my_activity.php", "My pledges", "See which pledges have been received."],
    ],
    "volunteer" => [
        ["volunteer.php", "Find a task", "Sign up for a volunteer task."],
        ["my_activity.php", "My sign-ups", "See whether your sign-ups are confirmed."],
    ],
];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
  <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?></h2>
  <p class="muted">Signed in as <strong><?php echo htmlspecialchars(str_replace('_', ' ', $role)); ?></strong></p>
  <div class="grid">
    <?php foreach ($ACTIONS[$role] as $a) { ?>
      <a class="card" href="<?php echo $a[0]; ?>">
        <h3><?php echo htmlspecialchars($a[1]); ?></h3>
        <span class="muted"><?php echo htmlspecialchars($a[2]); ?></span>
      </a>
    <?php } ?>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>