<?php
require_once __DIR__ . '/auth.php';
function render_sidebar(string $active): void {
  $linksStudent = [
    'dashboard' => ['label' => 'Dashboard', 'icon' => '📊'],
    'reservations' => ['label' => 'Reservations', 'icon' => '📅'],
    'rooms' => ['label' => 'Rooms', 'icon' => '🚪'],
    'feedback' => ['label' => 'Feedback', 'icon' => '💬'],
    'history' => ['label' => 'History', 'icon' => '📜'],
    'profile' => ['label' => 'Profile', 'icon' => '👤'],
  ];
  $linksLibrarian = [
    'librarian' => ['label' => 'Dashboard', 'icon' => '📊'],
    'approvals' => ['label' => 'Approvals', 'icon' => '✓'],
    'rooms' => ['label' => 'Rooms', 'icon' => '🚪'],
    'violations' => ['label' => 'Violations', 'icon' => '⚠'],
    'reports' => ['label' => 'Reports', 'icon' => '📈'],
    'feedback' => ['label' => 'Feedback', 'icon' => '💬'],
    'history' => ['label' => 'History', 'icon' => '📜'],
  ];
  $set = is_librarian() ? $linksLibrarian : $linksStudent;
  echo '<aside class="sidebar" id="sidebar">';
  echo '<button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">';
  echo '<span class="toggle-icon">☰</span>';
  echo '</button>';
  echo '<nav>';
  foreach ($set as $key => $item) {
    $activeClass = $key === $active ? ' class="active"' : '';
    echo '<a href="' . $key . '.php"' . $activeClass . '>';
    echo '<span class="nav-icon">' . $item['icon'] . '</span>';
    echo '<span class="nav-label">' . htmlspecialchars($item['label']) . '</span>';
    echo '</a>';
  }
  echo '</nav></aside>';
}
?>