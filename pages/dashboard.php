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
      <!-- Room Availability & Waitlist Buttons -->
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
        <div class="dashboard-big-btn" id="room-availability-btn" onclick="showRoomAvailabilityModal()">
          <div class="big-btn-icon">🚪</div>
          <div class="big-btn-content">
            <h3>Room Availability</h3>
            <p id="available-rooms-count">Loading...</p>
          </div>
        </div>
        <div class="dashboard-big-btn" id="waitlist-btn" onclick="showWaitlistModal()">
          <div class="big-btn-icon">⏳</div>
          <div class="big-btn-content">
            <h3>Waitlist</h3>
            <p id="waitlist-count">Loading...</p>
          </div>
        </div>
      </div>

      <!-- Recent Activity -->
      <div style="margin-top: 2.5rem;">
        <h2 style="margin-bottom: 1rem; color: var(--color-primary-dark); font-size: 1.5rem;">📜 Recent Activity</h2>
        <div class="activity-container">
          <div id="activity-list">
            <p style="color: var(--color-text-muted); text-align: center; padding: 2rem;">Loading activity...</p>
          </div>
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>

  <!-- Room Availability Modal -->
  <div id="roomAvailabilityModal" class="modal">
    <div class="modal-content" style="max-width: 700px;">
      <div class="modal-header">
        <h2>🚪 Room Availability</h2>
        <button class="modal-close" onclick="closeRoomAvailabilityModal()">&times;</button>
      </div>
      <div class="modal-body" id="roomAvailabilityContent">
        <p style="text-align: center; padding: 2rem;">Loading...</p>
      </div>
    </div>
  </div>

  <!-- Waitlist Modal -->
  <div id="waitlistModal" class="modal">
    <div class="modal-content" style="max-width: 800px;">
      <div class="modal-header">
        <h2>⏳ Waitlist</h2>
        <button class="modal-close" onclick="closeWaitlistModal()">&times;</button>
      </div>
      <div class="modal-body" id="waitlistContent">
        <p style="text-align: center; padding: 2rem;">Loading...</p>
      </div>
    </div>
  </div>

  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/dashboard.js"></script>
</body>
</html>
