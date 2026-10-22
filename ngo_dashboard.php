<?php
session_start();
require 'db.php';
require 'lookups.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ngo_admin") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];

$q = $conn->prepare("SELECT ngoID, org_name FROM ngo_profiles WHERE userID = ?");
$q->bind_param("i", $userID);
$q->execute();
$ngo = $q->get_result()->fetch_assoc();

if (!$ngo) {
    header("Location: ngo_profile.php");
    exit;
}
$ngoID = $ngo["ngoID"];

// Incoming matched requests
$stmt = $conn->prepare("
    SELECT r.description, r.category, r.area, r.date_created, m.match_score
    FROM match_history m
    JOIN beneficiary_requests r ON m.requestID = r.requestID
    WHERE m.ngoID = ?
    ORDER BY m.date_matched DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$matches = $stmt->get_result();

// Pledges
$stmt = $conn->prepare("
    SELECT p.amount, p.date_pledged, u.name
    FROM donor_pledges p
    JOIN users u ON p.userID = u.userID
    WHERE p.ngoID = ?
    ORDER BY p.date_pledged DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$pledges = $stmt->get_result();

// Volunteer sign-ups
$stmt = $conn->prepare("
    SELECT s.task, s.status, u.name
    FROM volunteer_signups s
    JOIN users u ON s.userID = u.userID
    WHERE s.ngoID = ?
    ORDER BY s.signupID DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$signups = $stmt->get_result();

$total = $conn->prepare("SELECT COALESCE(SUM(amount),0) AS t FROM donor_pledges WHERE ngoID = ?");
$total->bind_param("i", $ngoID);
$total->execute();
$totalPledged = $total->get_result()->fetch_assoc()["t"];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NGO Dashboard - Ubuntu Care</title>
 <link rel="stylesheet" href="style.css">
</head>
<body>
   <?php include 'navbar.php'; ?>
  <h2><?php echo htmlspecialchars($ngo["org_name"]); ?> - Dashboard</h2>

  <div class="stats">
    <div class="stat"><strong><?php echo $matches->num_rows; ?></strong>Matched requests</div>
    <div class="stat"><strong>R<?php echo number_format($totalPledged, 2); ?></strong>Pledged</div>
    <div class="stat"><strong><?php echo $signups->num_rows; ?></strong>Volunteers</div>
  </div>

  <h3>Incoming matched requests</h3>
  <?php if ($matches->num_rows === 0) echo "<p>No matched requests yet.</p>"; ?>
  <?php while ($m = $matches->fetch_assoc()) { ?>
    <div class="card">
      <span class="score"><?php echo $m["match_score"]; ?>%</span>
      <?php echo htmlspecialchars($m["description"]); ?><br>
      <small><?php echo htmlspecialchars($CATEGORIES[$m["category"]] ?? "Uncategorised"); ?> &middot;
      <?php echo htmlspecialchars($m["area"]); ?> &middot; <?php echo $m["date_created"]; ?></small>
    </div>
  <?php } ?>

  <h3>Donor pledges</h3>
  <?php if ($pledges->num_rows === 0) echo "<p>No pledges yet.</p>"; ?>
  <?php while ($p = $pledges->fetch_assoc()) { ?>
    <div class="card"><?php echo htmlspecialchars($p["name"]); ?> pledged
      <strong>R<?php echo number_format($p["amount"], 2); ?></strong> on <?php echo $p["date_pledged"]; ?></div>
  <?php } ?>

  <h3>Volunteer sign-ups</h3>
  <?php if ($signups->num_rows === 0) echo "<p>No volunteers yet.</p>"; ?>
  <?php while ($s = $signups->fetch_assoc()) { ?>
    <div class="card"><?php echo htmlspecialchars($s["name"]); ?> - <strong><?php echo htmlspecialchars($s["task"]); ?></strong>
      (<?php echo htmlspecialchars($s["status"]); ?>)</div>
  <?php } ?>

  <p><a href="ngo_profile.php">Edit profile</a> | <a href="dashboard.php">Back to dashboard</a></p>
  <?php include 'footer.php'; ?>
</body>
</html>