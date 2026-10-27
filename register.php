<?php
session_start();
require 'db.php';
require 'lookups.php';
require_once 'helpers.php';
require_once 'email_check.php';
require_once 'mailer.php';

$ROLE_LABELS = [
    "beneficiary" => "I need help (Beneficiary)",
    "ngo_admin"   => "I run an organisation (NGO)",
    "donor"       => "I want to donate (Donor)",
    "volunteer"   => "I want to volunteer (Volunteer)",
];
if (isset($_SESSION["user_id"]) && $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . ($_SESSION["role"] === "admin" ? "admin_dashboard.php" : "dashboard.php"));
    exit;
}
$errors = [];
$name = $email = $location = "";
$roleID = 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm"] ?? "";
    $location = $_POST["location"] ?? "";
    $roleID = (int)($_POST["roleID"] ?? 0);

    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
        $errors[] = "Enter your full name (2 to 100 characters).";
    } elseif (!preg_match("/^[\p{L}][\p{L} .'-]*$/u", $name)) {
        $errors[] = "Your name can only contain letters, spaces, apostrophes and hyphens.";
    }
    $emailError = validate_email_strict($email);
    if ($emailError !== "") {
        $errors[] = $emailError;
    }
    $pe = validate_password($password);
    if ($pe !== "") {
        $errors[] = $pe;
    } elseif ($password !== $confirm) {
        $errors[] = "The two passwords do not match.";
    }
    if (!isset($AREAS[$location])) {
        $errors[] = "Choose your area from the list.";
    }
    $rq = $conn->prepare("SELECT role_name FROM roles WHERE roleID = ?");
    $rq->bind_param("i", $roleID);
    $rq->execute();
    $rn = $rq->get_result()->fetch_assoc();
    if (!$rn || $rn["role_name"] === "admin") {
        $errors[] = "Choose a valid role.";
    }

    if (!$errors) {
        $check = $conn->prepare("SELECT userID FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = "That email is already registered. Try logging in.";
        } else {
            $needsVerify = mail_enabled() ? 0 : 1;   // if email is not set up, accounts are active straight away
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password_hash, roleID, location, email_verified) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisi", $name, $email, $hash, $roleID, $location, $needsVerify);
            if ($stmt->execute()) {
                $newID = $conn->insert_id;
                log_action($conn, $newID, "Registered", $email);
                if ($needsVerify === 0) {
                    $_SESSION["pending_verify"] = $newID;
                    $err = null; $shown = null;
                    send_code_email($conn, $newID, $email, $name, "verify", $err, $shown);
                    $_SESSION["mail_error"] = $err;
                    if ($shown) $_SESSION["demo_code"] = $shown;
                    header("Location: verify_email.php");
                } else {
                    header("Location: login.php?registered=1");
                }
                exit;
            }
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
$roles = $conn->query("SELECT roleID, role_name FROM roles WHERE role_name <> 'admin'");
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
<?php $narrow = true; $auth = true; include 'navbar.php'; ?>
  <div class="panel">
    <h2>Join Ubuntu Care</h2>
    <?php if ($errors) { ?>
      <ul class="errs"><?php foreach ($errors as $e) echo "<li>" . htmlspecialchars($e) . "</li>"; ?></ul>
    <?php } ?>
    <form method="post">
      <label for="name">Full name</label>
      <input id="name" type="text" name="name" required minlength="2" maxlength="100" autocomplete="name" value="<?php echo htmlspecialchars($name); ?>">

      <label for="email">Email</label>
      <input id="email" type="email" name="email" required maxlength="150" autocomplete="username" autocapitalize="none" spellcheck="false" value="<?php echo htmlspecialchars($email); ?>">
      <p class="hint">We will send a code to this address to confirm it is yours.</p>

      <label for="password">Password</label>
      <input id="password" type="password" name="password" required minlength="8" maxlength="72" autocomplete="new-password">
      <p class="hint">At least 8 characters, with a letter and a number.</p>

      <label for="confirm">Confirm password</label>
      <input id="confirm" type="password" name="confirm" required minlength="8" maxlength="72" autocomplete="new-password">

      <label for="location">Your area</label>
      <select id="location" name="location" required>
        <option value="">Choose your area</option>
        <?php foreach ($AREAS as $a => $coords) {
          $sel = ($a === $location) ? "selected" : "";
          echo "<option value='" . htmlspecialchars($a) . "' $sel>" . htmlspecialchars($a) . "</option>";
        } ?>
      </select>

      <label for="roleID">I am a...</label>
      <select id="roleID" name="roleID" required>
        <?php while ($r = $roles->fetch_assoc()) {
          $sel = ((int)$r["roleID"] === $roleID) ? "selected" : "";
          $label = $ROLE_LABELS[$r["role_name"]] ?? ucfirst($r["role_name"]);
          echo "<option value='" . (int)$r["roleID"] . "' $sel>" . htmlspecialchars($label) . "</option>";
        } ?>
      </select>

      <button type="submit">Create account</button>
    </form>
    <p>Already have an account? <a href="login.php">Log in</a></p>
  </div>
<?php include 'footer.php'; ?>
</body>
</html>