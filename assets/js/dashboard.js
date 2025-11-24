const REFRESH_MS = 30000; // 30s auto refresh
let currentController = null;

async function loadDashboardStats() {
    if (currentController) {
        currentController.abort();
    }
    currentController = new AbortController();
    const signal = currentController.signal;
    try {
        // set temporary loading indicators (non-intrusive)
        loadingDots('total-reservations');
        loadingDots('pending-reservations');
        loadingDots('approved-reservations');
        loadingDots('total-feedback');
        const [resReservations, resFeedback] = await Promise.all([
            fetch('/api/reservations', { signal }),
            fetch('/api/feedback', { signal })
        ]);
        if (!resReservations.ok || !resFeedback.ok) throw new Error('Network error');
        const [reservations, feedback] = await Promise.all([
            resReservations.json(),
            resFeedback.json()
        ]);
        const total = reservations.length;
        let pending = 0, approved = 0;
        for (let i = 0; i < reservations.length; i++) {
            const s = reservations[i].status;
            if (s === 'Pending') pending++;
            else if (s === 'Approved') approved++;
        }
        updateText('total-reservations', total);
        updateText('pending-reservations', pending);
        updateText('approved-reservations', approved);
        updateText('total-feedback', feedback.length);
    } catch (e) {
        if (e.name === 'AbortError') {
            // silently ignore aborted fetch
            return;
        }
        updateText('total-reservations', '0');
        updateText('pending-reservations', '0');
        updateText('approved-reservations', '0');
        updateText('total-feedback', '0');
        console.error('loadDashboardStats failed', e);
    }
}

function updateText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function loadingDots(id) {
    const el = document.getElementById(id);
    if (el) el.textContent = '...';
}

loadDashboardStats();
setInterval(loadDashboardStats, REFRESH_MS);
