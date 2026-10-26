<?php
session_start();
require 'db.php';
require_once 'helpers.php';
if (file_exists('config.php')) require_once 'config.php';

if (isset($_SESSION["user_id"]) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . ($_SESSION["role"] === "admin" ? "admin_dashboard.php" : "dashboard.php"));
    exit;
}
$message = "";
$sent = false;
$link = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } else {
        $stmt = $conn->prepare("SELECT userID FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $hash = hash("sha256", $token);
            $ins = $conn->prepare("INSERT INTO password_resets (userID, token_hash, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $ins->bind_param("is", $user["userID"], $hash);
            $ins->execute();
            log_action($conn, $user["userID"], "Password reset requested", $email);
            if (defined("DEMO_MODE") && DEMO_MODE) {
                $link = "reset_password.php?token=" . $token;
            }
        }
        $sent = true;
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
<?php $narrow = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Reset your password</h2>
    <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
    <?php if ($sent) { ?>
      <p class="ok">If that email is registered, a reset link has been created. It expires in 1 hour.</p>
      <?php if ($link) { ?>
        <div class="card">
          <strong>Demo mode</strong><br>
          <span class="muted">No email server is connected in this prototype, so the reset link is shown here instead of being emailed.</span><br><br>
          <a class="btn" href="<?php echo htmlspecialchars($link); ?>">Choose a new password</a>
        </div>
      <?php } ?>
    <?php } else { ?>
      <p class="muted">Enter the email you registered with.</p>
      <form method="post">
        <label for="email">Email</label>
        <input id="email" type="email" name="email" required maxlength="150" autocomplete="username">
        <button type="submit">Create reset link</button>
      </form>
    <?php } ?>
    <p><a href="login.php">Back to log in</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>