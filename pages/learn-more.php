<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Learn More - UB LRC-DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="has-bg">
  <div class="site-topbar">
    <div class="topbar-inner">
      <div class="brand"><a href="../index.php" class="brand-link" aria-label="UB LRC-DIMS Home"><img src="../assets/img/DIMS_logo.png" alt="DIMS Logo" class="brand-logo" height="40" width="40"> UB LRC-DIMS</a></div>
      <div class="actions">
        <a href="../index.php" class="btn btn-primary">Home</a>
        <a href="#" class="btn btn-primary" data-open="student">Student Login</a>
        <a href="#" class="btn btn-primary" data-open="librarian">Librarian Login</a>
      </div>
    </div>
  </div>
  <main class="main-content" style="background:#fff; margin:2rem auto; max-width:1100px; border-radius:12px; padding:2.25rem 2.5rem; box-shadow:0 4px 18px rgba(0,0,0,0.08);">
    <h1 style="margin-top:0;">About UB LRC-DIMS</h1>
    <p class="lead" style="font-weight:600; color:var(--color-primary);">A centralized platform for discussion space management, reservations, and resource access.</p>
    <section style="margin-top:2rem; display:grid; gap:2rem; grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
      <div style="background:var(--color-surface); padding:1.25rem 1.1rem; border-radius:10px; box-shadow:var(--shadow-sm);">
        <h3 style="margin:0 0 .5rem; color:var(--color-primary);">Key Features</h3>
        <ul style="padding-left:1.1rem; margin:0; line-height:1.55;">
          <li>Room & resource reservations</li>
          <li>Approval workflow for librarians</li>
          <li>Feedback submission</li>
          <li>Usage statistics dashboard</li>
        </ul>
      </div>
      <div style="background:var(--color-surface); padding:1.25rem 1.1rem; border-radius:10px; box-shadow:var(--shadow-sm);">
        <h3 style="margin:0 0 .5rem; color:var(--color-primary);">Benefits</h3>
        <p style="margin:0 0 .5rem;">Improves transparency in room allocation and streamlines librarian approvals.</p>
        <p style="margin:0;">Enhances student collaboration with clear scheduling and status updates.</p>
      </div>
      <div style="background:var(--color-surface); padding:1.25rem 1.1rem; border-radius:10px; box-shadow:var(--shadow-sm);">
        <h3 style="margin:0 0 .5rem; color:var(--color-primary);">Roadmap</h3>
        <ul style="padding-left:1.1rem; margin:0; line-height:1.55;">
          <li>Single Sign-On integration</li>
          <li>Advanced analytics charts</li>
          <li>Automated email notifications</li>
          <li>Digital archive search</li>
        </ul>
      </div>
    </section>
    <div style="margin-top:2.5rem; text-align:center;">
      <a href="../index.php" class="btn btn-primary">Back to Home</a>
    </div>
  </main>
  <!-- Login Modals -->
  <div class="login-modal" id="student-modal" aria-hidden="true" role="dialog" aria-labelledby="studentModalTitle">
    <div class="login-modal-dialog">
      <button class="modal-close" data-close>&times;</button>
      <h2 id="studentModalTitle" class="login-title">Student Login</h2>
      <form method="post" action="dashboard.php" class="login-form">
        <label for="student-email">UB Mail</label>
        <input id="student-email" type="email" name="email" placeholder="student@ub.edu.ph" required>
        <label for="student-password">Password</label>
        <input id="student-password" type="password" name="password" placeholder="••••••••" required>
        <button type="submit" class="btn btn-primary full">Sign In</button>
        <div class="login-meta">
          <a href="forgot_password.php" class="mini-link">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
  <div class="login-modal" id="librarian-modal" aria-hidden="true" role="dialog" aria-labelledby="librarianModalTitle">
    <div class="login-modal-dialog">
      <button class="modal-close" data-close>&times;</button>
      <h2 id="librarianModalTitle" class="login-title">Librarian Login</h2>
      <p class="login-sub">Restricted access portal for authorized library staff.</p>
      <form method="post" action="dashboard.php" class="login-form">
        <label for="librarian-email">UB Mail</label>
        <input id="librarian-email" type="email" name="email" placeholder="staff@ub.edu.ph" required>
        <label for="librarian-password">Password</label>
        <input id="librarian-password" type="password" name="password" placeholder="••••••••" required>
        <button type="submit" class="btn btn-primary full">Sign In</button>
        <div class="login-meta">
          <a href="forgot_password.php" class="mini-link">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/login-modal.js"></script>
</body>
</html>
