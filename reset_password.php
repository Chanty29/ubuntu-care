<?php
session_start();
require 'db.php';
require_once 'helpers.php';
require_once 'mailer.php';

$email = $_SESSION["reset_email"] ?? "";
if ($email === "") { header("Location: forgot_password.php"); exit; }

$message = "";
$isError = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = preg_replace('/\D/', '', $_POST["code"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm"] ?? "";

    $q = $conn->prepare("SELECT userID FROM users WHERE email = ?");
    $q->bind_param("s", $email);
    $q->execute();
    $u = $q->get_result()->fetch_assoc();

    $pe = validate_password($password);
    if (strlen($code) !== 6) {
        $message = "Enter the 6-digit code from your email.";
        $isError = true;
    } elseif ($pe !== "") {
        $message = $pe;
        $isError = true;
    } elseif ($password !== $confirm) {
        $message = "The two passwords do not match.";
        $isError = true;
    } else {
        $err = null;
        if ($u && check_code($conn, (int)$u["userID"], "reset", $code, $err)) {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $up = $conn->prepare("UPDATE users SET password_hash = ?, email_verified = 1 WHERE userID = ?");
            $up->bind_param("si", $hash, $u["userID"]);
            $up->execute();

            $clr = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
            $clr->bind_param("s", $email);
            $clr->execute();
            $rt = $conn->prepare("DELETE FROM remember_tokens WHERE userID = ?");
            $rt->bind_param("i", $u["userID"]);
            $rt->execute();

            log_action($conn, (int)$u["userID"], "Password reset", $email);
            unset($_SESSION["reset_email"], $_SESSION["reset_demo_code"]);
            header("Location: login.php?reset=1");
            exit;
        }
        $message = $err ?: "That code is not valid. Check it or request a new one.";
        $isError = true;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset password - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; $auth = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Reset your password</h2>
    <p>If <strong><?php echo htmlspecialchars($email); ?></strong> is registered, we sent a 6-digit code to it. It works for 15 minutes.</p>
    <?php if (!empty($_SESSION["reset_demo_code"])) echo "<p class='muted'>Demo mode: email could not be sent. Your code is <strong>" . htmlspecialchars($_SESSION["reset_demo_code"]) . "</strong></p>"; ?>
    <?php if ($message) {
        $style = $isError ? "background:#FEE2E2;color:#991B1B;" : "background:#DCFCE7;color:#166534;";
        echo "<p style='$style padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>";
    } ?>
    <form method="post">
      <label for="code">6-digit code</label>
      <input id="code" type="text" name="code" required inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" placeholder="123456">

      <label for="password">New password</label>
      <input id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
      <p class="hint">At least 8 characters, with a letter and a number.</p>

      <label for="confirm">Confirm new password</label>
      <input id="confirm" type="password" name="confirm" required minlength="8" maxlength="72" autocomplete="new-password">

      <button type="submit">Change password</button>
    </form>
    <p><a href="forgot_password.php">Send a new code</a> &middot; <a href="login.php">Back to login</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>