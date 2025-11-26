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

      <?php if (is_student()): ?>
      <!-- Reservation Form Card (Students Only) -->
      <div class="card">
        <h3>New Reservation</h3>
        <div id="form-message"></div>
        <form id="reservation-form">
          <label for="room">Room</label>
          <select id="room" name="room_id" required>
            <option value="">Select a room...</option>
          </select>

          <label for="date">Reservation Date</label>
          <input type="date" id="date" name="reservation_date" required>

          <label for="start-time">Start Time</label>
          <input type="time" id="start-time" name="start_time" required>

          <label for="end-time">End Time</label>
          <input type="time" id="end-time" name="end_time" required>

          <label for="purpose">Purpose</label>
          <textarea id="purpose" name="purpose" rows="3" placeholder="Brief description of your reservation purpose..."></textarea>

          <button type="submit" class="btn btn-primary">Submit Reservation</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- Reservations Table Card -->
      <div class="card" style="margin-top: 2rem;">
        <h3><?php echo is_student() ? 'My Reservations' : 'All Reservations'; ?></h3>
        <div id="reservations-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading reservations...
        </div>
        <table id="reservation-table" style="display: none;">
          <thead>
            <tr>
              <?php if (is_librarian()): ?>
              <th>Student</th>
              <?php endif; ?>
              <th>Room</th>
              <th>Date</th>
              <th>Time</th>
              <th>Purpose</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Populated via JavaScript -->
          </tbody>
        </table>
        <div id="no-reservations" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No reservations found.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/reservations.js"></script>
</body>
</html>
