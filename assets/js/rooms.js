// Rooms page functionality
document.addEventListener('DOMContentLoaded', function() {
    const loadingElement = document.getElementById('rooms-loading');
    const noDataElement = document.getElementById('no-rooms');
    const statusContainer = document.getElementById('rooms-container');
    const isLibrarian = (window.USER_ROLE === 'librarian');
    const isStudent = (window.USER_ROLE === 'student');

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
                if (statusContainer) statusContainer.innerHTML = '';
                return;
            }

            if (noDataElement) noDataElement.style.display = 'none';

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
                        <td style="text-align:right; display:flex; gap:0.25rem; justify-content:flex-end; flex-wrap:wrap;">
                            ${isLibrarian ? `<button class="btn btn-sm" data-action="complete" data-reservation="${r.reservation_id || ''}" ${occupied ? '' : 'disabled'}>Mark Completed</button>` : ''}
                            ${isStudent ? `<button class="btn btn-sm" data-action="reserve" data-room="${r.room_id}">Reserve</button>` : ''}
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
            if (!isLibrarian) return;
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
        } else if (action === 'reserve') {
            if (!isStudent) return;
            const roomId = btn.getAttribute('data-room');
            if (!roomId) return;
            window.location.href = `reservations.php?room_id=${encodeURIComponent(roomId)}`;
        } else if (action === 'waitlist') {
            const roomId = btn.getAttribute('data-room');
            showWaitlistModal(roomId);
        }
    });

    async function showWaitlistModal(roomId) {
        try {
            const response = await fetch('../api/get_waitlist.php');
            const data = await response.json();

            if (!data.success || !data.waitlist) {
                alert('Failed to load waitlist');
                return;
            }

            // Filter waitlist for this room
            const roomWaitlist = data.waitlist.filter(w => parseInt(w.room_id) === parseInt(roomId));
            const roomName = roomWaitlist.length > 0 ? roomWaitlist[0].room_name : 'Room';

            let html = `<h3>${escapeHtml(roomName)} - Waitlist</h3>`;
            
            if (roomWaitlist.length === 0) {
                html += '<p style="color:#999;">No one on the waitlist for this room.</p>';
            } else {
                html += '<table style="width:100%; border-collapse:collapse;">';
                html += '<thead><tr><th>Student</th><th>Preferred Date</th><th>Preferred Time</th><th>Status</th></tr></thead><tbody>';
                roomWaitlist.forEach(entry => {
                    // Format time to 12-hour format
                    let formattedTime = entry.preferred_time || '-';
                    if (entry.preferred_time && entry.preferred_time !== '-') {
                        const [hours, minutes] = entry.preferred_time.split(':');
                        const hour = parseInt(hours);
                        const ampm = hour >= 12 ? 'PM' : 'AM';
                        const hour12 = hour === 0 ? 12 : hour > 12 ? hour - 12 : hour;
                        formattedTime = `${hour12}:${minutes} ${ampm}`;
                    }
                    
                    html += `<tr style="border-bottom:1px solid #eee;">`;
                    html += `<td>${escapeHtml(entry.full_name || entry.email)}</td>`;
                    html += `<td>${entry.preferred_date || '-'}</td>`;
                    html += `<td>${formattedTime}</td>`;
                    html += `<td>${entry.status || 'waiting'}</td>`;
                    html += `</tr>`;
                });
                html += '</tbody></table>';
            }

            // Create modal
            let modal = document.getElementById('waitlist-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'waitlist-modal';
                modal.style.cssText = 'display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;';
                document.body.appendChild(modal);
            }

            const content = modal.querySelector('.modal-content') || document.createElement('div');
            content.className = 'modal-content';
            content.style.cssText = 'background:white; padding:2rem; border-radius:8px; max-width:600px; max-height:80vh; overflow-y:auto;';
            content.innerHTML = html + '<button class="btn btn-primary" style="margin-top:1rem;" onclick="this.closest(\'#waitlist-modal\').style.display=\'none\';">Close</button>';
            
            modal.innerHTML = '';
            modal.appendChild(content);
            modal.style.display = 'flex';

            // Close on outside click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) modal.style.display = 'none';
            });
        } catch (error) {
            alert('Error loading waitlist');
        }
    }
});
