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
      <div class="card">
        <h3>Available Rooms</h3>
        <p>Placeholder list of discussion rooms. Integrate dynamic data later.</p>
        <ul style="margin:0; padding-left:1.1rem; line-height:1.5;">
          <li>Room A (Capacity 6)</li>
          <li>Room B (Capacity 10)</li>
          <li>Room C (Capacity 4)</li>
          <li>Room D (Capacity 8)</li>
        </ul>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/script.js"></script>
</body>
</html>
