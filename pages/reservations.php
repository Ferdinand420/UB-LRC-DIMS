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
      </header>

      <?php if (is_student()): ?>
      <!-- Reservation Form Card (Students Only) -->
      <div class="card" style="max-width: 900px; margin: 0 auto;">
        <h3 style="margin-bottom: 1.5rem;">New Reservation</h3>
        <div id="form-message"></div>
        
        <!-- Step 1: Room & Capacity Selection -->
        <form id="reservation-form-step1" style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="email" style="margin: 0;">UB Mail</label>
            <input type="email" id="email" name="email" placeholder="your.email@ub.edu.ph" required style="margin-bottom: 0;">
          </div>

          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="date" style="margin: 0;">Date</label>
            <input type="date" id="date" name="reservation_date" required style="margin-bottom: 0;">
          </div>

          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="room" style="margin: 0;">Room</label>
            <select id="room" name="room_id" required style="margin-bottom: 0; width: 100%;">
              <option value="">Select a room...</option>
            </select>
          </div>

          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="capacity" style="margin: 0;">Number of Students</label>
            <select id="capacity" name="capacity" required style="margin-bottom: 0; width: 100%;">
              <option value="">Select number of students...</option>
              <option value="1">1</option>
              <option value="2">2</option>
              <option value="3">3</option>
              <option value="4">4</option>
              <option value="5">5</option>
              <option value="6">6</option>
              <option value="7">7</option>
              <option value="8">8</option>
              <option value="9">9</option>
              <option value="10">10</option>
            </select>
          </div>

          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="purpose" style="margin: 0;">Purpose</label>
            <textarea id="purpose" name="purpose" rows="3" placeholder="Brief description of your reservation purpose..." required style="margin-bottom: 0; resize: vertical;"></textarea>
          </div>

          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="start-time" style="margin: 0;">Start Time</label>
            <input type="time" id="start-time" name="start_time" required style="margin-bottom: 0;">
          </div>

          <div style="display: grid; grid-template-columns: 150px 1fr; align-items: center; gap: 0.75rem;">
            <label for="end-time" style="margin: 0;">End Time</label>
            <input type="time" id="end-time" name="end_time" required style="margin-bottom: 0;">
          </div>

          <div style="display: flex; gap: 0.75rem; margin-top: 1rem; justify-content: flex-end;">
            <button type="button" class="btn" id="cancel-btn-step1" style="padding: 0.6rem 2rem; background: var(--color-primary); color: #fff; border: none; transition: background 0.2s;">Cancel</button>
            <button type="button" class="btn" id="next-btn-step1" style="padding: 0.6rem 2rem; background: var(--color-gold); color: var(--color-primary-dark); transition: background 0.2s;">Next</button>
          </div>
        </form>

        <!-- Step 2: Student IDs (hidden initially) -->
        <form id="reservation-form-step2" style="display: none; gap: 0.75rem;">
          <div id="student-ids-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <!-- Dynamically populated based on capacity -->
          </div>

          <div style="display: flex; gap: 0.75rem; margin-top: 1rem; justify-content: flex-end;">
            <button type="button" class="btn" id="cancel-btn-step2" style="padding: 0.6rem 2rem; background: var(--color-primary); color: #fff; border: none; transition: background 0.2s;">Cancel</button>
            <button type="submit" class="btn" id="submit-btn-step2" style="padding: 0.6rem 2rem; background: var(--color-gold); color: var(--color-primary-dark); transition: background 0.2s;">Submit</button>
          </div>
        </form>

        <!-- Hidden data store for step1 values -->
        <div id="step1-data" style="display: none;"></div>
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
