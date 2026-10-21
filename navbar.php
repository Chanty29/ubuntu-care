<?php
$loggedIn = isset($_SESSION["user_id"]);
$role = $_SESSION["role"] ?? "";
?>
<nav class="nav"><div class="nav-in">
  <a class="brand" href="index.php">
    <svg width="34" height="34" viewBox="0 0 40 40" aria-hidden="true">
      <circle cx="14" cy="16" r="8" fill="#0F766E"/><circle cx="26" cy="16" r="8" fill="#F59E0B" opacity=".92"/>
      <path d="M6 30c4-6 10-6 14-2 4-4 10-4 14 2" stroke="#0B4F4A" stroke-width="3" fill="none" stroke-linecap="round"/>
    </svg>
    Ubuntu Care
  </a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <?php if ($loggedIn) { ?>
      <a href="dashboard.php">Dashboard</a>
      <span class="who"><?php echo htmlspecialchars($_SESSION["name"]); ?> (<?php echo htmlspecialchars(str_replace('_',' ',$role)); ?>)</span>
      <a class="cta" href="logout.php">Log out</a>
    <?php } else { ?>
      <a href="login.php">Log in</a>
      <a class="cta" href="register.php">Register</a>
    <?php } ?>
  </div>
</div></nav>
<main class="container<?php echo !empty($narrow) ? ' narrow' : ''; ?>"></main>