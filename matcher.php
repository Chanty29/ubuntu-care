<?php
require_once 'lookups.php';

function detect_category($text, $KEYWORDS) {
    $text = strtolower($text);
    $scores = [];
    foreach ($KEYWORDS as $cat => $words) {
        $scores[$cat] = 0;
        foreach ($words as $w) {
            if (strpos($text, $w) !== false) $scores[$cat]++;
        }
    }
    arsort($scores);
    $top = key($scores);
    return $scores[$top] > 0 ? $top : null;
}

function distance_km($lat1, $lng1, $lat2, $lng2) {
    $r = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

// Score out of 100: 70 for matching category, up to 30 for being close (within 60 km)
function rank_ngos($conn, $category, $lat, $lng) {
    $result = $conn->query("SELECT ngoID, org_name, category, area, latitude, longitude FROM ngo_profiles WHERE status = 'approved'");
    $ranked = [];
    while ($ngo = $result->fetch_assoc()) {
        $dist = distance_km($lat, $lng, $ngo["latitude"], $ngo["longitude"]);
        $catScore  = ($category !== null && $ngo["category"] === $category) ? 70 : 0;
        $distScore = max(0, 30 * (1 - $dist / 60));
        $ngo["score"] = round($catScore + $distScore, 2);
        $ngo["distance"] = round($dist, 1);
        $ranked[] = $ngo;
    }
    usort($ranked, fn($a, $b) => $b["score"] <=> $a["score"]);
    return array_slice($ranked, 0, 5);
}