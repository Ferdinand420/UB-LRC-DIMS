<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login - UB LRC-DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .auth-wrapper { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
    .auth-card { background:#fff; width:100%; max-width:420px; padding:2rem 1.75rem; border-radius:var(--radius-lg); box-shadow:var(--shadow-md); border-top:6px solid var(--color-primary); }
    .auth-card h1 { margin:0 0 1.25rem; font-size:1.6rem; color:var(--color-primary); }
    .auth-card form label { font-weight:600; display:block; margin-bottom:0.35rem; }
    .auth-card form input { width:100%; padding:0.65rem 0.75rem; margin-bottom:1rem; border:1px solid #ccc; border-radius:var(--radius-md); font-size:0.95rem; }
    .auth-card form input:focus { outline:none; border-color:var(--color-primary); box-shadow:0 0 0 3px rgba(98,7,24,0.15); }
    .auth-actions { display:flex; justify-content:space-between; align-items:center; margin-top:0.5rem; }
    .small-link { font-size:0.8rem; text-decoration:none; color:var(--color-primary); }
    .small-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1>Student Login</h1>
      <form method="post" action="dashboard.php">
        <label for="email">UB Email</label>
        <input id="email" type="email" name="email" placeholder="student@ub.edu.ph" required>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" placeholder="••••••••" required>
        <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
        <div class="auth-actions">
          <a href="../index.php" class="small-link">Back to Landing</a>
          <a href="#" class="small-link">Forgot password?</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
