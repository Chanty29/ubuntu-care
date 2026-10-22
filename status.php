<?php
$STATUS_LABELS = [
    "pending" => "Awaiting response", "accepted" => "Accepted", "in_progress" => "In progress",
    "completed" => "Completed", "declined" => "Declined",
    "pledged" => "Pledged", "received" => "Received",
    "signed_up" => "Signed up", "confirmed" => "Confirmed",
];
function status_badge($s) {
    global $STATUS_LABELS;
    $label = $STATUS_LABELS[$s] ?? ucfirst($s);
    return "<span class='st s-" . htmlspecialchars($s) . "'>" . htmlspecialchars($label) . "</span>";
}
function status_track($s) {
    if ($s === "declined") return "";
    $steps = ["pending", "accepted", "in_progress", "completed"];
    $i = array_search($s, $steps);
    $html = "<div class='track'>";
    foreach ($steps as $k => $x) $html .= "<span class='" . (($i !== false && $k <= $i) ? "on" : "") . "'></span>";
    return $html . "</div>";
}