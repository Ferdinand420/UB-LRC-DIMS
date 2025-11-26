<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
if (!is_librarian()) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reports - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('reports'); ?>
    <main class="main-content">
      <header>
        <h1>Usage Reports</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      <!-- Date Range Filter -->
      <div class="card">
        <h3>Report Period</h3>
        <div style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
          <div style="flex: 1; min-width: 200px;">
            <label for="start-date">Start Date</label>
            <input type="date" id="start-date" value="<?php echo date('Y-m-01'); ?>">
          </div>
          <div style="flex: 1; min-width: 200px;">
            <label for="end-date">End Date</label>
            <input type="date" id="end-date" value="<?php echo date('Y-m-d'); ?>">
          </div>
          <button onclick="loadReports()" class="btn btn-primary">Generate Report</button>
        </div>
      </div>

      <div id="report-loading" style="text-align: center; padding: 3rem; color: #999;">
        Loading report data...
      </div>

      <div id="report-container" style="display: none;">
        <!-- Summary Stats -->
        <div class="stats-grid" style="margin-top: 1.5rem;">
          <div class="card">
            <h3>Total Reservations</h3>
            <p id="stat-total-reservations">0</p>
          </div>
          <div class="card">
            <h3>Approved</h3>
            <p id="stat-approved" style="color: #28a745;">0</p>
          </div>
          <div class="card">
            <h3>Pending</h3>
            <p id="stat-pending" style="color: #ffc107;">0</p>
          </div>
          <div class="card">
            <h3>Violations</h3>
            <p id="stat-violations" style="color: #dc3545;">0</p>
          </div>
        </div>

        <!-- Room Utilization -->
        <div class="card" style="margin-top: 1.5rem;">
          <h3>Room Utilization</h3>
          <div id="room-utilization-chart" style="margin-top: 1rem;"></div>
        </div>

        <!-- Peak Hours -->
        <div class="card" style="margin-top: 1.5rem;">
          <h3>Peak Reservation Hours</h3>
          <div id="peak-hours-list" style="margin-top: 1rem;"></div>
        </div>

        <!-- Top Users -->
        <div class="card" style="margin-top: 1.5rem;">
          <h3>Most Active Students</h3>
          <table id="top-users-table" style="display: none;">
            <thead>
              <tr>
                <th>Rank</th>
                <th>Student</th>
                <th>Reservations</th>
              </tr>
            </thead>
            <tbody id="top-users-tbody"></tbody>
          </table>
          <div id="no-users" style="text-align: center; padding: 2rem; color: #999;">No data available.</div>
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/sidebar.js"></script>
  <script src="../assets/js/reports.js"></script>
</body>
</html>
