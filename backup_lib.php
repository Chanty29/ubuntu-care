<?php
// Backup helpers: used by the Backup page, the scheduled task and the automatic check.
date_default_timezone_set("Africa/Johannesburg");
require_once __DIR__ . '/helpers.php';

if (!defined('BACKUP_EVERY_HOURS')) define('BACKUP_EVERY_HOURS', 24);
if (!defined('BACKUP_KEEP')) define('BACKUP_KEEP', 14);

function backup_dir() {
    $dir = __DIR__ . "/backups";
    if (!is_dir($dir)) mkdir($dir, 0775, true);
    if (!file_exists($dir . "/.htaccess")) file_put_contents($dir . "/.htaccess", "Require all denied\n");
    return $dir;
}

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

function list_backups() {
    $files = glob(backup_dir() . "/ubuntu_care_backup_*.sql") ?: [];
    rsort($files);
    return $files;
}

// $label is "manual" or "auto". Keeps only the newest BACKUP_KEEP automatic backups.
function save_backup($conn, $label = "manual") {
    $name = "ubuntu_care_backup_" . date("Y-m-d_H-i-s") . "_" . $label . ".sql";
    if (file_put_contents(backup_dir() . "/" . $name, make_dump($conn)) === false) return null;
    $autos = glob(backup_dir() . "/ubuntu_care_backup_*_auto.sql") ?: [];
    rsort($autos);
    foreach (array_slice($autos, BACKUP_KEEP) as $old) @unlink($old);
    return $name;
}

function last_auto_backup_time() {
    $latest = 0;
    foreach (glob(backup_dir() . "/ubuntu_care_backup_*_auto.sql") ?: [] as $f) $latest = max($latest, filemtime($f));
    return $latest;
}

function system_user_id($conn) {
    $r = $conn->query("SELECT u.userID FROM users u JOIN roles r ON u.roleID = r.roleID WHERE r.role_name = 'admin' ORDER BY u.userID LIMIT 1");
    $row = $r ? $r->fetch_assoc() : null;
    return $row ? (int)$row["userID"] : null;
}

// Runs on its own: if the newest automatic backup is older than BACKUP_EVERY_HOURS, make a new one.
function maybe_auto_backup($conn) {
    $lock = backup_dir() . "/.lock";
    try {
        if (time() - last_auto_backup_time() < BACKUP_EVERY_HOURS * 3600) return null;
        if (file_exists($lock) && time() - filemtime($lock) < 300) return null;
        touch($lock);
        $name = save_backup($conn, "auto");
        @unlink($lock);
        $uid = system_user_id($conn);
        if ($name && $uid) log_action($conn, $uid, "Automatic database backup", $name);
        return $name;
    } catch (Throwable $e) {
        @unlink($lock);
        return null;
    }
}