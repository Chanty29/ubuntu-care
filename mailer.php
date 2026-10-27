<?php
// Sends email through Gmail (PHPMailer) and handles 6-digit email codes.
if (!defined('MAIL_USER') && file_exists(__DIR__ . '/config.php')) require_once __DIR__ . '/config.php';
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;

function mail_enabled() {
    return defined('MAIL_USER') && defined('MAIL_PASS') && MAIL_USER !== '' && MAIL_PASS !== ''
        && strpos(MAIL_USER, 'your-gmail') === false;
}

function send_mail($toEmail, $toName, $subject, $html, &$error = null) {
    if (!mail_enabled()) { $error = "Email is not configured."; return false; }
    $m = new PHPMailer(true);
    try {
        $m->isSMTP();
        $m->Host = "smtp.gmail.com";
        $m->SMTPAuth = true;
        $m->Username = MAIL_USER;
        $m->Password = str_replace(" ", "", MAIL_PASS);
        $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $m->Port = 587;
        $m->CharSet = "UTF-8";
        $m->Timeout = 15;
        if (defined('MAIL_INSECURE_TLS') && MAIL_INSECURE_TLS) {
            $m->SMTPOptions = ["ssl" => ["verify_peer" => false, "verify_peer_name" => false, "allow_self_signed" => true]];
        }
        $m->setFrom(MAIL_USER, "Ubuntu Care");
        $m->addAddress($toEmail, $toName);
        $m->isHTML(true);
        $m->Subject = $subject;
        $m->Body = $html;
        $m->AltBody = strip_tags($html);
        $m->send();
        return true;
    } catch (Throwable $e) {
        $error = $m->ErrorInfo ?: $e->getMessage();
        return false;
    }
}

// Creates a fresh 6-digit code (only its hash is stored). Returns null if asked again too soon.
function issue_code($conn, $userID, $purpose) {
    $q = $conn->prepare("SELECT TIMESTAMPDIFF(SECOND, created_at, NOW()) AS age FROM email_codes WHERE userID = ? AND purpose = ? ORDER BY codeID DESC LIMIT 1");
    $q->bind_param("is", $userID, $purpose);
    $q->execute();
    $last = $q->get_result()->fetch_assoc();
    if ($last && (int)$last["age"] < 30) return null;

    $code = str_pad((string)random_int(0, 999999), 6, "0", STR_PAD_LEFT);
    $hash = hash("sha256", $code);
    $del = $conn->prepare("DELETE FROM email_codes WHERE userID = ? AND purpose = ?");
    $del->bind_param("is", $userID, $purpose);
    $del->execute();
    $ins = $conn->prepare("INSERT INTO email_codes (userID, purpose, code_hash, expires_at) VALUES (?, ?, ?, NOW() + INTERVAL 15 MINUTE)");
    $ins->bind_param("iss", $userID, $purpose, $hash);
    $ins->execute();
    return $code;
}

// Emails the code. $shown receives the code only when DEMO_MODE is on and sending failed.
function send_code_email($conn, $userID, $email, $name, $purpose, &$error = null, &$shown = null) {
    $code = issue_code($conn, $userID, $purpose);
    if ($code === null) { $error = "Please wait 30 seconds before asking for another code."; return false; }
    $what = $purpose === "reset" ? "reset your password" : "verify your email";
    $html = "<div style='font-family:Arial,sans-serif;max-width:480px'>"
          . "<h2 style='color:#0B2447'>Ubuntu Care</h2>"
          . "<p>Hi " . htmlspecialchars($name) . ",</p>"
          . "<p>Use this code to $what:</p>"
          . "<p style='font-size:32px;letter-spacing:6px;font-weight:bold;color:#0057B8'>$code</p>"
          . "<p>It expires in 15 minutes. If you did not ask for it, you can ignore this email.</p></div>";
    $ok = send_mail($email, $name, "Your Ubuntu Care code: $code", $html, $error);
    if (!$ok && defined('DEMO_MODE') && DEMO_MODE) $shown = $code;
    return $ok;
}

function check_code($conn, $userID, $purpose, $code, &$error = null) {
    $q = $conn->prepare("SELECT codeID, code_hash, attempts, (expires_at > NOW()) AS valid FROM email_codes WHERE userID = ? AND purpose = ? ORDER BY codeID DESC LIMIT 1");
    $q->bind_param("is", $userID, $purpose);
    $q->execute();
    $row = $q->get_result()->fetch_assoc();
    if (!$row) { $error = "No active code. Press Resend code."; return false; }
    if (!(int)$row["valid"]) { $error = "That code has expired. Press Resend code."; return false; }
    if ((int)$row["attempts"] >= 5) { $error = "Too many wrong tries. Press Resend code."; return false; }
    $up = $conn->prepare("UPDATE email_codes SET attempts = attempts + 1 WHERE codeID = ?");
    $up->bind_param("i", $row["codeID"]);
    $up->execute();
    if (hash_equals($row["code_hash"], hash("sha256", $code))) {
        $del = $conn->prepare("DELETE FROM email_codes WHERE userID = ? AND purpose = ?");
        $del->bind_param("is", $userID, $purpose);
        $del->execute();
        return true;
    }
    $left = max(0, 4 - (int)$row["attempts"]);
    $error = "Wrong code. " . $left . " try(s) left.";
    return false;
}