// Reservations page functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reservation-form');
    const roomSelect = document.getElementById('room');
    const dateInput = document.getElementById('date');
    const messageDiv = document.getElementById('form-message');
    const tableElement = document.getElementById('reservation-table');
    const loadingElement = document.getElementById('reservations-loading');
    const noDataElement = document.getElementById('no-reservations');

    // Set minimum date to today
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);
    }

    // Load available rooms
    if (roomSelect) {
        loadRooms();
    }

    // Load existing reservations
    loadReservations();

    // Handle form submission
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                room_id: parseInt(document.getElementById('room').value),
                reservation_date: document.getElementById('date').value,
                start_time: document.getElementById('start-time').value,
                end_time: document.getElementById('end-time').value,
                purpose: document.getElementById('purpose').value
            };

            try {
                const response = await fetch('../api/create_reservation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    loadReservations(); // Refresh the list
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Error submitting reservation. Please try again.', 'error');
                console.error('Error:', error);
            }
        });
    }

    async function loadRooms() {
        try {
            const response = await fetch('../api/get_rooms.php');
            const data = await response.json();

            if (data.success && data.rooms) {
                roomSelect.innerHTML = '<option value="">Select a room...</option>';
                data.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = `${room.name} (Capacity: ${room.capacity})`;
                    if (room.status === 'maintenance') {
                        option.disabled = true;
                        option.textContent += ' - Under Maintenance';
                    }
                    roomSelect.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error loading rooms:', error);
        }
    }

    async function loadReservations() {
        try {
            const response = await fetch('../api/get_reservations.php');
            const data = await response.json();

            if (data.success && data.reservations) {
                displayReservations(data.reservations);
            }
        } catch (error) {
            console.error('Error loading reservations:', error);
            loadingElement.textContent = 'Error loading reservations.';
        }
    }

    function displayReservations(reservations) {
        loadingElement.style.display = 'none';

        if (reservations.length === 0) {
            noDataElement.style.display = 'block';
            tableElement.style.display = 'none';
            return;
        }

        noDataElement.style.display = 'none';
        tableElement.style.display = 'table';

        const tbody = tableElement.querySelector('tbody');
        tbody.innerHTML = '';

        reservations.forEach(reservation => {
            const row = document.createElement('tr');
            
            // Check if we're showing student column (librarian view)
            const showStudent = tableElement.querySelector('thead th:first-child').textContent === 'Student';
            
            if (showStudent) {
                const studentCell = document.createElement('td');
                studentCell.textContent = reservation.user_name || reservation.user_email;
                row.appendChild(studentCell);
            }

            const roomCell = document.createElement('td');
            roomCell.textContent = reservation.room_name;
            row.appendChild(roomCell);

            const dateCell = document.createElement('td');
            dateCell.textContent = formatDate(reservation.reservation_date);
            row.appendChild(dateCell);

            const timeCell = document.createElement('td');
            timeCell.textContent = `${formatTime(reservation.start_time)} - ${formatTime(reservation.end_time)}`;
            row.appendChild(timeCell);

            const purposeCell = document.createElement('td');
            purposeCell.textContent = reservation.purpose || 'N/A';
            purposeCell.style.maxWidth = '200px';
            purposeCell.style.overflow = 'hidden';
            purposeCell.style.textOverflow = 'ellipsis';
            purposeCell.style.whiteSpace = 'nowrap';
            row.appendChild(purposeCell);

            const statusCell = document.createElement('td');
            const statusBadge = document.createElement('span');
            statusBadge.className = `status-badge status-${reservation.status}`;
            statusBadge.textContent = reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1);
            statusCell.appendChild(statusBadge);
            row.appendChild(statusCell);

            tbody.appendChild(row);
        });
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
