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
      <div class="card">
        <h3>Account Details</h3>
        <p>Role: Student</p>
        <p>Reservation Count: (placeholder)</p>
        <p>Feedback Submitted: (placeholder)</p>
      </div>
      <div class="card" style="margin-top:1.5rem;">
        <h3>History Snapshot</h3>
        <ul style="margin:0; padding-left:1.1rem; line-height:1.5;">
          <li>2025-11-20 Reserved Room A</li>
          <li>2025-11-21 Submitted feedback</li>
          <li>2025-11-23 Reserved Room C</li>
        </ul>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
</body>
</html>
