<?php
// Run from the command line or Windows Task Scheduler:  php auto_backup.php
if (php_sapi_name() !== 'cli') { http_response_code(403); exit("Command line only."); }
require __DIR__ . '/db.php';
require __DIR__ . '/backup_lib.php';

$name = save_backup($conn, "auto");
$uid = system_user_id($conn);
if ($name && $uid) log_action($conn, $uid, "Scheduled database backup", $name);
echo $name ? "Backup saved: $name\n" : "Backup failed\n";