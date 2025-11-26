<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rooms - DIMS</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('rooms'); ?>
    <main class="main-content">
      <header>
        <h1>Rooms</h1>
        <p style="margin:0; font-size:.75rem; color:#555; font-weight:600;">Role: <?php echo htmlspecialchars(get_role()); ?></p>
      </header>

      <?php if (is_librarian()): ?>
      <!-- Room Management (Librarians Only) -->
      <div class="card">
        <h3>Add New Room</h3>
        <div id="room-message"></div>
        <form id="add-room-form">
          <label for="room-name">Room Name</label>
          <input type="text" id="room-name" name="name" placeholder="e.g., Room A" required>

          <label for="room-capacity">Capacity</label>
          <input type="number" id="room-capacity" name="capacity" placeholder="e.g., 8" min="1" required>

          <label for="room-status">Status</label>
          <select id="room-status" name="status" required>
            <option value="available">Available</option>
            <option value="maintenance">Under Maintenance</option>
          </select>

          <label for="room-description">Description (Optional)</label>
          <textarea id="room-description" name="description" rows="2" placeholder="Brief description..."></textarea>

          <button type="submit" class="btn btn-primary">Add Room</button>
        </form>
      </div>
      <?php endif; ?>

      <div class="card" style="margin-top: 2rem;">
        <h3>Available Rooms</h3>
        <div id="rooms-loading" style="text-align: center; padding: 2rem; color: #999;">
          Loading rooms...
        </div>
        <div id="rooms-grid" style="display: none; display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
          <!-- Populated via JavaScript -->
        </div>
        <div id="no-rooms" style="display: none; text-align: center; padding: 2rem; color: #999;">
          No rooms available.
        </div>
      </div>
    </main>
  </div>
  <footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
  <script src="../assets/js/rooms.js"></script>
</body>
</html>
