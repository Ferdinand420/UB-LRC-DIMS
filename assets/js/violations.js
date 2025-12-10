// Violations page functionality (Librarian only)
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('violation-form');
    const roomSelect = document.getElementById('violation-room');
    const messageDiv = document.getElementById('violation-message');
    const loadingElement = document.getElementById('violations-loading');
    const containerElement = document.getElementById('violations-container');
    const noDataElement = document.getElementById('no-violations');
    const tbody = document.getElementById('violations-tbody');

    // Load rooms for dropdown
    loadRooms();

    // Load violations
    loadViolations();

    // Handle form submission
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                student_email: document.getElementById('student-email').value,
                violation_type: document.getElementById('violation-type').value,
                room_id: document.getElementById('violation-room').value || null,
                description: document.getElementById('violation-description').value
            };

            try {
                const response = await fetch('../api/log_violation.php', {
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
                    loadViolations(); // Refresh the list
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Error logging violation. Please try again.', 'error');
            }
        });
    }

    async function loadRooms() {
        try {
            const response = await fetch('../api/get_rooms.php');
            const data = await response.json();

            if (data.success && data.rooms) {
                roomSelect.innerHTML = '<option value="">Select room (if applicable)...</option>';
                data.rooms.forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.id;
                    option.textContent = room.name;
                    roomSelect.appendChild(option);
                });
            }
        } catch (error) {
        }
    }

    async function loadViolations() {
        try {
            const response = await fetch('../api/get_violations.php');
            const data = await response.json();

            if (data.success && data.violations) {
                displayViolations(data.violations);
            }
        } catch (error) {
            loadingElement.textContent = 'Error loading violations.';
        }
    }

    function displayViolations(violations) {
        loadingElement.style.display = 'none';

        if (violations.length === 0) {
            noDataElement.style.display = 'block';
            containerElement.style.display = 'none';
            return;
        }

        noDataElement.style.display = 'none';
        containerElement.style.display = 'block';

        tbody.innerHTML = '';

        violations.forEach(violation => {
            const row = document.createElement('tr');

            const studentCell = document.createElement('td');
            studentCell.innerHTML = `<strong>${violation.student_name}</strong><br><small style="color:#666;">${violation.student_email}</small>`;
            row.appendChild(studentCell);

            const roomCell = document.createElement('td');
            roomCell.textContent = violation.room_name || 'N/A';
            row.appendChild(roomCell);

            const descCell = document.createElement('td');
            descCell.textContent = violation.description;
            descCell.style.maxWidth = '300px';
            row.appendChild(descCell);

            const loggedByCell = document.createElement('td');
            loggedByCell.textContent = violation.logged_by_name;
            row.appendChild(loggedByCell);

            const dateCell = document.createElement('td');
            dateCell.textContent = formatDateTime(violation.created_at);
            dateCell.style.whiteSpace = 'nowrap';
            row.appendChild(dateCell);

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

    function formatDateTime(dateTimeString) {
        const date = new Date(dateTimeString);
        return date.toLocaleString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }
});
