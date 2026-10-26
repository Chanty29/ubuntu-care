<?php
session_start();
require 'db.php';
require_once 'helpers.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "backup";
$dir = __DIR__ . "/backups";
if (!is_dir($dir)) mkdir($dir, 0775, true);
if (!file_exists($dir . "/.htaccess")) file_put_contents($dir . "/.htaccess", "Require all denied\n");

function make_dump($conn) {
    $skipData = ["remember_tokens", "password_resets"];
    $out = "-- Ubuntu Care database backup\n-- Created: " . date("Y-m-d H:i:s") . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";
    $tables = [];
    $res = $conn->query("SHOW TABLES");
    while ($row = $res->fetch_row()) $tables[] = $row[0];
    foreach ($tables as $t) {
        $create = $conn->query("SHOW CREATE TABLE `$t`")->fetch_row()[1];
        $out .= "DROP TABLE IF EXISTS `$t`;\n$create;\n\n";
        if (in_array($t, $skipData, true)) continue;
        $rows = $conn->query("SELECT * FROM `$t`");
        while ($r = $rows->fetch_assoc()) {
            $vals = array_map(function ($v) use ($conn) {
                return $v === null ? "NULL" : "'" . $conn->real_escape_string($v) . "'";
            }, array_values($r));
            $out .= "INSERT INTO `$t` VALUES (" . implode(",", $vals) . ");\n";
        }
        $out .= "\n";
    }
    return $out . "SET FOREIGN_KEY_CHECKS=1;\n";
}

// Download a saved backup
if (isset($_GET["file"])) {
    $f = basename($_GET["file"]);
    if (preg_match('/^ubuntu_care_backup_[0-9_-]+\.sql$/', $f) && file_exists("$dir/$f")) {
        header("Content-Type: application/sql");
        header('Content-Disposition: attachment; filename="' . $f . '"');
        header("Content-Length: " . filesize("$dir/$f"));
        readfile("$dir/$f");
        exit;
    }
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = "ubuntu_care_backup_" . date("Y-m-d_H-i-s") . ".sql";
    $sql = make_dump($conn);
    if (($_POST["action"] ?? "") === "download") {
        log_action($conn, $_SESSION["user_id"], "Database backup downloaded", $name);
        header("Content-Type: application/sql");
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header("Content-Length: " . strlen($sql));
        echo $sql;
        exit;
    }
    if (file_put_contents("$dir/$name", $sql) !== false) {
        log_action($conn, $_SESSION["user_id"], "Database backup saved", $name);
        $message = "Backup saved: " . $name;
    } else {
        $message = "Could not save the backup. Check folder permissions.";
    }
}

$files = glob($dir . "/ubuntu_care_backup_*.sql") ?: [];
rsort($files);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Backup - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; include 'admin_side.php'; ?>
  <h2 style="margin-top:0">System backup</h2>
  <?php if ($message) echo "<p class='ok'>" . htmlspecialchars($message) . "</p>"; ?>
  <div class="card">
    <p style="margin-top:0">A backup is a full copy of the database (users, organisations, requests, pledges, volunteers, logs) saved as a <strong>.sql</strong> file.</p>
    <form method="post" class="inline">
      <button class="sm" name="action" value="save">Create backup on server</button>
      <button class="sm" name="action" value="download" style="background:#0B2447">Download a backup now</button>
    </form>
    <p class="muted" style="margin-bottom:0">Sign-in tokens and reset links are not included. Backup files contain password hashes, so keep them private.</p>
  </div>

  <h3>Saved backups</h3>
  <div class="scroll"><table>
    <tr><th>File</th><th>Size</th><th>Created</th><th></th></tr>
    <?php if (!$files) echo "<tr><td colspan='4'>No backups saved yet.</td></tr>"; ?>
    <?php foreach ($files as $p) { $b = basename($p); ?>
      <tr>
        <td><?php echo htmlspecialchars($b); ?></td>
        <td><?php echo round(filesize($p) / 1024, 1); ?> KB</td>
        <td><?php echo date("Y-m-d H:i", filemtime($p)); ?></td>
        <td><a href="admin_backup.php?file=<?php echo urlencode($b); ?>">Download</a></td>
      </tr>
    <?php } ?>
  </table></div>

  <div class="card">
    <strong>How to restore</strong>
    <ol style="margin-bottom:0">
      <li>Open phpMyAdmin and click <strong>ubuntu_care</strong>.</li>
      <li>Click the <strong>Import</strong> tab, choose the .sql file, and click <strong>Go</strong>.</li>
    </ol>
  </div>
</section></div>
<?php include 'footer.php'; ?>
</body>
</html>