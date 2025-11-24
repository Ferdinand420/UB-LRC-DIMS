// ===== Reservations =====
const reservationForm = document.getElementById('reservation-form');
const reservationTable = document.getElementById('reservation-table')?.getElementsByTagName('tbody')[0];

// Dashboard stats elements
const totalEl = document.getElementById('total-reservations');
const pendingEl = document.getElementById('pending-reservations');
const approvedEl = document.getElementById('approved-reservations');
const rejectedEl = document.getElementById('rejected-reservations');

async function loadReservations() {
    if (!reservationTable) return;
    const res = await fetch('/api/reservations');
    const reservations = await res.json();
    reservationTable.innerHTML = '';

    let total = 0, pending = 0, approved = 0, rejected = 0;

    reservations.forEach((r, idx) => {
        total++;
        if (r.status === 'Pending') pending++;
        else if (r.status === 'Approved') approved++;
        else if (r.status === 'Rejected') rejected++;

        const row = reservationTable.insertRow();
        row.insertCell(0).textContent = r.name;
        row.insertCell(1).textContent = r.resource;
        row.insertCell(2).textContent = r.date;

        const statusCell = row.insertCell(3);
        const status = r.status || 'Pending';
        statusCell.innerHTML = `<span class="status ${status}">${status}</span>`;

        const actionCell = row.insertCell(4);
        actionCell.innerHTML = `
            <button class="approve-btn">Approve</button>
            <button class="reject-btn">Reject</button>
        `;

        actionCell.querySelector('.approve-btn').addEventListener('click', async () => {
            r.status = 'Approved';
            await updateReservations(reservations);
        });

        actionCell.querySelector('.reject-btn').addEventListener('click', async () => {
            r.status = 'Rejected';
            await updateReservations(reservations);
        });
    });

    // Update dashboard stats if present
    if (totalEl) totalEl.textContent = total;
    if (pendingEl) pendingEl.textContent = pending;
    if (approvedEl) approvedEl.textContent = approved;
    if (rejectedEl) rejectedEl.textContent = rejected;
}

async function updateReservations(reservations) {
    await fetch('/api/reservations/update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(reservations)
    });
    loadReservations();
}

if (reservationForm) {
    reservationForm.addEventListener('submit', async e => {
        e.preventDefault();
        const data = {
            name: document.getElementById('name').value,
            resource: document.getElementById('resource').value,
            date: document.getElementById('date').value,
            status: 'Pending'
        };
        await fetch('/api/reservations', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        reservationForm.reset();
        loadReservations();
    });
}

loadReservations();

// ===== Feedback =====
const feedbackForm = document.getElementById('feedback-form');
const feedbackList = document.getElementById('feedback-list');

async function loadFeedback() {
    if (!feedbackList) return;
    const res = await fetch('/api/feedback');
    const feedback = await res.json();
    feedbackList.innerHTML = '';
    feedback.forEach(f => {
        const li = document.createElement('li');
        li.textContent = `${f.name}: ${f.text}`;
        feedbackList.appendChild(li);
    });
}

if (feedbackForm) {
    feedbackForm.addEventListener('submit', async e => {
        e.preventDefault();
        const data = {
            name: document.getElementById('user-name').value,
            text: document.getElementById('feedback-text').value
        };
        await fetch('/api/feedback', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        feedbackForm.reset();
        loadFeedback();
    });
}

loadFeedback();
