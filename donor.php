<?php
session_start();
require 'db.php';
require_once 'helpers.php';
require_once 'status.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";
$isError = false;

if (isset($_GET["ok"])) {
    $message = "Pledge recorded. Thank you for your support!";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ngoID  = (int)($_POST["ngoID"] ?? 0);
    $amount = (float)($_POST["amount"] ?? 0);

    if ($ngoID <= 0 || $amount < 1 || $amount > 1000000) {
        $message = "Please pick an organisation and enter an amount from R1 to R1 000 000.";
        $isError = true;
    } else {
        // Only approved organisations can receive pledges
        $chk = $conn->prepare("SELECT userID, org_name FROM ngo_profiles WHERE ngoID = ? AND status = 'approved'");
        $chk->bind_param("i", $ngoID);
        $chk->execute();
        $ngo = $chk->get_result()->fetch_assoc();

        if (!$ngo) {
            $message = "That organisation is not available for pledges.";
            $isError = true;
        } else {
            $stmt = $conn->prepare("INSERT INTO donor_pledges (userID, ngoID, amount) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $userID, $ngoID, $amount);
            if ($stmt->execute()) {
                $amt = "R" . number_format($amount, 2);
                log_action($conn, $userID, "Pledge made", $amt . " to " . $ngo["org_name"]);
                notify($conn, $ngo["userID"], "New pledge received", $_SESSION["name"] . " pledged " . $amt . " to " . $ngo["org_name"] . ".");
                header("Location: donor.php?ok=1");
                exit;
            } else {
                $message = "Something went wrong. Please try again.";
                $isError = true;
            }
        }
    }
}

$ngos = $conn->query("SELECT ngoID, org_name, area FROM ngo_profiles WHERE status = 'approved' ORDER BY org_name");

$stmt = $conn->prepare("
    SELECT p.amount, p.date_pledged, p.status, n.org_name,
           (SELECT COUNT(*) FROM match_history m WHERE m.ngoID = n.ngoID) AS people_matched
    FROM donor_pledges p
    JOIN ngo_profiles n ON p.ngoID = n.ngoID
    WHERE p.userID = ?
    ORDER BY p.date_pledged DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$pledges = $stmt->get_result();

$sum = $conn->prepare("SELECT COUNT(*), COALESCE(SUM(amount),0), COUNT(DISTINCT ngoID) FROM donor_pledges WHERE userID = ?");
$sum->bind_param("i", $userID);
$sum->execute();
$sum->bind_result($pCount, $pTotal, $pOrgs);
$pCount = 0; $pTotal = 0; $pOrgs = 0;
$sum->fetch();
$sum->close();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Donor - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <h2>Pledge support</h2>

  <?php if ($pCount > 0) { ?>
    <div class="card">
      You have made <strong><?php echo $pCount; ?></strong> pledge(s) totalling
      <strong>R<?php echo number_format($pTotal, 2); ?></strong>
      to <strong><?php echo $pOrgs; ?></strong> organisation(s).
    </div>
  <?php } ?>

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
          $sel = ((int)($_POST["ngoID"] ?? 0) === (int)$n["ngoID"]) ? "selected" : "";
          echo "<option value='{$n['ngoID']}' $sel>" . htmlspecialchars($n['org_name']) . " (" . htmlspecialchars($n['area']) . ")</option>";
        } ?>
      </select>

      <label>Pledge amount (R)</label>
      <input type="number" name="amount" min="1" max="1000000" step="0.01" required
             value="<?php echo $isError ? htmlspecialchars($_POST["amount"] ?? "") : ""; ?>">

      <button type="submit">Pledge</button>
    </form>
    <p class="muted" style="margin-bottom:0"><small>Pledges are recorded only. No payment is processed in this prototype.</small></p>
  </div>

  <h3>Your pledges and their impact</h3>
  <?php if ($pledges->num_rows === 0) echo "<p>No pledges yet.</p>"; ?>
  <?php while ($p = $pledges->fetch_assoc()) { ?>
    <div class="card">
      <strong><?php echo htmlspecialchars($p["org_name"]); ?></strong>
      &middot; R<?php echo number_format($p["amount"], 2); ?>
      &nbsp;<?php echo status_badge($p["status"]); ?><br>
      Pledged on <?php echo date("d M Y", strtotime($p["date_pledged"])); ?><br>
      Impact: this organisation has been matched with <strong><?php echo $p["people_matched"]; ?></strong> support request(s).
    </div>
  <?php } ?>

  <p><a href="dashboard.php">Back to dashboard</a></p>
<?php include 'footer.php'; ?>
</body>
</html>