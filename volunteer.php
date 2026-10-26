<?php
session_start();
require 'db.php';
require_once 'helpers.php';
require_once 'status.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "volunteer") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";
$isError = false;

$TASKS = [
    "Food parcel packing",
    "Delivering meals",
    "Tutoring learners",
    "Admin and data capture",
    "Clinic support",
    "Fundraising event help",
];

if (isset($_GET["ok"])) {
    $message = "You are signed up. Thank you for volunteering!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ngoID = (int)($_POST["ngoID"] ?? 0);
    $task  = $_POST["task"] ?? "";

    if ($ngoID <= 0 || !in_array($task, $TASKS, true)) {
        $message = "Please pick an organisation and a task.";
        $isError = true;
    } else {
        // Organisation must be approved
        $chk = $conn->prepare("SELECT userID, org_name FROM ngo_profiles WHERE ngoID = ? AND status = 'approved'");
        $chk->bind_param("i", $ngoID);
        $chk->execute();
        $ngo = $chk->get_result()->fetch_assoc();

        if (!$ngo) {
            $message = "That organisation is not available for sign-ups.";
            $isError = true;
        } else {
            // No duplicate sign-up for the same organisation and task
            $dup = $conn->prepare("SELECT signupID FROM volunteer_signups WHERE userID = ? AND ngoID = ? AND task = ?");
            $dup->bind_param("iis", $userID, $ngoID, $task);
            $dup->execute();
            if ($dup->get_result()->fetch_assoc()) {
                $message = "You have already signed up for that task at this organisation.";
                $isError = true;
            } else {
                $stmt = $conn->prepare("INSERT INTO volunteer_signups (userID, ngoID, task) VALUES (?, ?, ?)");
                $stmt->bind_param("iis", $userID, $ngoID, $task);
                if ($stmt->execute()) {
                    log_action($conn, $userID, "Volunteer signed up", $task . " at " . $ngo["org_name"]);
                    notify($conn, $ngo["userID"], "New volunteer", $_SESSION["name"] . " signed up to help with: " . $task . ".");
                    header("Location: volunteer.php?ok=1");
                    exit;
                } else {
                    $message = "Something went wrong. Please try again.";
                    $isError = true;
                }
            }
        }
    }
}

$ngos = $conn->query("SELECT ngoID, org_name, area FROM ngo_profiles WHERE status = 'approved' ORDER BY org_name");

$stmt = $conn->prepare("
    SELECT s.task, s.status, n.org_name, n.area
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

  <?php if ($message) {
      $style = $isError ? "background:#FEE2E2;color:#991B1B;" : "background:#DCFCE7;color:#166534;";
      echo "<p style='$style padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>";
  } ?>

  <div class="panel">
    <form method="post">
      <label>Organisation</label>
      <select name="ngoID" required>
        <option value="">Choose an organisation</option>
        <?php while ($n = $ngos->fetch_assoc()) {
          $sel = ((int)($_POST["ngoID"] ?? 0) === (int)$n["ngoID"] && $isError) ? "selected" : "";
          echo "<option value='{$n['ngoID']}' $sel>" . htmlspecialchars($n['org_name']) . " (" . htmlspecialchars($n['area']) . ")</option>";
        } ?>
      </select>

      <label>Task</label>
      <select name="task" required>
        <option value="">Choose a task</option>
        <?php foreach ($TASKS as $t) {
          $sel = (($_POST["task"] ?? "") === $t && $isError) ? "selected" : "";
          echo "<option $sel>" . htmlspecialchars($t) . "</option>";
        } ?>
      </select>

      <button type="submit">Sign up</button>
    </form>
  </div>

  <h3>Your sign-ups</h3>
  <?php if ($signups->num_rows === 0) echo "<p>No sign-ups yet.</p>"; ?>
  <?php while ($s = $signups->fetch_assoc()) { ?>
    <div class="card">
      <strong><?php echo htmlspecialchars($s["task"]); ?></strong>
      at <?php echo htmlspecialchars($s["org_name"]); ?> (<?php echo htmlspecialchars($s["area"]); ?>)<br>
      <?php echo status_badge($s["status"]); ?>
    </div>
  <?php } ?>

  <p><a href="dashboard.php">Back to dashboard</a></p>
<?php include 'footer.php'; ?>
</body>
</html>