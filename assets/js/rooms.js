// Rooms page functionality
document.addEventListener('DOMContentLoaded', function() {
    const loadingElement = document.getElementById('rooms-loading');
    const gridElement = document.getElementById('rooms-grid');
    const noDataElement = document.getElementById('no-rooms');
    const statusContainer = document.getElementById('rooms-container');

    // Load rooms status table
    loadRoomsStatus();

    // Add room form removed

    function formatRemaining(seconds) {
        if (seconds == null) return '-';
        const m = Math.floor(seconds / 60);
        const h = Math.floor(m / 60);
        const mins = m % 60;
        return h > 0 ? `${h}h ${mins}m` : `${mins}m`;
    }

    async function loadRoomsStatus() {
        try {
            const response = await fetch('../api/get_room_status.php');
            const data = await response.json();

            loadingElement.style.display = 'none';

            if (!data.success) {
                if (statusContainer) statusContainer.textContent = 'Failed to load rooms.';
                return;
            }

            const rooms = data.rooms || [];
            if (rooms.length === 0) {
                if (noDataElement) noDataElement.style.display = 'block';
                if (gridElement) gridElement.style.display = 'none';
                if (statusContainer) statusContainer.innerHTML = '';
                return;
            }

            if (noDataElement) noDataElement.style.display = 'none';
            if (gridElement) gridElement.style.display = 'none';

            if (statusContainer) {
                statusContainer.innerHTML = '';
                const table = document.createElement('table');
                table.className = 'table rooms-status-table';
                table.innerHTML = `
                    <thead>
                        <tr>
                            <th>Room</th>
                            <th>Status</th>
                            <th>Current User</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Remaining</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                `;
                const tbody = table.querySelector('tbody');

                rooms.forEach(r => {
                    const tr = document.createElement('tr');
                    const occupied = !!r.reservation_id;
                    const statusLabel = r.room_status === 'maintenance' ? 'Maintenance' : (occupied ? 'Occupied' : 'Available');
                    const user = occupied ? (r.user_name || r.user_email || 'Unknown') : '-';
                    const start = occupied ? r.start_time : '-';
                    const end = occupied ? r.end_time : '-';
                    const remain = formatRemaining(r.remaining_seconds);

                    tr.innerHTML = `
                        <td>${escapeHtml(r.room_name)}</td>
                        <td>${statusLabel}</td>
                        <td>${escapeHtml(user)}</td>
                        <td>${start}</td>
                        <td>${end}</td>
                        <td>${remain}</td>
                        <td style="text-align:right">
                            <button class="btn btn-sm" data-action="complete" data-reservation="${r.reservation_id || ''}" ${occupied ? '' : 'disabled'}>Mark Completed</button>
                            <button class="btn btn-sm" data-action="waitlist" data-room="${r.room_id}">View Waitlist (${r.waitlist_count})</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                statusContainer.appendChild(table);
            }
        } catch (error) {
            if (statusContainer) statusContainer.textContent = 'Error loading rooms.';
        }
    }

    // Message UI removed with add room form

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // actions
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button[data-action]');
        if (!btn) return;
        const action = btn.getAttribute('data-action');
        if (action === 'complete') {
            const resId = btn.getAttribute('data-reservation');
            if (!resId) return;
            if (!confirm('Mark this reservation as completed?')) return;
            fetch('../api/update_reservation_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ reservation_id: resId, status: 'completed' })
            }).then(r => r.json()).then(j => {
                if (j.success) {
                    loadRoomsStatus();
                } else {
                    alert(j.message || 'Failed to update');
                }
            }).catch(() => alert('Failed to update'));
        } else if (action === 'waitlist') {
            const roomId = btn.getAttribute('data-room');
            window.location.href = `../pages/reservations.php?room=${roomId}&tab=waitlist`;
        }
    });
});
