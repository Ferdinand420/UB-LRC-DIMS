<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>History - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('history'); ?>
    <main class="main-content">
      <header>
        <h1>History</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      <!-- Filter Options -->
      <div class="card">
        <h3>Filter History</h3>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <button onclick="filterHistory('all')" class="btn btn-primary" id="filter-all">All</button>
          <button onclick="filterHistory('reservations')" class="btn btn-primary" id="filter-reservations">Reservations</button>
          <button onclick="filterHistory('feedback')" class="btn btn-primary" id="filter-feedback">Feedback</button>
          <button onclick="filterHistory('violations')" class="btn btn-primary" id="filter-violations">Violations</button>
        </div>
      </div>

      <!-- History Timeline -->
      <div class="card" style="margin-top: 1.5rem;">
        <h3>Activity Timeline</h3>
        <div id="history-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading activity...
        </div>
        <div id="history-timeline" style="display: none; margin-top: 1rem;">
          <!-- Populated via JavaScript -->
        </div>
        <div id="no-history" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No activity found.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/history.js"></script>
  <script src="../assets/js/violations.js"></script>
</body>
</html>
