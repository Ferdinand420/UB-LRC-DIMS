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
        <h1>Logged Violations</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>
      <div class="card">
        <h3>Record Violation</h3>
        <form>
          <input type="email" placeholder="Student Email" required />
          <input type="text" placeholder="Room" required />
          <textarea rows="3" placeholder="Description" required></textarea>
          <button type="submit">Log Violation</button>
        </form>
      </div>
      <div class="card" style="margin-top:1.75rem;">
        <h3>Recent Violations</h3>
        <table style="width:100%; margin-top:.75rem; border-collapse:collapse;">
          <thead><tr><th>ID</th><th>Student</th><th>Room</th><th>Date</th><th>Notes</th></tr></thead>
          <tbody>
            <tr><td>15</td><td>student@ub.edu.ph</td><td>Room A</td><td>2025-11-23</td><td>Food in restricted area</td></tr>
            <tr><td>16</td><td>learner@ub.edu.ph</td><td>Room B</td><td>2025-11-24</td><td>No-show after booking</td></tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
</body>
</html>
