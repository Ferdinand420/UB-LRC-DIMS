// ===== Dashboard Stats =====
async function loadDashboardStats() {
    // Fetch reservations
    const res = await fetch('/api/reservations');
    const reservations = await res.json();

    const total = reservations.length;
    const pending = reservations.filter(r => r.status === 'Pending').length;
    const approved = reservations.filter(r => r.status === 'Approved').length;

    document.getElementById('total-reservations').textContent = total;
    document.getElementById('pending-reservations').textContent = pending;
    document.getElementById('approved-reservations').textContent = approved;

    // Fetch feedback
    const fbRes = await fetch('/api/feedback');
    const feedback = await fbRes.json();
    document.getElementById('total-feedback').textContent = feedback.length;
}

// Load stats on page load
loadDashboardStats();
