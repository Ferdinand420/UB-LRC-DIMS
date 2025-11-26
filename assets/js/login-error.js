// Auto-open login modal if there's an error parameter
(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const role = urlParams.get('role');
    
    if (error) {
        // Determine which modal to open based on role or default to student
        const modalId = role === 'librarian' ? 'librarian-modal' : 'student-modal';
        const modal = document.getElementById(modalId);
        
        if (modal) {
            // Small delay to ensure page is loaded
            setTimeout(() => {
                modal.classList.add('active');
                modal.setAttribute('aria-hidden', 'false');
            }, 100);
        }
    }
})();
