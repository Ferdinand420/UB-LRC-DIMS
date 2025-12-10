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
  <title>Approvals - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('approvals'); ?>
    <main class="main-content">
      <header>
        <h1>Pending Approvals</h1>
      </header>
      <div class="card">
        <h3>Reservation Requests</h3>
        <div id="approvals-message"></div>
        <div id="approvals-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading pending reservations...
        </div>
        <div id="approvals-container" style="display: none;">
          <table>
            <thead>
              <tr>
                <th>Student</th>
                <th>Room</th>
                <th>Date</th>
                <th>Time</th>
                <th>Purpose</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="approvals-tbody">
              <!-- Populated via JavaScript -->
            </tbody>
          </table>
        </div>
        <div id="no-approvals" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No pending reservations at this time.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/approvals.js"></script>
</body>
</html>
