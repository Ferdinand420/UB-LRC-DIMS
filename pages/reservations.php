<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('reservations'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <h1>Reservations</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      <!-- Reservation Form Card -->
      <div class="card">
        <h3>New Reservation</h3>
        <form id="reservation-form">
          <input type="text" id="name" placeholder="Your Name" required>
          <input type="text" id="resource" placeholder="Resource Name" required>
          <input type="date" id="date" required>
          <button type="submit">Submit Reservation</button>
        </form>
      </div>

      <!-- Reservations Table Card -->
      <div class="card" style="margin-top: 2rem;">
        <h3>All Reservations</h3>
        <table id="reservation-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Resource</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Populated via script.js -->
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/script.js"></script>
</body>
</html>
