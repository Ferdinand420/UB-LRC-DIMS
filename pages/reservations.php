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
      <div class="card" style="max-width: 900px; margin: 0 auto;">
        <h3 style="margin-bottom: 1.5rem;">New Reservation</h3>
        <div id="form-message"></div>
        <form id="reservation-form" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem 1.5rem;">
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="student-id" style="margin: 0;">Student ID</label>
              <input type="text" id="student-id" name="student_id" placeholder="Enter your student ID" required style="margin-bottom: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="full-name" style="margin: 0;">Full Name</label>
              <input type="text" id="full-name" name="full_name" placeholder="Enter your full name" required style="margin-bottom: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="email" style="margin: 0;">UB Mail</label>
              <input type="email" id="email" name="email" placeholder="your.email@ub.edu.ph" required style="margin-bottom: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="password" style="margin: 0;">Password</label>
              <input type="password" id="password" name="password" placeholder="Enter your password" required style="margin-bottom: 0;">
            </div>
          </div>

          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="date" style="margin: 0;">Date</label>
              <input type="date" id="date" name="reservation_date" required style="margin-bottom: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="room" style="margin: 0;">Room</label>
              <select id="room" name="room_id" required style="margin-bottom: 0; width: 100%;">
                <option value="">Select a room...</option>
              </select>
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="purpose" style="margin: 0;">Purpose</label>
              <textarea id="purpose" name="purpose" rows="3" placeholder="Brief description of your reservation purpose..." required style="margin-bottom: 0; resize: vertical;"></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="start-time" style="margin: 0;">Start Time</label>
              <input type="time" id="start-time" name="start_time" required style="margin-bottom: 0;">
            </div>

            <div style="display: grid; grid-template-columns: 100px 1fr; align-items: center; gap: 0.75rem;">
              <label for="end-time" style="margin: 0;">End Time</label>
              <input type="time" id="end-time" name="end_time" required style="margin-bottom: 0;">
            </div>
          </div>

          <div style="grid-column: 1 / -1; display: flex; gap: 0.75rem; margin-top: 1rem; justify-content: flex-end;">
            <button type="button" class="btn" id="cancel-btn" style="padding: 0.6rem 2rem; background: var(--color-primary); color: #fff; border: none; transition: background 0.2s;" onmouseover="this.style.background='var(--color-primary-dark)'" onmouseout="this.style.background='var(--color-primary)'">Cancel</button>
            <button type="submit" class="btn btn-primary" style="padding: 0.6rem 2rem; background: var(--color-gold); color: var(--color-primary-dark); transition: background 0.2s;" onmouseover="this.style.background='#d89640'" onmouseout="this.style.background='var(--color-gold)'">Submit</button>
          </div>
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
              <?php if (is_student()): ?>
              <th>Action</th>
              <?php endif; ?>
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

  <!-- Confirmation Modal -->
  <div class="modal" id="confirm-modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <h3 id="modal-title">Confirm Action</h3>
        <p id="modal-message">Are you sure you want to proceed?</p>
        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
          <button class="btn btn-primary" id="modal-confirm">Confirm</button>
          <button class="btn btn-outline" id="modal-cancel" style="background: var(--color-primary); color: #fff;">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/reservations.js"></script>
</body>
</html>
