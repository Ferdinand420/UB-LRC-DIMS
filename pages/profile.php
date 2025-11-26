<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
if (!is_student()) { header('Location: librarian.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profile - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('profile'); ?>
    <main class="main-content">
      <header>
        <h1>My Profile</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Email: <?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
      </header>

      <!-- Profile Information -->
      <div class="card">
        <h3>Account Information</h3>
        <div id="profile-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading profile...
        </div>
        <div id="profile-info" style="display: none;">
          <div style="margin-bottom: 1.5rem;">
            <label for="full-name">Full Name</label>
            <input type="text" id="full-name" placeholder="Your full name">
            <button onclick="updateProfile()" class="btn btn-primary" style="margin-top: 0.5rem;">Update Name</button>
            <div id="profile-message" style="margin-top: 0.75rem;"></div>
          </div>
          <div style="padding: 1rem; background: #f9fafb; border-radius: var(--radius-md);">
            <div style="margin-bottom: 0.5rem;"><strong>Email:</strong> <span id="user-email"></span></div>
            <div style="margin-bottom: 0.5rem;"><strong>Role:</strong> <span id="user-role"></span></div>
            <div><strong>Member Since:</strong> <span id="user-since"></span></div>
          </div>
        </div>
      </div>

      <!-- Statistics -->
      <div class="stats-grid" style="margin-top: 1.5rem;">
        <div class="card">
          <h3>Total Reservations</h3>
          <p id="stat-total">0</p>
        </div>
        <div class="card">
          <h3>Approved</h3>
          <p id="stat-approved" style="color: #28a745;">0</p>
        </div>
        <div class="card">
          <h3>Feedback Submitted</h3>
          <p id="stat-feedback">0</p>
        </div>
        <div class="card">
          <h3>Violations</h3>
          <p id="stat-violations" style="color: #dc3545;">0</p>
        </div>
      </div>

      <!-- Recent Activity -->
      <div class="card" style="margin-top: 1.5rem;">
        <h3>Recent Reservations</h3>
        <div id="recent-activity" style="margin-top: 1rem;">
          <!-- Populated via JavaScript -->
        </div>
        <div id="no-activity" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No recent activity.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/profile.js"></script>
</body>
</html>
