// Approvals page functionality (Librarian only)
document.addEventListener('DOMContentLoaded', function() {
    const messageDiv = document.getElementById('approvals-message');
    const loadingElement = document.getElementById('approvals-loading');
    const containerElement = document.getElementById('approvals-container');
    const noDataElement = document.getElementById('no-approvals');
    const tbody = document.getElementById('approvals-tbody');

    // Load pending reservations
    loadPendingReservations();

    async function loadPendingReservations() {
        try {
            const response = await fetch('../api/get_pending_reservations.php');
            const data = await response.json();

            if (data.success && data.reservations) {
                displayReservations(data.reservations);
            }
        } catch (error) {
            loadingElement.textContent = 'Error loading reservations.';
        }
    }

    function displayReservations(reservations) {
        loadingElement.style.display = 'none';

        if (reservations.length === 0) {
            noDataElement.style.display = 'block';
            containerElement.style.display = 'none';
            return;
        }

        noDataElement.style.display = 'none';
        containerElement.style.display = 'block';

        tbody.innerHTML = '';

        reservations.forEach(reservation => {
            const row = document.createElement('tr');
            row.id = `reservation-${reservation.id}`;

            const studentCell = document.createElement('td');
            studentCell.innerHTML = `<strong>${reservation.user_name}</strong><br><small style="color:#666;">${reservation.user_email}</small>`;
            row.appendChild(studentCell);

            const roomCell = document.createElement('td');
            roomCell.textContent = reservation.room_name;
            row.appendChild(roomCell);

            const dateCell = document.createElement('td');
            dateCell.textContent = formatDate(reservation.reservation_date);
            dateCell.style.whiteSpace = 'nowrap';
            row.appendChild(dateCell);

            const timeCell = document.createElement('td');
            timeCell.textContent = `${formatTime(reservation.start_time)} - ${formatTime(reservation.end_time)}`;
            timeCell.style.whiteSpace = 'nowrap';
            row.appendChild(timeCell);

            const purposeCell = document.createElement('td');
            purposeCell.textContent = reservation.purpose || 'N/A';
            purposeCell.style.maxWidth = '200px';
            purposeCell.style.overflow = 'hidden';
            purposeCell.style.textOverflow = 'ellipsis';
            row.appendChild(purposeCell);

            const actionCell = document.createElement('td');
            actionCell.style.whiteSpace = 'nowrap';

            const approveBtn = document.createElement('button');
            approveBtn.className = 'approve-btn';
            approveBtn.textContent = 'Approve';
            approveBtn.onclick = () => handleAction(reservation.id, 'approve');

            const rejectBtn = document.createElement('button');
            rejectBtn.className = 'reject-btn';
            rejectBtn.textContent = 'Reject';
            rejectBtn.style.marginLeft = '0.5rem';
            rejectBtn.onclick = () => handleAction(reservation.id, 'reject');

            actionCell.appendChild(approveBtn);
            actionCell.appendChild(rejectBtn);
            row.appendChild(actionCell);

            tbody.appendChild(row);
        });
    }

    async function handleAction(reservationId, action) {
        const actionText = action === 'approve' ? 'approve' : 'reject';
        
        if (!confirm(`Are you sure you want to ${actionText} this reservation?`)) {
            return;
        }

        try {
            const response = await fetch('../api/update_reservation_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    reservation_id: reservationId,
                    action: action
                })
            });

            const data = await response.json();

            if (data.success) {
                showMessage(data.message, 'success');
                
                // Remove the row from the table
                const row = document.getElementById(`reservation-${reservationId}`);
                if (row) {
                    row.style.transition = 'opacity 0.3s';
                    row.style.opacity = '0';
                    setTimeout(() => {
                        row.remove();
                        
                        // Check if table is now empty
                        if (tbody.children.length === 0) {
                            containerElement.style.display = 'none';
                            noDataElement.style.display = 'block';
                        }
                    }, 300);
                }
            } else {
                showMessage(data.message, 'error');
            }
        } catch (error) {
            showMessage('Error processing request. Please try again.', 'error');
        }
    }

    function showMessage(message, type) {
        if (!messageDiv) return;
        
        messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
        messageDiv.textContent = message;
        messageDiv.style.display = 'block';

        setTimeout(() => {
            messageDiv.style.display = 'none';
        }, 5000);
    }

    function formatDate(dateString) {
        const date = new Date(dateString + 'T00:00:00');
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function formatTime(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        return `${hour12}:${minutes} ${ampm}`;
    }
});
