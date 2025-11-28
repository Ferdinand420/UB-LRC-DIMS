<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Support & Help - UB LRC-DIMS</title>
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
  <main class="main-content" style="background:#fff; margin:2rem auto; max-width:1000px; border-radius:12px; padding:2rem 2.25rem; box-shadow:0 4px 18px rgba(0,0,0,0.08);">
    <h1 style="margin-top:0;">Support & Help</h1>
    <p class="lead" style="font-weight:600; color:var(--color-primary);">Find assistance, contact info, and common answers.</p>
    <section style="margin-top:1.75rem; display:grid; gap:1.75rem; grid-template-columns:repeat(auto-fit,minmax(300px,1fr));">
      <div style="background:var(--color-surface); padding:1.25rem 1.1rem; border-radius:10px; box-shadow:var(--shadow-sm);">
        <h3 style="margin-top:0; color:var(--color-primary);">Contact</h3>
        <p>Email: <a href="mailto:lrc-support@ub.edu.ph" style="color:var(--color-primary); font-weight:600;">lrc-support@ub.edu.ph</a></p>
        <p>Phone: (043) 000-1234</p>
        <p>Desk Hours: 8:00 AM – 5:00 PM</p>
      </div>
      <div style="background:var(--color-surface); padding:1.25rem 1.1rem; border-radius:10px; box-shadow:var(--shadow-sm);">
        <h3 style="margin-top:0; color:var(--color-primary);">FAQ</h3>
        <ul style="padding-left:1.1rem; margin:0; line-height:1.5;">
          <li>How do I reserve a room?</li>
          <li>What is my UB Mail format?</li>
          <li>How can I reset my password?</li>
          <li>Who approves reservations?</li>
        </ul>
      </div>
      <div style="background:var(--color-surface); padding:1.25rem 1.1rem; border-radius:10px; box-shadow:var(--shadow-sm);">
        <h3 style="margin-top:0; color:var(--color-primary);">Guides</h3>
        <p>Room Reservation Walkthrough (PDF)</p>
        <p>Student Collaboration Policy</p>
        <p>Digital Archive Access Steps</p>
      </div>
    </section>
    <div style="margin-top:2.25rem; text-align:center;">
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
