<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rooms - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('rooms'); ?>
    <main class="main-content">
      <header>
        <h1>Rooms</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      

      <div class="card" style="margin-top: 2rem;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
          <h3 style="margin:0;">Rooms</h3>
        </div>
        <div id="rooms-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading rooms...
        </div>
        <div id="rooms-container" style="margin-top:1rem;">
          <!-- Status table populated by JS -->
        </div>
        <div id="no-rooms" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No rooms available.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/rooms.js"></script>
</body>
</html>
