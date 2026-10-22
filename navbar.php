<?php
$loggedIn = isset($_SESSION["user_id"]);
$role = $_SESSION["role"] ?? "";
?>
<nav class="nav"><div class="nav-in">
  <a class="brand" href="index.php">
    <svg width="36" height="36" viewBox="0 0 40 40" aria-hidden="true"><rect width="40" height="40" rx="10" fill="#0057B8"/><circle cx="15" cy="17" r="7" fill="#fff"/><circle cx="25" cy="17" r="7" fill="#F97316" opacity=".95"/><path d="M8 31c4-5 9-5 12-2 3-3 8-3 12 2" stroke="#fff" stroke-width="2.6" fill="none" stroke-linecap="round"/></svg>
    Ubuntu Care
  </a>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <?php if ($loggedIn) { ?>
      <a href="dashboard.php">Dashboard</a>
      <?php if ($role === "ngo_admin") { ?>
        <a href="ngo_manage.php">Manage</a>
      <?php } else { ?>
        <a href="my_activity.php">My activity</a>
      <?php } ?>
      <span class="who"><?php echo htmlspecialchars($_SESSION["name"]); ?> (<?php echo htmlspecialchars(str_replace('_',' ',$role)); ?>)</span>
      <a class="cta" href="logout.php">Log out</a>
    <?php } else { ?>
      <a href="login.php">Log in</a>
      <a class="cta" href="register.php">Register</a>
    <?php } ?>
  </div>
</div></nav>
<main class="container<?php echo !empty($narrow) ? ' narrow' : ''; ?>"></main>