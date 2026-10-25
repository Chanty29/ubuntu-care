<?php
session_start();
require 'db.php';
require 'lookups.php';
require 'helpers.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "dashboard";

function count_of($conn, $sql) { return $conn->query($sql)->fetch_assoc()["c"]; }
$totalUsers = count_of($conn, "SELECT COUNT(*) c FROM users");
$pending    = count_of($conn, "SELECT COUNT(*) c FROM ngo_profiles WHERE status = 'pending'");
$approved   = count_of($conn, "SELECT COUNT(*) c FROM ngo_profiles WHERE status = 'approved'");
$requests   = count_of($conn, "SELECT COUNT(*) c FROM beneficiary_requests");
$matches    = count_of($conn, "SELECT COUNT(*) c FROM match_history");
$volunteers = count_of($conn, "SELECT COUNT(*) c FROM volunteer_signups");
$pledged    = count_of($conn, "SELECT COALESCE(SUM(amount),0) c FROM donor_pledges");

$byRole = $conn->query("SELECT r.role_name, COUNT(u.userID) c FROM roles r LEFT JOIN users u ON u.roleID = r.roleID GROUP BY r.roleID ORDER BY c DESC")->fetch_all(MYSQLI_ASSOC);
$maxRole = max(1, max(array_column($byRole, "c")));

$pendingList = $conn->query("SELECT n.ngoID, n.org_name, n.category, n.area, u.name, u.email FROM ngo_profiles n JOIN users u ON n.userID = u.userID WHERE n.status = 'pending' ORDER BY n.ngoID DESC LIMIT 5");
$recent = $conn->query("SELECT l.action, l.details, l.created_at, u.name FROM activity_log l LEFT JOIN users u ON l.userID = u.userID ORDER BY l.logID DESC LIMIT 8");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; include 'admin_side.php'; ?>
  <h2 style="margin-top:0">Dashboard overview</h2>
  <div class="stats">
    <div class="stat"><strong><?php echo $totalUsers; ?></strong>Total users</div>
    <div class="stat"><strong style="color:#EA580C"><?php echo $pending; ?></strong>NGOs awaiting review</div>
    <div class="stat"><strong><?php echo $approved; ?></strong>Approved NGOs</div>
    <div class="stat"><strong><?php echo $requests; ?></strong>Requests made</div>
    <div class="stat"><strong><?php echo $matches; ?></strong>Matches made</div>
    <div class="stat"><strong><?php echo $volunteers; ?></strong>Volunteer sign-ups</div>
    <div class="stat"><strong>R<?php echo number_format($pledged); ?></strong>Pledged</div>
  </div>

  <div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(300px,1fr))">
    <div class="card">
      <h3>Organisations awaiting review</h3>
      <?php if ($pendingList->num_rows === 0) echo "<p class='muted'>Nothing waiting. All caught up.</p>"; ?>
      <?php while ($n = $pendingList->fetch_assoc()) { ?>
        <div style="padding:12px 0;border-top:1px solid #E2E8F0">
          <strong><?php echo htmlspecialchars($n["org_name"]); ?></strong><br>
          <span class="muted"><?php echo htmlspecialchars($CATEGORIES[$n["category"]] ?? $n["category"]); ?> &middot; <?php echo htmlspecialchars($n["area"]); ?> &middot; <?php echo htmlspecialchars($n["email"]); ?></span>
          <form method="post" action="admin_ngos.php" class="inline" style="margin-top:8px">
            <input type="hidden" name="id" value="<?php echo $n["ngoID"]; ?>">
            <button class="sm" name="decision" value="approved">Approve</button>
            <button class="sm" name="decision" value="rejected" style="background:#DC2626">Reject</button>
          </form>
        </div>
      <?php } ?>
    </div>

    <div class="card">
      <h3>Users by role</h3>
      <?php foreach ($byRole as $r) { ?>
        <div style="margin:12px 0">
          <div class="row"><span><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $r["role_name"]))); ?></span><strong><?php echo $r["c"]; ?></strong></div>
          <div class="bar" style="width:<?php echo round(100 * $r["c"] / $maxRole); ?>%"></div>
        </div>
      <?php } ?>
    </div>
  </div>

  <h3>Recent activity</h3>
  <div class="scroll"><table>
    <tr><th>User</th><th>Action</th><th>Details</th><th>When</th></tr>
    <?php if ($recent->num_rows === 0) echo "<tr><td colspan='4'>No activity yet.</td></tr>"; ?>
    <?php while ($l = $recent->fetch_assoc()) { ?>
      <tr><td><?php echo htmlspecialchars($l["name"] ?? "System"); ?></td><td><?php echo htmlspecialchars($l["action"]); ?></td><td><?php echo htmlspecialchars($l["details"]); ?></td><td><?php echo $l["created_at"]; ?></td></tr>
    <?php } ?>
  </table></div>
</section></div>
<?php include 'footer.php'; ?>
</body>
</html>