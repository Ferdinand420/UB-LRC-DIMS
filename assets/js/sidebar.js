// Sidebar collapse functionality
// Apply saved state immediately to prevent flash
(function() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar && localStorage.getItem('sidebarCollapsed') === 'true') {
        sidebar.classList.add('collapsed', 'no-transition');
    }
})();

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('sidebar-toggle');
    
    if (!sidebar || !toggle) return;
    
    // Remove no-transition class after a short delay to enable smooth transitions
    setTimeout(() => {
        sidebar.classList.remove('no-transition');
    }, 50);
    
    // Toggle sidebar on button click
    toggle.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        const collapsed = sidebar.classList.contains('collapsed');
        localStorage.setItem('sidebarCollapsed', collapsed);
    });
});
