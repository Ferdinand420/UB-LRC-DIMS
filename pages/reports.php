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
  <title>Reports - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('reports'); ?>
    <main class="main-content">
      <header>
        <h1>Usage Reports</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>
      <div class="card">
        <h3>Summary</h3>
        <p>Placeholder: aggregate stats (total reservations, peak times, room utilization).</p>
      </div>
      <div class="card" style="margin-top:1.5rem;">
        <h3>Export</h3>
        <p>Download CSV or PDF (future implementation).</p>
        <button>Export CSV</button>
        <button style="margin-left:.5rem;">Export PDF</button>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
</body>
</html>
