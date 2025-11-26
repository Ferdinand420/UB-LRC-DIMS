// Rooms page functionality
document.addEventListener('DOMContentLoaded', function() {
    const addForm = document.getElementById('add-room-form');
    const messageDiv = document.getElementById('room-message');
    const loadingElement = document.getElementById('rooms-loading');
    const gridElement = document.getElementById('rooms-grid');
    const noDataElement = document.getElementById('no-rooms');

    // Load rooms
    loadRooms();

    // Handle add room form (librarians only)
    if (addForm) {
        addForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('room-name').value,
                capacity: parseInt(document.getElementById('room-capacity').value),
                status: document.getElementById('room-status').value,
                description: document.getElementById('room-description').value
            };

            try {
                const response = await fetch('../api/add_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    addForm.reset();
                    loadRooms(); // Refresh the list
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Error adding room. Please try again.', 'error');
                console.error('Error:', error);
            }
        });
    }

    async function loadRooms() {
        try {
            const response = await fetch('../api/get_rooms.php');
            const data = await response.json();

            if (data.success && data.rooms) {
                displayRooms(data.rooms);
            }
        } catch (error) {
            console.error('Error loading rooms:', error);
            loadingElement.textContent = 'Error loading rooms.';
        }
    }

    function displayRooms(rooms) {
        loadingElement.style.display = 'none';

        if (rooms.length === 0) {
            noDataElement.style.display = 'block';
            gridElement.style.display = 'none';
            return;
        }

        noDataElement.style.display = 'none';
        gridElement.style.display = 'grid';
        gridElement.innerHTML = '';

        rooms.forEach(room => {
            const roomCard = document.createElement('div');
            roomCard.className = 'room-card';
            roomCard.innerHTML = `
                <div class="room-card-header">
                    <h4 style="margin: 0; color: var(--color-primary);">${escapeHtml(room.name)}</h4>
                    <span class="status-badge status-${room.status}">${room.status === 'available' ? 'Available' : 'Maintenance'}</span>
                </div>
                <div class="room-card-body">
                    <p style="margin: 0.5rem 0; font-size: 0.875rem;">
                        <strong>Capacity:</strong> ${room.capacity} persons
                    </p>
                    ${room.description ? `<p style="margin: 0.5rem 0; font-size: 0.813rem; color: #666;">${escapeHtml(room.description)}</p>` : ''}
                </div>
            `;

            gridElement.appendChild(roomCard);
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

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
