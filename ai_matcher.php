<?php
// AI layer: understands a request in plain language and picks a category.
// Asks several models at once and uses the first answer. If none answer in time,
// the keyword matcher is used, so the system never stalls or breaks.
if (!defined('AI_API_KEY') && file_exists(__DIR__ . '/config.php')) require_once __DIR__ . '/config.php';
require_once __DIR__ . '/matcher.php';

function ai_enabled() {
    return defined('AI_API_KEY') && defined('AI_MODEL')
        && AI_API_KEY !== '' && AI_API_KEY !== 'your-key-here'
        && AI_MODEL !== '' && AI_MODEL !== 'your-model-name-here';
}

function ai_models() {
    return array_values(array_unique([AI_MODEL, "gemini-3.5-flash", "gemini-3.5-flash-lite", "gemini-3.7-flash", "gemini-3.8-flash"]));
}

// Sends the prompt to all models in parallel. Returns the first good answer.
function ai_call($prompt, &$error = null, &$usedModel = null) {
    $payload = json_encode([
        "contents" => [["parts" => [["text" => $prompt]]]],
        "generationConfig" => ["temperature" => 0, "thinkingConfig" => ["thinkingLevel" => "low"]],
    ]);

    $mh = curl_multi_init();
    $handles = [];
    foreach (ai_models() as $model) {
        $ch = curl_init("https://generativelanguage.googleapis.com/v1beta/models/" . $model . ":generateContent");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => ["Content-Type: application/json", "x-goog-api-key: " . AI_API_KEY],
            CURLOPT_TIMEOUT => 15,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$model] = $ch;
    }

    $answer = null;
    $errors = [];
    $done = [];
    do {
        curl_multi_exec($mh, $running);
        while ($info = curl_multi_info_read($mh)) {
            $ch = $info["handle"];
            $model = array_search($ch, $handles, true);
            if ($model === false || isset($done[$model])) continue;
            $done[$model] = true;
            if ($info["result"] !== CURLE_OK) { $errors[] = "$model: " . curl_error($ch); continue; }
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $data = json_decode(curl_multi_getcontent($ch), true);
            if ($code === 200) {
                $text = $data["candidates"][0]["content"]["parts"][0]["text"] ?? null;
                if ($text !== null) { $answer = trim($text); $usedModel = $model; break 2; }
                $errors[] = "$model: empty answer";
            } else {
                $errors[] = "$model: HTTP $code " . ($data["error"]["message"] ?? "");
            }
        }
        if ($running && $answer === null) curl_multi_select($mh, 0.3);
    } while ($running && $answer === null);

    foreach ($handles as $ch) { curl_multi_remove_handle($mh, $ch); curl_close($ch); }
    curl_multi_close($mh);

    if ($answer === null) $error = $errors ? implode(" | ", $errors) : "No answer in time.";
    return $answer;
}

function ai_detect_category($text, $CATEGORIES, &$error = null, &$usedModel = null) {
    $list = [];
    foreach ($CATEGORIES as $key => $label) $list[] = "$key ($label)";
    $prompt = "You help match people in need to community organisations in South Africa.\n"
        . "Read the request and choose the ONE best category from this list:\n"
        . implode(", ", $list) . ".\n"
        . "Reply with only the category key (for example: food). No other words.\n\n"
        . "Request: " . $text;
    $answer = ai_call($prompt, $error, $usedModel);
    if ($answer === null) return null;
    $answer = strtolower(preg_replace('/[^a-z_ ]/i', ' ', $answer));
    foreach (array_keys($CATEGORIES) as $key) {
        if (preg_match('/\b' . preg_quote($key, '/') . '\b/', $answer)) return $key;
    }
    $error = "AI answer was not a known category: " . $answer;
    return null;
}

// Returns [category, source]. source is "AI (model)" or "keyword fallback".
function smart_detect($text, &$error = null) {
    global $CATEGORIES, $KEYWORDS;
    if (ai_enabled()) {
        $used = null;
        $cat = ai_detect_category($text, $CATEGORIES, $error, $used);
        if ($cat !== null) return [$cat, "AI (" . $used . ")"];
    } else {
        $error = "AI is not configured.";
    }
    return [detect_category($text, $KEYWORDS), "keyword fallback"];
}