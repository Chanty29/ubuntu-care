<?php
session_start();
require 'db.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "donor") {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $ngoID = (int)$_POST["ngoID"];
    $amount = (float)$_POST["amount"];

    if ($ngoID <= 0 || $amount <= 0) {
        $message = "Please pick an organisation and enter an amount above 0.";
    } else {
        $stmt = $conn->prepare("INSERT INTO donor_pledges (userID, ngoID, amount) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $userID, $ngoID, $amount);
        $message = $stmt->execute() ? "Pledge recorded. Thank you!" : "Something went wrong.";
    }
}

$ngos = $conn->query("SELECT ngoID, org_name, area FROM ngo_profiles WHERE status = 'approved' ORDER BY org_name");
$stmt = $conn->prepare("
    SELECT p.amount, p.date_pledged, n.org_name,
           (SELECT COUNT(*) FROM match_history m WHERE m.ngoID = n.ngoID) AS people_matched
    FROM donor_pledges p
    JOIN ngo_profiles n ON p.ngoID = n.ngoID
    WHERE p.userID = ?
    ORDER BY p.date_pledged DESC");
$stmt->bind_param("i", $userID);
$stmt->execute();
$pledges = $stmt->get_result();
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
  <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
  <form method="post">
    <label>Organisation</label>
    <select name="ngoID">
      <?php while ($n = $ngos->fetch_assoc()) {
        echo "<option value='{$n['ngoID']}'>" . htmlspecialchars($n['org_name']) . " (" . htmlspecialchars($n['area']) . ")</option>";
      } ?>
    </select>
    <label>Pledge amount (R)</label>
    <input type="number" name="amount" min="1" step="0.01" required>
    <button type="submit">Pledge</button>
  </form>
  <p><small>Pledges are recorded only. No payment is processed in this prototype.</small></p>

  <h3>Your pledges and their impact</h3>
  <?php if ($pledges->num_rows === 0) echo "<p>No pledges yet.</p>"; ?>
  <?php while ($p = $pledges->fetch_assoc()) { ?>
    <div class="card">
      <strong><?php echo htmlspecialchars($p["org_name"]); ?></strong> &middot; R<?php echo number_format($p["amount"], 2); ?><br>
      Pledged on <?php echo $p["date_pledged"]; ?><br>
      Impact: this organisation has been matched with <strong><?php echo $p["people_matched"]; ?></strong> support request(s).
    </div>
  <?php } ?>
  <p><a href="dashboard.php">Back to dashboard</a></p>
   <?php include 'footer.php'; ?>
</body>
</html>