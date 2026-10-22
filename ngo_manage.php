<?php
session_start();
require 'db.php';
require 'status.php';
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

$OPTIONS = [
    "match"  => ["pending", "accepted", "in_progress", "completed", "declined"],
    "pledge" => ["pledged", "received"],
    "signup" => ["signed_up", "confirmed", "completed"],
];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kind = $_POST["kind"] ?? "";
    $id = (int)($_POST["id"] ?? 0);
    $status = $_POST["status"] ?? "";

    if (isset($OPTIONS[$kind]) && in_array($status, $OPTIONS[$kind], true)) {
        if ($kind === "match")  $stmt = $conn->prepare("UPDATE match_history SET status = ? WHERE matchID = ? AND ngoID = ?");
        if ($kind === "pledge") $stmt = $conn->prepare("UPDATE donor_pledges SET status = ? WHERE pledgeID = ? AND ngoID = ?");
        if ($kind === "signup") $stmt = $conn->prepare("UPDATE volunteer_signups SET status = ? WHERE signupID = ? AND ngoID = ?");
        $stmt->bind_param("sii", $status, $id, $ngoID);
        $stmt->execute();
        $message = "Status updated.";
    }
}

$stmt = $conn->prepare("SELECT m.matchID, m.status, m.match_score, r.description, r.area, r.date_created FROM match_history m JOIN beneficiary_requests r ON m.requestID = r.requestID WHERE m.ngoID = ? ORDER BY m.matchID DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$matches = $stmt->get_result();

$stmt = $conn->prepare("SELECT p.pledgeID, p.amount, p.status, p.date_pledged, u.name FROM donor_pledges p JOIN users u ON p.userID = u.userID WHERE p.ngoID = ? ORDER BY p.pledgeID DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$pledges = $stmt->get_result();

$stmt = $conn->prepare("SELECT s.signupID, s.task, s.status, u.name FROM volunteer_signups s JOIN users u ON s.userID = u.userID WHERE s.ngoID = ? ORDER BY s.signupID DESC");
$stmt->bind_param("i", $ngoID);
$stmt->execute();
$signups = $stmt->get_result();

function status_form($kind, $id, $current, $OPTIONS, $STATUS_LABELS) {
    $h = "<form method='post' class='inline'><input type='hidden' name='kind' value='$kind'><input type='hidden' name='id' value='$id'><select name='status' aria-label='Status'>";
    foreach ($OPTIONS[$kind] as $o) {
        $sel = ($o === $current) ? " selected" : "";
        $h .= "<option value='$o'$sel>" . htmlspecialchars($STATUS_LABELS[$o]) . "</option>";
    }
    return $h . "</select><button type='submit' class='sm'>Update</button></form>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
  <h2><?php echo htmlspecialchars($ngo["org_name"]); ?>: manage statuses</h2>
  <?php if ($message) echo "<p class='ok'>" . htmlspecialchars($message) . "</p>"; ?>

  <h3>Requests matched to you</h3>
  <?php if ($matches->num_rows === 0) echo "<p class='muted'>No matched requests yet.</p>"; ?>
  <?php while ($m = $matches->fetch_assoc()) { ?>
    <div class="card">
      <span class="score"><?php echo $m["match_score"]; ?>%</span>
      <?php echo htmlspecialchars($m["description"]); ?><br>
      <span class="muted"><?php echo htmlspecialchars($m["area"]); ?> &middot; <?php echo $m["date_created"]; ?></span>
      <div class="row" style="margin-top:10px"><?php echo status_badge($m["status"]); ?>
      <?php echo status_form("match", $m["matchID"], $m["status"], $OPTIONS, $STATUS_LABELS); ?></div>
    </div>
  <?php } ?>

  <h3>Donor pledges</h3>
  <?php if ($pledges->num_rows === 0) echo "<p class='muted'>No pledges yet.</p>"; ?>
  <?php while ($p = $pledges->fetch_assoc()) { ?>
    <div class="card row">
      <div><?php echo htmlspecialchars($p["name"]); ?> pledged <strong>R<?php echo number_format($p["amount"], 2); ?></strong><br>
      <span class="muted"><?php echo $p["date_pledged"]; ?></span></div>
      <?php echo status_form("pledge", $p["pledgeID"], $p["status"], $OPTIONS, $STATUS_LABELS); ?>
    </div>
  <?php } ?>

  <h3>Volunteers</h3>
  <?php if ($signups->num_rows === 0) echo "<p class='muted'>No volunteers yet.</p>"; ?>
  <?php while ($s = $signups->fetch_assoc()) { ?>
    <div class="card row">
      <div><?php echo htmlspecialchars($s["name"]); ?>: <strong><?php echo htmlspecialchars($s["task"]); ?></strong></div>
      <?php echo status_form("signup", $s["signupID"], $s["status"], $OPTIONS, $STATUS_LABELS); ?>
    </div>
  <?php } ?>
<?php include 'footer.php'; ?>
</body>
</html>