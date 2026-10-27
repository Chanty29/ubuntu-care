<?php
session_start();
require 'db.php';
require_once 'helpers.php';
require_once 'mailer.php';

$message = "";
$isError = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));

    if (mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
        $isError = true;
    } elseif (!mail_enabled()) {
        $message = "Email sending is not set up on this server.";
        $isError = true;
    } else {
        $q = $conn->prepare("SELECT userID, name FROM users WHERE email = ?");
        $q->bind_param("s", $email);
        $q->execute();
        $u = $q->get_result()->fetch_assoc();

        $_SESSION["reset_email"] = $email;
        unset($_SESSION["reset_demo_code"]);
        if ($u) {
            $err = null; $shown = null;
            send_code_email($conn, (int)$u["userID"], $email, $u["name"], "reset", $err, $shown);
            if ($shown) $_SESSION["reset_demo_code"] = $shown;
            log_action($conn, (int)$u["userID"], "Password reset requested", $email);
        }
        // Same answer whether or not the email exists, so nobody can test which emails are registered
        header("Location: reset_password.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot password - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; $auth = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Forgot your password?</h2>
    <p>Enter your email and we will send you a 6-digit code.</p>
    <?php if ($message) {
        $style = $isError ? "background:#FEE2E2;color:#991B1B;" : "background:#DCFCE7;color:#166534;";
        echo "<p style='$style padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>";
    } ?>
    <form method="post">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" required maxlength="150" autocomplete="username" autocapitalize="none" spellcheck="false">
      <button type="submit">Send code</button>
    </form>
    <p><a href="login.php">Back to login</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>