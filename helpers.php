<?php
function log_action($conn, $userID, $action, $details = "") {
    $stmt = $conn->prepare("INSERT INTO activity_log (userID, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userID, $action, $details);
    $stmt->execute();
}
function notify($conn, $userID, $title, $body = "") {
    $stmt = $conn->prepare("INSERT INTO notifications (userID, title, body) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userID, $title, $body);
    $stmt->execute();
}
function ngo_badge($s) {
    $labels = ["pending" => "Pending review", "approved" => "Approved", "rejected" => "Rejected"];
    return "<span class='st s-" . htmlspecialchars($s) . "'>" . htmlspecialchars($labels[$s] ?? ucfirst($s)) . "</span>";
}

function validate_password($p) {
    if (strlen($p) < 8) return "Password must be at least 8 characters.";
    if (!preg_match('/[A-Za-z]/', $p) || !preg_match('/[0-9]/', $p)) {
        return "Password must contain at least one letter and one number.";
    }
    return "";
}