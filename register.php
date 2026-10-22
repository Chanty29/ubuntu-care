<?php
session_start();
require 'db.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $location = trim($_POST["location"]);
    $roleID = (int)$_POST["roleID"];

    if ($name === "" || $email === "" || strlen($password) < 6) {
        $message = "Please fill in all fields. Password must be at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT userID FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "That email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, roleID, location) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssis", $name, $email, $hash, $roleID, $location);
            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $message = "Something went wrong. Try again.";
            }
        }
    }
}
$roles = $conn->query("SELECT roleID, role_name FROM roles");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - Ubuntu Care</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Join Ubuntu Care</h2>
    <?php if ($message) echo "<p class='msg'>" . htmlspecialchars($message) . "</p>"; ?>
    <form method="post">
      <label>Full name</label>
      <input type="text" name="name" required>
      <label>Email</label>
      <input type="email" name="email" required>
      <label>Password</label>
      <input type="password" name="password" required>
      <label>Your area (e.g. Soweto)</label>
      <input type="text" name="location">
      <label>I am a...</label>
      <select name="roleID">
        <?php while ($r = $roles->fetch_assoc()) {
          echo "<option value='{$r['roleID']}'>" . ucfirst(str_replace('_', ' ', $r['role_name'])) . "</option>";
        } ?>
      </select>
      <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>