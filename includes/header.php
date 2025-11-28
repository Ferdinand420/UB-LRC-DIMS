<?php
require_once __DIR__ . '/auth.php';
$inPages = str_contains($_SERVER['SCRIPT_NAME'], '/pages/');
$base = $inPages ? '../' : '';
?>
<div class="site-topbar">
  <div class="topbar-inner">
    <div class="brand"><a href="<?= $base ?>index.php" class="brand-link" aria-label="UB LRC-DIMS Home"><img src="<?= $base ?>assets/img/DIMS_logo.png" alt="DIMS Logo" class="brand-logo" height="40" width="40"> UB LRC-DIMS</a></div>
    <div class="actions">
      <?php if (get_role()): ?>
        <span style="color:#fff; font-size:.8rem; font-weight:600; letter-spacing:.5px;">Logged in as <?= htmlspecialchars(get_role()) ?></span>
        <a href="<?= $base ?>auth/logout.php" class="btn logout-btn" style="margin-left:.75rem;">Logout</a>
      <?php else: ?>
        <!-- No login buttons here (handled on public landing or modals) -->
      <?php endif; ?>
    </div>
  </div>
</div>
