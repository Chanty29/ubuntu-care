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