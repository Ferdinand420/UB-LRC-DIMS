<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reservations - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <h2>UB LRC-DIMS</h2>
      <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="reservations.php" class="active">Reservations</a>
        <a href="feedback.php">Feedback</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header>
        <h1>Reservations</h1>
      </header>

      <!-- Reservation Form Card -->
      <div class="card">
        <h3>New Reservation</h3>
        <form id="reservation-form">
          <input type="text" id="name" placeholder="Your Name" required>
          <input type="text" id="resource" placeholder="Resource Name" required>
          <input type="date" id="date" required>
          <button type="submit">Submit Reservation</button>
        </form>
      </div>

      <!-- Reservations Table Card -->
      <div class="card" style="margin-top: 2rem;">
        <h3>All Reservations</h3>
        <table id="reservation-table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Resource</th>
              <th>Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Populated via script.js -->
          </tbody>
        </table>
      </div>
    </main>
  </div>

  <script src="../assets/js/script.js"></script>
</body>
</html>
