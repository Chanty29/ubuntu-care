</main>
<div class="foot">Ubuntu Care &middot; Final year IT project, Richfield Graduate Institute of Technology</div>
<script src="app.js"></script>
<?php if (isset($conn) && $conn instanceof mysqli) { require_once __DIR__ . '/backup_lib.php'; maybe_auto_backup($conn); } ?>