<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Librarian Login - UB LRC-DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .auth-wrapper { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:2rem; }
    .auth-card { background:#fff; width:100%; max-width:440px; padding:2.2rem 1.9rem; border-radius:var(--radius-lg); box-shadow:var(--shadow-md); border-top:6px solid var(--color-primary); }
    .auth-card h1 { margin:0 0 1.1rem; font-size:1.55rem; color:var(--color-primary); }
    .auth-card p.sub { margin:0 0 1.4rem; font-size:0.85rem; color:#555; }
    .auth-card form label { font-weight:600; display:block; margin-bottom:0.35rem; }
    .auth-card form input { width:100%; padding:0.65rem 0.75rem; margin-bottom:1rem; border:1px solid #ccc; border-radius:var(--radius-md); font-size:0.95rem; }
    .auth-card form input:focus { outline:none; border-color:var(--color-primary); box-shadow:0 0 0 3px rgba(98,7,24,0.15); }
    .auth-row { display:flex; gap:0.75rem; }
    .auth-row .half { flex:1; }
    .auth-actions { display:flex; justify-content:space-between; align-items:center; margin-top:0.6rem; }
    .small-link { font-size:0.8rem; text-decoration:none; color:var(--color-primary); }
    .small-link:hover { text-decoration:underline; }
  </style>
</head>
<body>
  <div class="auth-wrapper">
    <div class="auth-card">
      <h1>Librarian Login</h1>
      <p class="sub">Restricted access portal for authorized library staff.</p>
      <form method="post" action="dashboard.php">
        <label for="username">Username</label>
        <input id="username" type="text" name="username" placeholder="librarian" required>
        <label for="password">Password</label>
        <input id="password" type="password" name="password" placeholder="••••••••" required>
        <div class="auth-row">
          <div class="half">
            <label for="pin">Security PIN</label>
            <input id="pin" type="text" name="pin" placeholder="1234" required>
          </div>
          <div class="half">
            <label for="code">Access Code</label>
            <input id="code" type="text" name="code" placeholder="ABC123" required>
          </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">Sign In</button>
        <div class="auth-actions">
          <a href="../index.php" class="small-link">Back to Landing</a>
          <a href="#" class="small-link">Need access?</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
