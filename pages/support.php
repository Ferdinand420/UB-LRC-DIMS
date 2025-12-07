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
      <div class="brand"><a href="../index.php" class="brand-link" aria-label="UB LRC-DIMS Home"><img src="../assets/media/DIMS_logo.png" alt="DIMS Logo" class="brand-logo" height="40" width="40"> UB LRC-DIMS</a></div>
      <div class="actions">
        <a href="../index.php" class="btn btn-primary">Home</a>
        <a href="#" class="btn btn-primary" data-open="student">Student Login</a>
        <a href="#" class="btn btn-primary" data-open="librarian">Librarian Login</a>
      </div>
    </div>
  </div>
  <main class="main-content" style="background:#fff; margin:2rem auto; max-width:1000px; border-radius:12px; padding:2rem 2.25rem; box-shadow:0 4px 18px rgba(0,0,0,0.08);">
    <h1 style="margin-top:0;">Need Help Using UB LRC-DIMS?</h1>
    <p class="lead" style="font-weight:600; color:var(--color-primary);">We're here to make your experience smooth and stress-free. Whether you're booking a room or troubleshooting an issue, this page has you covered.</p>
    
    <h2 style="margin-top:2rem; color:var(--color-primary);">Getting Started</h2>
    <ul style="line-height:1.8;">
      <li><strong>How to Log In:</strong> Use your UB email to access the system securely.</li>
      <li><strong>Booking a Room:</strong> Check real-time availability and reserve a slot with just a few clicks.</li>
    </ul>

    <h2 style="margin-top:2rem; color:var(--color-primary);">FAQs</h2>
    <div style="line-height:1.8;">
      <p><strong>Q: What happens if I miss my check-in time?</strong><br>A: Your slot will be released to the next person on the waitlist automatically.</p>
      <p><strong>Q: How can I check my reservation status?</strong><br>A: You can view your active and pending reservations. Full history is accessible only to librarians.</p>
      <p><strong>Q: Who can I contact for technical issues?</strong><br>A: Reach out to the LRC staff or email us at support@ubdims.edu.ph.</p>
    </div>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Troubleshooting Tips</h2>
    <ul style="line-height:1.8;">
      <li>If the page doesn't load, check your internet connection.</li>
      <li>Make sure your UB email is active and verified.</li>
      <li>For QR code issues, try refreshing or using a different device.</li>
    </ul>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Contact Us</h2>
    <p>Need more help? Visit the LRC front desk or email us. We're happy to assist!</p>

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
          <a href="#" class="mini-link" data-open="forgot">Forgot password?</a>
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
          <a href="#" class="mini-link" data-open="forgot">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
  <!-- Forgot Password Step 1: Enter Email -->
  <div class="login-modal" id="forgot-modal" aria-hidden="true" role="dialog" aria-labelledby="forgotModalTitle">
    <div class="login-modal-dialog">
      <button class="modal-close" data-close>&times;</button>
      <h2 id="forgotModalTitle" class="login-title">Forgot Password</h2>
      <p>Enter your email address, then click Reset Password.</p>
      <form id="request-reset-form" style="margin-top: 12px;">
        <label for="reset-email">Email</label>
        <input id="reset-email" name="email" type="email" placeholder="you@ub.edu.ph" required />
        <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Reset Password</button>
        <div id="request-reset-msg" class="mini-note" style="margin-top: 10px;"></div>
      </form>
    </div>
  </div>
  <!-- Forgot Password Step 2: Set New Password -->
  <div class="login-modal" id="forgot-reset-modal" aria-hidden="true" role="dialog" aria-labelledby="forgotResetModalTitle">
    <div class="login-modal-dialog">
      <button class="modal-close" data-close>&times;</button>
      <h2 id="forgotResetModalTitle" class="login-title">Set New Password</h2>
      <form id="perform-reset-form" style="margin-top: 12px;">
        <input id="reset-token-input" name="token" type="hidden" />
        <label for="new-password">New Password</label>
        <input id="new-password" name="password" type="password" placeholder="At least 8 characters" required />
        <label for="confirm-password">Confirm New Password</label>
        <input id="confirm-password" name="confirm" type="password" required />
        <button type="submit" class="btn btn-primary" style="margin-top: 12px;">Update Password</button>
        <div id="perform-reset-msg" class="mini-note" style="margin-top: 10px;"></div>
      </form>
    </div>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/login-modal.js"></script>
</body>
</html>
