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
      <div class="brand"><a href="../index.php" class="brand-link" aria-label="UB LRC-DIMS Home"><img src="../assets/media/DIMS_logo.png" alt="DIMS Logo" class="brand-logo" height="40" width="40"> UB LRC-DIMS</a></div>
      <div class="actions">
        <a href="../index.php" class="btn btn-primary">Home</a>
        <a href="#" class="btn btn-primary" data-open="student">Student Login</a>
        <a href="#" class="btn btn-primary" data-open="librarian">Librarian Login</a>
      </div>
    </div>
  </div>
  <main class="main-content" style="background:#fff; margin:2rem auto; max-width:1100px; border-radius:12px; padding:2.25rem 2.5rem; box-shadow:0 4px 18px rgba(0,0,0,0.08);">
    <h1 style="margin-top:0;">What is UB LRC-DIMS?</h1>
    <p class="lead" style="font-weight:600; color:var(--color-primary);">UB LRC-DIMS stands for University of Batangas Learning Resource Center – Discussion Integrated Management System. It's a smart, student-centered platform designed to make discussion room reservations easier, faster, and fairer.</p>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Why We Built It</h2>
    <p>The old manual sign-in system was slow and prone to errors. Students couldn't see room availability in real time, and staff had to manage everything by hand. UB LRC-DIMS solves these problems with automation, transparency, and real-time updates.</p>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Key Features</h2>
    <ul style="line-height:1.8;">
      <li>Real-Time Room Availability</li>
      <li>Secure Login via UB Email</li>
      <li>Reservation History</li>
      <li>Feedback Submission</li>
      <li>Auto-Cancellation & Waitlisting</li>
    </ul>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Who Benefits</h2>
    <ul style="line-height:1.8;">
      <li><strong>Students:</strong> No more waiting or guessing—just book and go.</li>
      <li><strong>LRC Staff:</strong> Less paperwork, more time to support students.</li>
      <li><strong>UB Admin:</strong> Access to usage data for better planning and policy-making.</li>
    </ul>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Rules</h2>
    <ul style="line-height:1.8;">
      <li>Strictly maximum of 10 students</li>
      <li>No food or beverages allowed</li>
    </ul>

    <h2 style="margin-top:2rem; color:var(--color-primary);">Built with Purpose</h2>
    <p>This system was developed by UB students using Agile methodology, with input from real users—students, librarians, and staff. It's designed to grow with your needs and reflect UB's commitment to innovation.</p>

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
          <a href="#" class="mini-link">Forgot password?</a>
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
          <a href="#" class="mini-link">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/login-modal.js"></script>
</body>
</html>
