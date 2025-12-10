<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Feedback - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('feedback'); ?>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <h1>Feedback</h1>
      </header>

      <!-- Feedback Form Card (Students Only) -->
      <?php if (!is_librarian()): ?>
      <div class="card">
        <h3>Submit Feedback</h3>
        <p style="font-size: 0.875rem; color: #666; margin-bottom: 1rem;">Share your thoughts, suggestions, or concerns about the LRC services and room condition.</p>
        <div id="feedback-message"></div>
        <form id="feedback-form">
          <label for="condition-status">Room Condition</label>
          <select id="condition-status" name="condition_status" required>
            <option value="">Select room condition...</option>
            <option value="clean">Clean (Good condition)</option>
            <option value="dirty">Dirty (Needs cleaning)</option>
            <option value="damaged">Damaged (Equipment/furniture damaged)</option>
          </select>

          <label for="room-select">Room</label>
          <select id="room-select" name="room_id" required>
            <option value="" disabled selected>Select a room...</option>
          </select>
          
          <label for="feedback-text">Your Feedback</label>
          <textarea id="feedback-text" name="feedback_text" placeholder="Write your feedback and suggestions..." rows="5" required></textarea>
          <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
      </div>
      <?php endif; ?>

      <!-- Feedback List Card -->
      <div class="card" style="margin-top: 2rem;">
        <h3><?php echo is_librarian() ? 'All Feedback' : 'My Feedback History'; ?></h3>
        <div id="feedback-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading feedback...
        </div>
        <div id="feedback-container" style="display: none;">
          <table id="feedback-table">
            <thead>
              <tr>
                <?php if (is_librarian()): ?>
                <th>Student</th>
                <?php endif; ?>
                <th>Room</th>
                <th>Condition</th>
                <th>Feedback</th>
                <th>Status</th>
                <th>Date</th>
                <?php if (is_librarian()): ?>
                <th style="text-align:right;">Action</th>
                <?php endif; ?>
              </tr>
            </thead>
            <tbody>
              <!-- Populated via JavaScript -->
            </tbody>
          </table>
        </div>
        <div id="no-feedback" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No feedback submitted yet.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script>
    window.USER_ROLE = "<?php echo htmlspecialchars(get_role()); ?>";
  </script>
  <script src="../assets/js/feedback.js"></script>
</body>
</html>
