<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Feedback - DIMS</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>UB LRC-DIMS</h2>
      <nav>
        <a href="dashboard.php">Dashboard</a>      
        <a href="reservations.php">Reservations</a> 
        <a href="feedback.php">Feedback</a>         
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <h1>Feedback</h1>
      </header>

      <!-- Feedback Form Card -->
      <div class="card">
        <h3>Submit Feedback</h3>
        <form id="feedback-form">
          <input type="text" id="user-name" placeholder="Your Name" required>
          <textarea id="feedback-text" placeholder="Write your feedback..." rows="4" required></textarea>
          <button type="submit">Submit Feedback</button>
        </form>
      </div>

      <!-- Feedback List Card -->
      <div class="card" style="margin-top: 2rem;">
        <h3>All Feedback</h3>
        <ul id="feedback-list">
          <!-- Populated via script.js -->
        </ul>
      </div>
    </main>
  </div>

  <script src="assets/js/script.js"></script>
</body>
</html>
