<?php
require_once __DIR__ . '/auth.php';
function render_sidebar(string $active): void {
  $linksStudent = [
    'dashboard' => 'Dashboard',
    'reservations' => 'Reservations',
    'rooms' => 'Rooms',
    'feedback' => 'Feedback',
    'history' => 'History',
    'profile' => 'Profile',
  ];
  $linksLibrarian = [
    'librarian' => 'Dashboard',
    'approvals' => 'Approvals',
    'rooms' => 'Rooms',
    'violations' => 'Violations',
    'reports' => 'Reports',
    'feedback' => 'Feedback',
    'history' => 'History',
  ];
  $set = is_librarian() ? $linksLibrarian : $linksStudent;
  echo '<aside class="sidebar"><nav>';
  foreach ($set as $key => $label) {
    $activeClass = $key === $active ? ' class="active"' : '';
    echo '<a href="' . $key . '.php"' . $activeClass . '>' . htmlspecialchars($label) . '</a>';
  }
  echo '</nav></aside>';
}
?>