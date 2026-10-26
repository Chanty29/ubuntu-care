<?php
session_start();
require 'db.php';
require_once 'helpers.php';

if (isset($_SESSION["user_id"]) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . ($_SESSION["role"] === "admin" ? "admin_dashboard.php" : "dashboard.php"));
    exit;
}

const MAX_ATTEMPTS = 3;
const LOCK_MINUTES = 3;

$message = "";
$locked = false;
$savedEmail = $_COOKIE["uc_email"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $remember = isset($_POST["remember"]);
    $savedEmail = $email;
    $ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        $message = "Enter a valid email and your password.";
    } else {
        // How many recent failures for this email? (time is calculated inside MySQL)
        $chk = $conn->prepare("SELECT COUNT(*) AS c,
                TIMESTAMPDIFF(SECOND, NOW(), MAX(attempted_at) + INTERVAL " . LOCK_MINUTES . " MINUTE) AS secs
            FROM login_attempts
            WHERE email = ? AND attempted_at > NOW() - INTERVAL " . LOCK_MINUTES . " MINUTE");
        $chk->bind_param("s", $email);
        $chk->execute();
        $row = $chk->get_result()->fetch_assoc();
        $fails = (int)$row["c"];

        if ($fails >= MAX_ATTEMPTS) {
            $locked = true;
            $mins = max(1, (int)ceil(max(0, (int)$row["secs"]) / 60));
            $message = "Too many failed attempts. This account is locked. Try again in about " . $mins . " minute(s).";
        } else {
            $stmt = $conn->prepare("SELECT u.userID, u.name, u.password_hash, r.role_name FROM users u JOIN roles r ON u.roleID = r.roleID WHERE u.email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

            if ($user && password_verify($password, $user["password_hash"])) {
                // Success: clear the failed attempts
                $clr = $conn->prepare("DELETE FROM login_attempts WHERE email = ?");
                $clr->bind_param("s", $email);
                $clr->execute();

                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["userID"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role_name"];
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
            }

            // Failure: record it
            $rec = $conn->prepare("INSERT INTO login_attempts (email, ip) VALUES (?, ?)");
            $rec->bind_param("ss", $email, $ip);
            $rec->execute();
            $fails++;
            $left = MAX_ATTEMPTS - $fails;

            if ($left <= 0) {
                $locked = true;
                $message = "Too many failed attempts. This account is locked for " . LOCK_MINUTES . " minutes.";
                if ($user) log_action($conn, $user["userID"], "Account locked", "Too many failed logins");
            } else {
                $message = "Wrong email or password. " . $left . " attempt(s) left before the account is locked.";
            }
        }
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
<?php $narrow = true; $auth = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Log in to Ubuntu Care</h2>
    <?php if (isset($_GET["registered"])) echo "<p class='ok'>Account created! Please log in.</p>"; ?>
    <?php if (isset($_GET["reset"])) echo "<p class='ok'>Password changed. Please log in with your new password.</p>"; ?>
    <?php if ($message) {
        $style = $locked ? "background:#FEE2E2;color:#991B1B;" : "background:#FEF3C7;color:#92400E;";
        echo "<p style='$style padding:10px 14px;border-radius:8px;font-weight:600'>" . htmlspecialchars($message) . "</p>";
    } ?>
    <form method="post">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" required maxlength="150" autocomplete="username" value="<?php echo htmlspecialchars($savedEmail); ?>">
      <label for="password">Password</label>
      <input id="password" type="password" name="password" required autocomplete="current-password">
      <label class="check"><input type="checkbox" name="remember" checked> Keep me signed in on this device</label>
      <button type="submit">Log in</button>
    </form>
    <p><a href="forgot_password.php">Forgot your password?</a></p>
    <p>No account? <a href="register.php">Register</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>