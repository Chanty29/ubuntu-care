<?php
session_start();
require 'db.php';
$message = "";
$savedEmail = $_COOKIE["uc_email"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    $stmt = $conn->prepare("SELECT u.userID, u.name, u.password_hash, r.role_name FROM users u JOIN roles r ON u.roleID = r.roleID WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["userID"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role_name"];
                require_once 'helpers.php';
        log_action($conn, $user["userID"], "Login", $user["role_name"]);

        $opts = ["path" => "/", "httponly" => true, "samesite" => "Lax"];
        if ($remember) {
            $selector = bin2hex(random_bytes(8));
            $validator = bin2hex(random_bytes(32));
            $hash = hash("sha256", $validator);
            $expires = date("Y-m-d H:i:s", time() + 60 * 60 * 24 * 30);
            $ins = $conn->prepare("INSERT INTO remember_tokens (userID, selector, token_hash, expires_at) VALUES (?, ?, ?, ?)");
            $ins->bind_param("isss", $user["userID"], $selector, $hash, $expires);
            $ins->execute();
            setcookie("uc_remember", $selector . ":" . $validator, $opts + ["expires" => time() + 60 * 60 * 24 * 30]);
            setcookie("uc_email", $email, $opts + ["expires" => time() + 60 * 60 * 24 * 30]);
        }
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Wrong email or password.";
        $savedEmail = $email;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Log in to Ubuntu Care</h2>
    <?php if (isset($_GET["registered"])) echo "<p class='ok'>Registered! Please log in.</p>"; ?>
    <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
    <form method="post">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" autocomplete="username" required value="<?php echo htmlspecialchars($savedEmail); ?>">
      <label for="password">Password</label>
      <input id="password" type="password" name="password" autocomplete="current-password" required>
      <label class="check"><input type="checkbox" name="remember" checked> Keep me signed in on this device</label>
      <button type="submit">Log in</button>
    </form>
    <p>No account? <a href="register.php">Register</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>