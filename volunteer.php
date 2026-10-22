<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "volunteer") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";

$TASKS = [
    "Food parcel packing",
    "Delivering meals",
    "Tutoring learners",
    "Admin and data capture",
    "Clinic support",
    "Fundraising event help",
];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ngoID = (int)$_POST["ngoID"];
    $task = $_POST["task"];

    if ($ngoID <= 0 || !in_array($task, $TASKS)) {
        $message = "Please pick an organisation and a task.";
    } else {
        $stmt = $conn->prepare("INSERT INTO volunteer_signups (userID, ngoID, task) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $userID, $ngoID, $task);
        $message = $stmt->execute() ? "You are signed up. Thank you!" : "Something went wrong.";
    }
}

$ngos = $conn->query("SELECT ngoID, org_name, area FROM ngo_profiles ORDER BY org_name");

$stmt = $conn->prepare("
    SELECT s.task, s.status, n.org_name
    FROM volunteer_signups s
    JOIN ngo_profiles n ON s.ngoID = n.ngoID
    WHERE s.userID = ?
    ORDER BY s.signupID DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$signups = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Volunteer - Ubuntu Care</title>
 <link rel="stylesheet" href="style.css">
</head>
<body>
 <?php $narrow = true; include 'navbar.php'; ?>
  <h2>Volunteer for a task</h2>
  <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
  <form method="post">
    <label>Organisation</label>
    <select name="ngoID">
      <?php while ($n = $ngos->fetch_assoc()) {
        echo "<option value='{$n['ngoID']}'>" . htmlspecialchars($n['org_name']) . " (" . htmlspecialchars($n['area']) . ")</option>";
      } ?>
    </select>
    <label>Task</label>
    <select name="task">
      <?php foreach ($TASKS as $t) echo "<option>" . htmlspecialchars($t) . "</option>"; ?>
    </select>
    <button type="submit">Sign up</button>
  </form>

  <h3>Your sign-ups</h3>
  <?php if ($signups->num_rows === 0) echo "<p>No sign-ups yet.</p>"; ?>
  <?php while ($s = $signups->fetch_assoc()) { ?>
    <div class="card">
      <strong><?php echo htmlspecialchars($s["task"]); ?></strong> at <?php echo htmlspecialchars($s["org_name"]); ?><br>
      Status: <?php echo htmlspecialchars($s["status"]); ?>
    </div>
  <?php } ?>
  <p><a href="dashboard.php">Back to dashboard</a></p>
  <?php include 'footer.php'; ?>
</body>
</html>