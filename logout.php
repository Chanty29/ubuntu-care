<?php
session_start();
require 'db.php';
if (!empty($_COOKIE["uc_remember"])) {
    $parts = explode(":", $_COOKIE["uc_remember"], 2);
    $del = $conn->prepare("DELETE FROM remember_tokens WHERE selector = ?");
    $del->bind_param("s", $parts[0]);
    $del->execute();
}
setcookie("uc_remember", "", time() - 3600, "/");
$_SESSION = [];
session_destroy();
header("Location: login.php");
exit;