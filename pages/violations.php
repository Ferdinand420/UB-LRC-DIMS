<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
if (!is_librarian()) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Violations - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('violations'); ?>
    <main class="main-content">
      <header>
        <h1>Violation Records</h1>
      </header>
      <div class="card">
        <h3>Record Violation</h3>
        <div id="violation-message"></div>
        <form id="violation-form">
          <label for="student-email">Student Email</label>
          <input type="email" id="student-email" name="student_email" placeholder="student@ub.edu.ph" required />
          
          <label for="violation-type">Violation Type</label>
          <select id="violation-type" name="violation_type" required>
            <option value="">Select violation type...</option>
            <option value="no-show">No-show (Student didn't arrive)</option>
            <option value="late">Late (Arrived late)</option>
            <option value="damaged property">Damaged Property (Room/equipment damaged)</option>
            <option value="overcapacity">Overcapacity (Exceeded room capacity)</option>
          </select>
          
          <label for="violation-room">Room (Optional)</label>
          <select id="violation-room" name="room_id">
            <option value="">Select room (if applicable)...</option>
          </select>
          
          <label for="violation-description">Description</label>
          <textarea id="violation-description" name="description" rows="4" placeholder="Describe the violation..." required></textarea>
          
          <button type="submit" class="btn btn-primary">Log Violation</button>
        </form>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/violations.js"></script>
</body>
</html>
