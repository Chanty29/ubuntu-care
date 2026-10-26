<?php
session_start();
require 'db.php';
require_once 'helpers.php';

$token = $_GET["token"] ?? ($_POST["token"] ?? "");
$valid = null;
$message = "";
$done = false;

if (preg_match('/^[a-f0-9]{64}$/', $token)) {
    $hash = hash("sha256", $token);
    $stmt = $conn->prepare("SELECT resetID, userID FROM password_resets WHERE token_hash = ? AND used = 0 AND expires_at > NOW()");
    $stmt->bind_param("s", $hash);
    $stmt->execute();
    $valid = $stmt->get_result()->fetch_assoc();
}

if ($valid && $_SERVER["REQUEST_METHOD"] === "POST") {
    $p = $_POST["password"] ?? "";
    $c = $_POST["confirm"] ?? "";
    $err = validate_password($p);
    if ($err !== "") {
        $message = $err;
    } elseif ($p !== $c) {
        $message = "The two passwords do not match.";
    } else {
        $newHash = password_hash($p, PASSWORD_BCRYPT);
        $u = $conn->prepare("UPDATE users SET password_hash = ? WHERE userID = ?");
        $u->bind_param("si", $newHash, $valid["userID"]);
        $u->execute();
        $m = $conn->prepare("UPDATE password_resets SET used = 1 WHERE resetID = ?");
        $m->bind_param("i", $valid["resetID"]);
        $m->execute();
        $d = $conn->prepare("DELETE FROM remember_tokens WHERE userID = ?");
        $d->bind_param("i", $valid["userID"]);
        $d->execute();
        log_action($conn, $valid["userID"], "Password reset completed", "");
        header("Location: login.php?reset=1");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Choose a new password - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Choose a new password</h2>
    <?php if (!$valid) { ?>
      <p class="msg">This reset link is invalid, already used, or has expired.</p>
      <p><a href="forgot_password.php">Request a new link</a></p>
    <?php } else { ?>
      <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
      <form method="post">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label for="password">New password</label>
        <input id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
        <p class="hint">At least 8 characters, with a letter and a number.</p>
        <label for="confirm">Confirm new password</label>
        <input id="confirm" type="password" name="confirm" required minlength="8" maxlength="72" autocomplete="new-password">
        <button type="submit">Change password</button>
      </form>
    <?php } ?>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>