<?php
session_start();
date_default_timezone_set("Africa/Johannesburg");
require 'db.php';
require_once 'helpers.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "reports";

$REPORTS = [
  "users" => [
    "title" => "Users by role",
    "about" => "How many people are registered in each role.",
    "head"  => ["Role", "Registered users"],
    "sql"   => "SELECT r.role_name, COUNT(u.userID) AS total
                FROM roles r LEFT JOIN users u ON u.roleID = r.roleID
                GROUP BY r.roleID, r.role_name ORDER BY total DESC",
  ],
  "ngos" => [
    "title" => "NGO register",
    "about" => "Every organisation, its focus area and approval status.",
    "head"  => ["Organisation", "Category", "Area", "Status"],
    "sql"   => "SELECT org_name, category, area, status
                FROM ngo_profiles ORDER BY org_name",
  ],
  "requests" => [
    "title" => "Help requests by category and area",
    "about" => "Where the need is: requests grouped by category and area.",
    "head"  => ["Category", "Area", "Requests"],
    "sql"   => "SELECT category, area, COUNT(*) AS total
                FROM beneficiary_requests
                GROUP BY category, area ORDER BY total DESC, category",
  ],
  "donations" => [
    "title" => "Donations by NGO",
    "about" => "Number of pledges and total amount pledged per organisation (rand).",
    "head"  => ["Organisation", "Pledges", "Total pledged (R)"],
    "sql"   => "SELECT n.org_name, COUNT(p.pledgeID) AS pledges,
                       COALESCE(SUM(p.amount), 0) AS total
                FROM ngo_profiles n LEFT JOIN donor_pledges p ON p.ngoID = n.ngoID
                GROUP BY n.ngoID, n.org_name ORDER BY total DESC",
  ],
  "volunteers" => [
    "title" => "Volunteer sign-ups by NGO",
    "about" => "Volunteer sign-ups per organisation and how many are confirmed or completed.",
    "head"  => ["Organisation", "Sign-ups", "Confirmed", "Completed"],
    "sql"   => "SELECT n.org_name, COUNT(v.signupID) AS signups,
                       COALESCE(SUM(v.status = 'confirmed'), 0) AS confirmed,
                       COALESCE(SUM(v.status = 'completed'), 0) AS completed
                FROM ngo_profiles n LEFT JOIN volunteer_signups v ON v.ngoID = n.ngoID
                GROUP BY n.ngoID, n.org_name ORDER BY signups DESC",
  ],
];

$key = $_GET["r"] ?? "users";
if (!isset($REPORTS[$key])) $key = "users";
$rep = $REPORTS[$key];

$rows = [];
$res = $conn->query($rep["sql"]);
if ($res) while ($r = $res->fetch_row()) $rows[] = $r;

// CSV download
if (isset($_GET["csv"])) {
    log_action($conn, $_SESSION["user_id"], "Report exported (CSV)", $rep["title"]);
    header("Content-Type: text/csv; charset=utf-8");
    header('Content-Disposition: attachment; filename="ubuntu_care_' . $key . '_' . date("Y-m-d") . '.csv"');
    $out = fopen("php://output", "w");
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, $rep["head"]);
    foreach ($rows as $r) fputcsv($out, $r);
    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reports - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .rtabs{display:flex;flex-wrap:wrap;gap:8px;margin:0 0 16px}
    .rtabs a{padding:8px 14px;border-radius:999px;border:1px solid #E2E8F0;background:#fff;color:#0B2447;text-decoration:none;font-size:14px;font-weight:600}
    .rtabs a.on{background:#0057B8;border-color:#0057B8;color:#fff}
    .printhead{display:none}
    @media print{
      nav,.navbar,.rtabs,.actions,.side,.sidebar,.foot,header{display:none !important}
      .printhead{display:block;margin-bottom:12px}
      body{background:#fff}
    }
  </style>
</head>
<body>
<?php include 'navbar.php'; include 'admin_side.php'; ?>
  <h2 style="margin-top:0">Reports</h2>

  <div class="rtabs">
    <?php foreach ($REPORTS as $k => $r) { ?>
      <a href="admin_reports.php?r=<?php echo $k; ?>" class="<?php echo $k === $key ? 'on' : ''; ?>"><?php echo htmlspecialchars($r["title"]); ?></a>
    <?php } ?>
  </div>

  <div class="printhead">
    <h2 style="margin:0">Ubuntu Care: <?php echo htmlspecialchars($rep["title"]); ?></h2>
    <div>Generated <?php echo date("d M Y, H:i"); ?></div>
  </div>

  <div class="card">
    <h3 style="margin-top:0"><?php echo htmlspecialchars($rep["title"]); ?></h3>
    <p class="muted"><?php echo htmlspecialchars($rep["about"]); ?> &middot; <?php echo count($rows); ?> row(s)</p>
    <div class="actions">
      <button class="sm" type="button" onclick="window.print()">Print / save as PDF</button>
      <a class="sm" href="admin_reports.php?r=<?php echo $key; ?>&csv=1" style="display:inline-block;padding:8px 14px;border-radius:8px;background:#0B2447;color:#fff;text-decoration:none;font-weight:600;font-size:14px">Download CSV (Excel)</a>
    </div>
  </div>

  <div class="scroll"><table>
    <tr><?php foreach ($rep["head"] as $h) echo "<th>" . htmlspecialchars($h) . "</th>"; ?></tr>
    <?php if (!$rows) echo "<tr><td colspan='" . count($rep["head"]) . "'>No data yet.</td></tr>"; ?>
    <?php foreach ($rows as $r) { echo "<tr>"; foreach ($r as $c) echo "<td>" . htmlspecialchars((string)$c) . "</td>"; echo "</tr>"; } ?>
  </table></div>
</section></div>
<?php include 'footer.php'; ?>
</body>
</html>