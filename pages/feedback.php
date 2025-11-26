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
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      <!-- Feedback Form Card -->
      <div class="card">
        <h3>Submit Feedback</h3>
        <p style="font-size: 0.875rem; color: #666; margin-bottom: 1rem;">Share your thoughts, suggestions, or concerns about the LRC services.</p>
        <div id="feedback-message"></div>
        <form id="feedback-form">
          <label for="feedback-text">Your Feedback</label>
          <textarea id="feedback-text" name="message" placeholder="Write your feedback..." rows="5" required></textarea>
          <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
      </div>

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
                <th>Feedback</th>
                <th>Status</th>
                <th>Date</th>
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
  <script src="../assets/js/feedback.js"></script>
</body>
</html>
