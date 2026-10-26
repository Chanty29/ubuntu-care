<?php
session_start();
require 'db.php';
require 'lookups.php';
require 'ai_matcher.php';
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") { header("Location: login.php"); exit; }

$text = trim($_POST["text"] ?? "I lost my job and my children have not eaten since yesterday");
$result = null; $error = null; $ms = 0;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $start = microtime(true);
    $result = smart_detect($text, $error);
    $ms = round((microtime(true) - $start) * 1000);
}
?>
<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>AI test</title><link rel="stylesheet" href="style.css"></head>
<body>
<?php $narrow = true; include 'navbar.php'; ?>
  <h2>AI test</h2>
  <p>AI configured: <strong><?php echo ai_enabled() ? "yes" : "no"; ?></strong></p>
  <div class="panel">
    <form method="post">
      <label>Try a request</label>
      <textarea name="text" rows="4"><?php echo htmlspecialchars($text); ?></textarea>
      <button type="submit">Detect category</button>
    </form>
  </div>
  <?php if ($result) { ?>
    <div class="card">
      Category: <strong><?php echo htmlspecialchars($result[0] ?? "none"); ?></strong><br>
      Detected by: <strong><?php echo htmlspecialchars($result[1]); ?></strong> (<?php echo $ms; ?> ms)
      <?php if ($error) echo "<br><small>Note: " . htmlspecialchars($error) . "</small>"; ?>
    </div>
  <?php } ?>
<?php include 'footer.php'; ?>
</body></html>