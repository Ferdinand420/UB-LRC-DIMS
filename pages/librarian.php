<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Librarian Panel - DIMS</title>
	<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
	<?php include __DIR__ . '/../includes/header.php'; ?>
	<div class="container">
		<?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('dashboard'); ?>
		<main class="main-content">
			<header>
				<h1>Librarian Panel</h1>
				<p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
			</header>
			<div class="card">
				<h3>Pending Approvals Snapshot</h3>
				<p>2 reservations awaiting action. <a href="approvals.php">Review now</a>.</p>
			</div>
			<div class="card" style="margin-top:1.5rem;">
				<h3>Recent Activity</h3>
				<ul style="margin:0; padding-left:1.1rem; line-height:1.5;">
					<li>2025-11-23 Approved Room B for student@ub.edu.ph</li>
					<li>2025-11-24 Logged violation (No-show)</li>
				</ul>
			</div>
		</main>
	</div>
	<footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
	<script src="../assets/js/sidebar.js"></script>
	<script src="../assets/js/script.js"></script>
</body>
</html>