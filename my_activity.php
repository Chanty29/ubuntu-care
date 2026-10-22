<?php
session_start();
require 'db.php';
require 'status.php';
require 'lookups.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}
$role = $_SESSION["role"];
$userID = $_SESSION["user_id"];
if ($role === "ngo_admin") {
    header("Location: ngo_manage.php");
    exit;
}

$requests = [];
$items = [];
if ($role === "beneficiary") {
    $stmt = $conn->prepare("SELECT r.requestID, r.description, r.category, r.area, r.date_created, m.status, m.match_score, n.org_name
        FROM beneficiary_requests r
        LEFT JOIN match_history m ON m.requestID = r.requestID
        LEFT JOIN ngo_profiles n ON n.ngoID = m.ngoID
        WHERE r.userID = ?
        ORDER BY r.date_created DESC, m.match_score DESC");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $id = $row["requestID"];
        if (!isset($requests[$id])) $requests[$id] = ["info" => $row, "matches" => []];
        if ($row["org_name"]) $requests[$id]["matches"][] = $row;
    }
} elseif ($role === "donor") {
    $stmt = $conn->prepare("SELECT p.amount, p.status, p.date_pledged, n.org_name FROM donor_pledges p JOIN ngo_profiles n ON p.ngoID = n.ngoID WHERE p.userID = ? ORDER BY p.date_pledged DESC");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $stmt = $conn->prepare("SELECT s.task, s.status, n.org_name FROM volunteer_signups s JOIN ngo_profiles n ON s.ngoID = n.ngoID WHERE s.userID = ? ORDER BY s.signupID DESC");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My activity - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
  <h2>My activity</h2>

  <?php if ($role === "beneficiary") { ?>
    <?php if (!$requests) echo "<p>No requests yet. <a href='request.php'>Make your first request</a>.</p>"; ?>
    <?php foreach ($requests as $r) { $i = $r["info"]; ?>
      <div class="card">
        <strong><?php echo htmlspecialchars($i["description"]); ?></strong><br>
        <span class="muted"><?php echo htmlspecialchars($CATEGORIES[$i["category"]] ?? "Uncategorised"); ?> &middot; <?php echo htmlspecialchars($i["area"]); ?> &middot; <?php echo $i["date_created"]; ?></span>
        <?php foreach ($r["matches"] as $m) { ?>
          <div style="margin-top:14px;padding-top:12px;border-top:1px solid #E2E8F0">
            <div class="row"><span><?php echo htmlspecialchars($m["org_name"]); ?></span><?php echo status_badge($m["status"]); ?></div>
            <?php echo status_track($m["status"]); ?>
          </div>
        <?php } ?>
      </div>
    <?php } ?>

  <?php } elseif ($role === "donor") { ?>
    <?php if (!$items) echo "<p>No pledges yet. <a href='donor.php'>Make a pledge</a>.</p>"; ?>
    <?php foreach ($items as $p) { ?>
      <div class="card row">
        <div><strong><?php echo htmlspecialchars($p["org_name"]); ?></strong> &middot; R<?php echo number_format($p["amount"], 2); ?><br>
        <span class="muted">Pledged on <?php echo $p["date_pledged"]; ?></span></div>
        <?php echo status_badge($p["status"]); ?>
      </div>
    <?php } ?>

  <?php } else { ?>
    <?php if (!$items) echo "<p>No sign-ups yet. <a href='volunteer.php'>Find a task</a>.</p>"; ?>
    <?php foreach ($items as $s) { ?>
      <div class="card row">
        <div><strong><?php echo htmlspecialchars($s["task"]); ?></strong> at <?php echo htmlspecialchars($s["org_name"]); ?></div>
        <?php echo status_badge($s["status"]); ?>
      </div>
    <?php } ?>
  <?php } ?>
<?php include 'footer.php'; ?>
</body>
</html>