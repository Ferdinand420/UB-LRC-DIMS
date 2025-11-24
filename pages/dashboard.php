<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('dashboard'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <h1>Dashboard</h1>
        <p style="margin:0; font-size:.85rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      <!-- Stats Cards Grid (Student Overview) -->
      <div class="stats-grid">
        <div class="card">
          <h3>Total Reservations</h3>
          <p id="total-reservations">0</p>
        </div>
        <div class="card">
          <h3>Pending</h3>
          <p id="pending-reservations">0</p>
        </div>
        <div class="card">
          <h3>Approved</h3>
          <p id="approved-reservations">0</p>
        </div>
        <div class="card">
          <h3>Total Feedback</h3>
          <p id="total-feedback">0</p>
        </div>
      </div>

      <!-- Quick Actions Grid (Student Shortcuts) -->
      <div class="quick-actions-grid">
        <div class="card">
          <h3>New Reservation</h3>
          <p>Submit a new reservation request quickly.</p>
          <a href="reservations.php"><button>Go to Reservations</button></a>
        </div>
        <div class="card">
          <h3>Submit Feedback</h3>
          <p>Share your feedback or suggestions.</p>
          <a href="feedback.php"><button>Go to Feedback</button></a>
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
