const reservationForm = document.getElementById('reservation-form');
const reservationTable = document.getElementById('reservation-table')?.getElementsByTagName('tbody')[0];
const totalEl = document.getElementById('total-reservations');
const pendingEl = document.getElementById('pending-reservations');
const approvedEl = document.getElementById('approved-reservations');
const rejectedEl = document.getElementById('rejected-reservations');
const feedbackForm = document.getElementById('feedback-form');
const feedbackList = document.getElementById('feedback-list');

// Auto refresh config & controllers
const LIST_REFRESH_MS = 30000; // 30s
let reservationsController = null;
let feedbackController = null;

function updateText(el, value) {
    if (el) el.textContent = value;
}

async function fetchJSON(url, options) {
    const res = await fetch(url, options);
    if (!res.ok) throw new Error('Request failed: ' + url + ' ' + res.status);
    return res.json();
}

async function loadReservations() {
    if (!reservationTable) return;
    if (reservationsController) reservationsController.abort();
    reservationsController = new AbortController();
    const signal = reservationsController.signal;
    try {
        reservationTable.innerHTML = '';
        const loadingRow = reservationTable.insertRow();
        const loadingCell = loadingRow.insertCell(0);
        loadingCell.colSpan = 5;
        loadingCell.textContent = 'Loading...';
        const reservations = await fetchJSON('/api/reservations', { signal });
        reservationTable.innerHTML = '';
        let total = 0, pending = 0, approved = 0, rejected = 0;
        for (let i = 0; i < reservations.length; i++) {
            const r = reservations[i];
            total++;
            const s = r.status;
            if (s === 'Pending') pending++; else if (s === 'Approved') approved++; else if (s === 'Rejected') rejected++;
            const row = reservationTable.insertRow();
            row.insertCell(0).textContent = r.name;
            row.insertCell(1).textContent = r.resource;
            row.insertCell(2).textContent = r.date;
            const statusCell = row.insertCell(3);
            const status = s || 'Pending';
            statusCell.innerHTML = '<span class="status ' + status + '">' + status + '</span>';
            const actionCell = row.insertCell(4);
            actionCell.innerHTML = '<button class="approve-btn">Approve</button><button class="reject-btn">Reject</button>';
            const approveBtn = actionCell.querySelector('.approve-btn');
            const rejectBtn = actionCell.querySelector('.reject-btn');
            approveBtn.addEventListener('click', async () => {
                r.status = 'Approved';
                await updateReservations(reservations);
            });
            rejectBtn.addEventListener('click', async () => {
                r.status = 'Rejected';
                await updateReservations(reservations);
            });
        }
        updateText(totalEl, total);
        updateText(pendingEl, pending);
        updateText(approvedEl, approved);
        updateText(rejectedEl, rejected);
    } catch (e) {
        if (e.name === 'AbortError') return;
        updateText(totalEl, '0');
        updateText(pendingEl, '0');
        updateText(approvedEl, '0');
        updateText(rejectedEl, '0');
        reservationTable.innerHTML = '';
        const row = reservationTable.insertRow();
        const cell = row.insertCell(0);
        cell.colSpan = 5;
        cell.textContent = 'Failed to load reservations';
    }
}

async function updateReservations(reservations) {
    try {
        await fetchJSON('/api/reservations/update', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(reservations)
        });
        loadReservations();
    } catch (e) {
    }
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
        try {
            await fetchJSON('/api/reservations', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            reservationForm.reset();
            loadReservations();
        } catch (e) {
        }
    });
}

loadReservations();
setInterval(loadReservations, LIST_REFRESH_MS);

async function loadFeedback() {
    if (!feedbackList) return;
    if (feedbackController) feedbackController.abort();
    feedbackController = new AbortController();
    const signal = feedbackController.signal;
    try {
        feedbackList.innerHTML = '<li>Loading...</li>';
        const feedback = await fetchJSON('/api/feedback', { signal });
        feedbackList.innerHTML = '';
        for (let i = 0; i < feedback.length; i++) {
            const f = feedback[i];
            const li = document.createElement('li');
            li.textContent = f.name + ': ' + f.text;
            feedbackList.appendChild(li);
        }
    } catch (e) {
        if (e.name === 'AbortError') return;
        feedbackList.innerHTML = '<li>Failed to load feedback</li>';
    }
}

if (feedbackForm) {
    feedbackForm.addEventListener('submit', async e => {
        e.preventDefault();
        const data = {
            name: document.getElementById('user-name').value,
            text: document.getElementById('feedback-text').value
        };
        try {
            await fetchJSON('/api/feedback', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            feedbackForm.reset();
            loadFeedback();
        } catch (e) {
        }
    });
}

loadFeedback();
setInterval(loadFeedback, LIST_REFRESH_MS);
