<?php
session_start();
require 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT u.userID, u.name, u.password_hash, r.role_name FROM users u JOIN roles r ON u.roleID = r.roleID WHERE u.email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id(true);
        $_SESSION["user_id"] = $user["userID"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role_name"];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Wrong email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Ubuntu Care</title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 420px; margin: 40px auto; padding: 0 16px; }
    input, button { width: 100%; padding: 10px; margin: 6px 0 14px; box-sizing: border-box; }
    button { background: #0057b8; color: white; border: none; cursor: pointer; }
    .msg { color: #b00020; } .ok { color: #1b7f3b; }
  </style>
</head>
<body>
  <h2>Log in to Ubuntu Care</h2>
  <?php if (isset($_GET["registered"])) echo "<p class='ok'>Registered! Please log in.</p>"; ?>
  <?php if ($message) echo "<p class='msg'>$message</p>"; ?>
  <form method="post">
    <label>Email</label>
    <input type="email" name="email" required>
    <label>Password</label>
    <input type="password" name="password" required>
    <button type="submit">Log in</button>
  </form>
  <p>No account? <a href="register.php">Register</a></p>
</body>
</html>