<?php
session_start();
require 'db.php';
require_once 'helpers.php';
require_once 'mailer.php';

$uid = (int)($_SESSION["pending_verify"] ?? 0);
if ($uid <= 0) { header("Location: login.php"); exit; }

$q = $conn->prepare("SELECT name, email, email_verified FROM users WHERE userID = ?");
$q->bind_param("i", $uid);
$q->execute();
$u = $q->get_result()->fetch_assoc();
if (!$u) { unset($_SESSION["pending_verify"]); header("Location: login.php"); exit; }
if ((int)$u["email_verified"] === 1) { unset($_SESSION["pending_verify"]); header("Location: login.php?verified=1"); exit; }

$message = "";
$isError = false;

$shown = null; $err = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (($_POST["action"] ?? "") === "resend") {
        $err = null; $shown = null;
        if (send_code_email($conn, $uid, $u["email"], $u["name"], "verify", $err, $shown)) {
            $message = "A new code was sent to your email.";
        } else {
            $message = $err ?: "Could not send the email.";
            $isError = true;
            if ($shown) { $_SESSION["demo_code"] = $shown; }
        }
    } else {
        $code = preg_replace('/\D/', '', $_POST["code"] ?? "");
        if (strlen($code) !== 6) {
            $message = "Enter the 6-digit code from your email.";
            $isError = true;
        } else {
            $err = null;
            if (check_code($conn, $uid, "verify", $code, $err)) {
                $up = $conn->prepare("UPDATE users SET email_verified = 1 WHERE userID = ?");
                $up->bind_param("i", $uid);
                $up->execute();
                log_action($conn, $uid, "Email verified", $u["email"]);
                unset($_SESSION["pending_verify"], $_SESSION["demo_code"], $_SESSION["mail_error"]);
                header("Location: login.php?verified=1");
                exit;
            }
            $message = $err;
            $isError = true;
        }
    }
}

// Mask the email: j***@gmail.com
[$local, $domain] = explode("@", $u["email"]);
$masked = substr($local, 0, 1) . str_repeat("*", max(2, strlen($local) - 1)) . "@" . $domain;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verify your email - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; $auth = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Check your email</h2>
    <p>We sent a 6-digit code to <strong><?php echo htmlspecialchars($masked); ?></strong>. Enter it below to activate your account. The code works for 15 minutes.</p>

    <?php if (!empty($_SESSION["mail_error"]) && defined('MAIL_DEBUG') && MAIL_DEBUG) echo "<p class='muted'><small>Email problem: " . htmlspecialchars($_SESSION["mail_error"]) . "</small></p>"; ?>
    <?php if ($message) {
        $style = $isError ? "background:#FEE2E2;color:#991B1B;" : "background:#DCFCE7;color:#166534;";
        echo "<p style='$style padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>";
    } ?>
    <?php if (!empty($_SESSION["demo_code"])) echo "<p class='muted'>Demo mode: email could not be sent. Your code is <strong>" . htmlspecialchars($_SESSION["demo_code"]) . "</strong></p>"; ?>

    <form method="post">
      <label for="code">6-digit code</label>
      <input id="code" type="text" name="code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="123456">
      <button type="submit" name="action" value="verify">Verify email</button>
    </form>
    <form method="post" style="margin-top:12px">
      <button type="submit" name="action" value="resend" style="background:#0B2447">Resend code</button>
    </form>
    <p><a href="login.php">Back to login</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>