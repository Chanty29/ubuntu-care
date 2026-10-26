<?php
session_start();
require 'db.php';
require 'lookups.php';
require_once 'helpers.php';
require_once 'status.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "ngo_admin") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];

$q = $conn->prepare("SELECT ngoID, org_name, status FROM ngo_profiles WHERE userID = ?");
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
    SELECT r.description, r.category, r.area, r.date_created, m.match_score, m.status
    FROM match_history m
    JOIN beneficiary_requests r ON m.requestID = r.requestID
    WHERE m.ngoID = ?
    ORDER BY m.date_matched DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$matches = $stmt->get_result();

// Pledges
$stmt = $conn->prepare("
    SELECT p.amount, p.date_pledged, p.status, u.name
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

$nMatches  = $matches->num_rows;
$nPledges  = $pledges->num_rows;
$nSignups  = $signups->num_rows;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NGO Dashboard - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:14px;margin:16px 0 24px}
    .kpi{background:#fff;border:1px solid #E2E8F0;border-radius:14px;padding:16px}
    .kpi strong{display:block;font-size:26px;color:#0057B8;margin-bottom:2px}
    .kpi span{color:#475569;font-size:14px}
    .banner{background:#E0EDFB;color:#0B2447;border-radius:10px;padding:12px 16px;margin-bottom:16px}
  </style>
</head>
<body>
<?php include 'navbar.php'; ?>
  <h2 style="margin-bottom:6px"><?php echo htmlspecialchars($ngo["org_name"]); ?></h2>
  <p style="margin-top:0"><?php echo ngo_badge($ngo["status"]); ?></p>

  <?php if ($ngo["status"] === "pending") { ?>
    <div class="banner">Your organisation is waiting for admin approval. You will start receiving matched requests, pledges and volunteers once it is approved.</div>
  <?php } elseif ($ngo["status"] === "rejected") { ?>
    <div class="banner">Your application was not approved. Update your details on the <a href="ngo_profile.php">profile page</a>.</div>
  <?php } ?>

  <p>
    <a href="ngo_manage.php" style="display:inline-block;padding:10px 18px;border-radius:10px;background:#0057B8;color:#fff;text-decoration:none;font-weight:600">Manage requests</a>
    <a href="ngo_profile.php" style="margin-left:12px">Edit profile</a>
  </p>

  <div class="kpis">
    <div class="kpi"><strong><?php echo $nMatches; ?></strong><span>Matched requests</span></div>
    <div class="kpi"><strong>R<?php echo number_format($totalPledged, 2); ?></strong><span>Total pledged</span></div>
    <div class="kpi"><strong><?php echo $nSignups; ?></strong><span>Volunteers</span></div>
  </div>

  <h3>Incoming matched requests</h3>
  <?php if ($nMatches === 0) echo "<p>No matched requests yet.</p>"; ?>
  <?php while ($m = $matches->fetch_assoc()) { ?>
    <div class="card">
      <span class="score"><?php echo $m["match_score"]; ?>%</span>
      <?php echo htmlspecialchars($m["description"]); ?><br>
      <small><?php echo htmlspecialchars($CATEGORIES[$m["category"]] ?? "Uncategorised"); ?> &middot;
      <?php echo htmlspecialchars($m["area"]); ?> &middot;
      <?php echo date("d M Y", strtotime($m["date_created"])); ?></small>
      &nbsp;<?php echo status_badge($m["status"]); ?>
    </div>
  <?php } ?>

  <h3>Donor pledges</h3>
  <?php if ($nPledges === 0) echo "<p>No pledges yet.</p>"; ?>
  <?php while ($p = $pledges->fetch_assoc()) { ?>
    <div class="card">
      <strong>R<?php echo number_format($p["amount"], 2); ?></strong>
      from <?php echo htmlspecialchars($p["name"]); ?>
      &middot; <?php echo date("d M Y", strtotime($p["date_pledged"])); ?>
      &nbsp;<?php echo status_badge($p["status"]); ?>
    </div>
  <?php } ?>

  <h3>Volunteers</h3>
  <?php if ($nSignups === 0) echo "<p>No volunteers yet.</p>"; ?>
  <?php while ($s = $signups->fetch_assoc()) { ?>
    <div class="card">
      <strong><?php echo htmlspecialchars($s["name"]); ?></strong>
      &middot; <?php echo htmlspecialchars($s["task"]); ?>
      &nbsp;<?php echo status_badge($s["status"]); ?>
    </div>
  <?php } ?>

  <p><a href="dashboard.php">Back to dashboard</a></p>
<?php include 'footer.php'; ?>
</body>
</html>