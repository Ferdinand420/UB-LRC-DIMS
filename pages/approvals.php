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
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>
      <div class="card">
        <h3>Reservation Requests</h3>
        <p>Placeholder: List pending reservations requiring librarian action.</p>
        <table style="width:100%; margin-top:1rem; border-collapse:collapse;">
          <thead>
            <tr><th>ID</th><th>Student</th><th>Room</th><th>Date</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr><td>101</td><td>student@ub.edu.ph</td><td>Room A</td><td>2025-11-25</td><td>Pending</td><td><button class="approve-btn">Approve</button> <button class="reject-btn">Reject</button></td></tr>
            <tr><td>102</td><td>learner@ub.edu.ph</td><td>Room B</td><td>2025-11-26</td><td>Pending</td><td><button class="approve-btn">Approve</button> <button class="reject-btn">Reject</button></td></tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
</body>
</html>
