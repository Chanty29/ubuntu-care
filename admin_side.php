<?php
$pendingCount = $conn->query("SELECT COUNT(*) c FROM ngo_profiles WHERE status = 'pending'")->fetch_assoc()["c"];
$sideItems = [
    ["admin_dashboard.php", "dashboard", "Overview"],
    ["admin_ngos.php", "ngos", "Organisations"],
    ["admin_users.php", "users", "Users"],
    ["admin_logs.php", "logs", "Activity log"],
];
?>
<div class="shell">
<aside class="side">
  <b>Admin console</b>
  <?php foreach ($sideItems as $i) { ?>
    <a class="<?php echo $active === $i[1] ? 'on' : ''; ?>" href="<?php echo $i[0]; ?>">
      <?php echo $i[2]; ?>
      <?php if ($i[1] === "ngos" && $pendingCount > 0) echo "<span class='pill'>$pendingCount</span>"; ?>
    </a>
  <?php } ?>
</aside>
<section class="content"></section>