<?php
session_start();
require 'db.php';
require_once 'helpers.php';
require_once 'backup_lib.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}
$active = "backup";
$dir = backup_dir();

// Download a saved backup (works from any device where the admin is logged in)
if (isset($_GET["file"])) {
    $f = basename($_GET["file"]);
    if (preg_match('/^ubuntu_care_backup_[0-9_-]+(_auto|_manual)?\.sql$/', $f) && file_exists("$dir/$f")) {
        header("Content-Type: application/sql");
        header('Content-Disposition: attachment; filename="' . $f . '"');
        header("Content-Length: " . filesize("$dir/$f"));
        readfile("$dir/$f");
        exit;
    }
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (($_POST["action"] ?? "") === "download") {
        $name = "ubuntu_care_backup_" . date("Y-m-d_H-i-s") . ".sql";
        $sql = make_dump($conn);
        log_action($conn, $_SESSION["user_id"], "Database backup downloaded", $name);
        header("Content-Type: application/sql");
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header("Content-Length: " . strlen($sql));
        echo $sql;
        exit;
    }
    $name = save_backup($conn, "manual");
    if ($name) {
        log_action($conn, $_SESSION["user_id"], "Database backup saved", $name);
        $message = "Backup saved: " . $name;
    } else {
        $message = "Could not save the backup. Check folder permissions.";
    }
}

$files = list_backups();
$lastAuto = last_auto_backup_time();
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
    <strong>Automatic backups: ON</strong><br>
    A new backup is made by the system every <?php echo BACKUP_EVERY_HOURS; ?> hours, even when nobody presses a button
    (it checks whenever the site is used, and a scheduled task runs it daily). The newest <?php echo BACKUP_KEEP; ?> automatic backups are kept.<br>
    <span class="muted">Last automatic backup: <?php echo $lastAuto ? date("d M Y, H:i", $lastAuto) : "none yet"; ?></span>
  </div>

  <div class="card">
    <strong>Manual backup</strong>
    <p style="margin:6px 0">A full copy of the database (users, organisations, requests, pledges, volunteers, logs) as a <strong>.sql</strong> file.</p>
    <form method="post" class="inline">
      <button class="sm" name="action" value="save">Create backup on server</button>
      <button class="sm" name="action" value="download" style="background:#0B2447">Download a backup now</button>
    </form>
    <p class="muted" style="margin-bottom:0">Sign-in tokens and reset codes are not included. Backup files contain password hashes, so keep them private. You can download any backup from any device by logging in as admin.</p>
  </div>

  <h3>Saved backups</h3>
  <div class="scroll"><table>
    <tr><th>File</th><th>Type</th><th>Size</th><th>Created</th><th></th></tr>
    <?php if (!$files) echo "<tr><td colspan='5'>No backups saved yet.</td></tr>"; ?>
    <?php foreach ($files as $p) { $b = basename($p); ?>
      <tr>
        <td><?php echo htmlspecialchars($b); ?></td>
        <td><?php echo strpos($b, "_auto.sql") !== false ? "Automatic" : "Manual"; ?></td>
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