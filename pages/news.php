<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>News & Updates - UB LRC-DIMS</title>
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
  <main class="main-content" style="background:#fff; margin:2rem auto; max-width:1180px; border-radius:12px; padding:2rem; box-shadow:0 4px 18px rgba(0,0,0,0.08); display:flex; flex-direction:column; min-height:400px;">
    <h1 style="margin-top:0;">News & Updates</h1>
    <div id="news-container" style="margin-top:1.75rem; flex:1;">
      <!-- News content will be populated here -->
    </div>
    <div style="margin-top:1.5rem; text-align:center;">
      <a href="../index.php" class="btn btn-primary">Back to Home</a>
    </div>
  </main>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const newsContainer = document.getElementById('news-container');
      // Placeholder library news items (static for now)
      const news = [
        {
          title: 'Extended Library Hours for Finals Week',
          date: 'December 12, 2025',
          summary: 'LRC open 7:00 AM – 7:00 PM from Dec 12-20 to support review sessions.',
          bullet: 'Quiet zones enforced; group rooms by reservation only.'
        },
        {
          title: 'New Group Study Policy',
          date: 'December 5, 2025',
          summary: 'Reservations must include all student IDs to reduce no-shows.',
          bullet: ''
        }
      ];
      
      if (news.length === 0) {
        newsContainer.innerHTML = '<div style="text-align:center; padding:2rem; color:#999;"><p style="font-size:1.1rem;">There is no news.</p></div>';
        return;
      }

      const newsGrid = document.createElement('section');
      newsGrid.style.cssText = 'display:grid; gap:1.5rem; grid-template-columns:repeat(auto-fit,minmax(260px,1fr));';
      news.forEach(item => {
        const article = document.createElement('article');
        article.className = 'card';
        article.style.textAlign = 'left';
        article.innerHTML = `
          <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.5rem;">
            <h3 style="margin:0;">${item.title}</h3>
            <span style="font-size:0.85rem; color:#7d0920; font-weight:700; white-space:nowrap;">${item.date}</span>
          </div>
          <p style="font-weight:400; margin:0.5rem 0 0.75rem; color:#444;">${item.summary}</p>
          ${item.bullet ? `<p style="margin:0; color:#666; font-size:0.95rem;">• ${item.bullet}</p>` : ''}
        `;
        newsGrid.appendChild(article);
      });
      newsContainer.appendChild(newsGrid);
    });
  </script>
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
