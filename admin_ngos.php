<?php
session_start();
require 'db.php';
require 'lookups.php';
require 'helpers.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "ngos";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $decision = $_POST["decision"] ?? "";
    if ($id > 0 && in_array($decision, ["approved", "rejected"], true)) {
        $stmt = $conn->prepare("UPDATE ngo_profiles SET status = ? WHERE ngoID = ?");
        $stmt->bind_param("si", $decision, $id);
        $stmt->execute();

        $o = $conn->prepare("SELECT userID, org_name FROM ngo_profiles WHERE ngoID = ?");
        $o->bind_param("i", $id);
        $o->execute();
        $ngo = $o->get_result()->fetch_assoc();
        if ($ngo) {
            $body = ($decision === "approved")
                ? "You now appear in matching and can receive pledges and volunteers."
                : "Please review your details or contact the platform admin.";
            notify($conn, $ngo["userID"], "Your organisation was " . $decision, $body);
            log_action($conn, $_SESSION["user_id"], "NGO " . $decision, $ngo["org_name"]);
            $message = $ngo["org_name"] . " was " . $decision . ".";
        }
    }
}

$f = $_GET["s"] ?? "all";
if (!in_array($f, ["all", "pending", "approved", "rejected"], true)) $f = "all";
$sql = "SELECT n.ngoID, n.org_name, n.category, n.area, n.status, u.name, u.email FROM ngo_profiles n JOIN users u ON n.userID = u.userID";
if ($f !== "all") {
    $stmt = $conn->prepare($sql . " WHERE n.status = ? ORDER BY n.ngoID DESC");
    $stmt->bind_param("s", $f);
    $stmt->execute();
    $rows = $stmt->get_result();
} else {
    $rows = $conn->query($sql . " ORDER BY n.ngoID DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Organisations - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; include 'admin_side.php'; ?>
  <h2 style="margin-top:0">Organisations</h2>
  <?php if ($message) echo "<p class='ok'>" . htmlspecialchars($message) . "</p>"; ?>
  <div class="tabs">
    <?php foreach (["all" => "All", "pending" => "Pending", "approved" => "Approved", "rejected" => "Rejected"] as $k => $label) { ?>
      <a class="<?php echo $f === $k ? 'on' : ''; ?>" href="admin_ngos.php?s=<?php echo $k; ?>"><?php echo $label; ?></a>
    <?php } ?>
  </div>
  <div class="scroll"><table>
    <tr><th>Organisation</th><th>Owner</th><th>Category</th><th>Area</th><th>Status</th><th>Action</th></tr>
    <?php if ($rows->num_rows === 0) echo "<tr><td colspan='6'>No organisations here.</td></tr>"; ?>
    <?php while ($n = $rows->fetch_assoc()) { ?>
      <tr>
        <td><strong><?php echo htmlspecialchars($n["org_name"]); ?></strong></td>
        <td><?php echo htmlspecialchars($n["name"]); ?><br><span class="muted"><?php echo htmlspecialchars($n["email"]); ?></span></td>
        <td><?php echo htmlspecialchars($CATEGORIES[$n["category"]] ?? $n["category"]); ?></td>
        <td><?php echo htmlspecialchars($n["area"]); ?></td>
        <td><?php echo ngo_badge($n["status"]); ?></td>
        <td>
          <form method="post" class="inline">
            <input type="hidden" name="id" value="<?php echo $n["ngoID"]; ?>">
            <?php if ($n["status"] !== "approved") { ?><button class="sm" name="decision" value="approved">Approve</button><?php } ?>
            <?php if ($n["status"] !== "rejected") { ?><button class="sm" name="decision" value="rejected" style="background:#DC2626">Reject</button><?php } ?>
          </form>
        </td>
      </tr>
    <?php } ?>
  </table></div>
</section></div>
<?php include 'footer.php'; ?>
</body>
</html>