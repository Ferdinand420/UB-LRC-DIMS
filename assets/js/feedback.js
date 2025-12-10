// Feedback page functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feedback-form');
    const messageDiv = document.getElementById('feedback-message');
    const tableContainer = document.getElementById('feedback-container');
    const loadingElement = document.getElementById('feedback-loading');
    const noDataElement = document.getElementById('no-feedback');
    const roomSelect = document.getElementById('room-select');
    const isLibrarian = (window.USER_ROLE === 'librarian');

    // Load existing feedback
    loadFeedback();
    if (roomSelect) loadRooms();

    // Handle form submission (students only - form won't exist for librarians)
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const conditionStatus = document.getElementById('condition-status').value;
            const feedbackText = document.getElementById('feedback-text').value;
            const roomIdVal = roomSelect && roomSelect.value ? parseInt(roomSelect.value, 10) : null;

            if (!roomIdVal || Number.isNaN(roomIdVal)) {
                showMessage('Please select a room.', 'error');
                return;
            }

            try {
                const response = await fetch('../api/submit_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ 
                        condition_status: conditionStatus,
                        feedback_text: feedbackText,
                        room_id: roomIdVal
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(data.message, 'success');
                    form.reset();
                    loadFeedback(); // Refresh the list
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                showMessage('Error submitting feedback. Please try again.', 'error');
            }
        });
    }

    async function loadFeedback() {
        try {
            const response = await fetch('../api/get_feedback.php');
            const data = await response.json();

            if (!response.ok || !data.success || !Array.isArray(data.feedback)) {
                throw new Error(data.message || 'Failed to load feedback');
            }

            displayFeedback(data.feedback);
        } catch (error) {
            loadingElement.textContent = 'Error loading feedback.';
        }
    }

    function displayFeedback(feedbackList) {
        loadingElement.style.display = 'none';

        if (feedbackList.length === 0) {
            noDataElement.style.display = 'block';
            tableContainer.style.display = 'none';
            return;
        }

        noDataElement.style.display = 'none';
        tableContainer.style.display = 'block';

        const tbody = document.querySelector('#feedback-table tbody');
        tbody.innerHTML = '';

        const table = document.getElementById('feedback-table');
        const showStudent = isLibrarian;

        feedbackList.forEach(feedback => {
            const row = document.createElement('tr');

            if (showStudent) {
                const studentCell = document.createElement('td');
                studentCell.textContent = feedback.user_name || feedback.user_email;
                row.appendChild(studentCell);
            }

            const roomCell = document.createElement('td');
            roomCell.textContent = feedback.room_name || '—';
            row.appendChild(roomCell);

            // Add condition status cell
            const conditionCell = document.createElement('td');
            const conditionBadge = document.createElement('span');
            const conditionStatus = feedback.condition_status || 'unknown';
            const conditionColors = {
                'clean': '#28a745',
                'dirty': '#ffc107',
                'damaged': '#dc3545'
            };
            conditionBadge.style.backgroundColor = conditionColors[conditionStatus] || '#6c757d';
            conditionBadge.style.color = 'white';
            conditionBadge.style.padding = '0.25rem 0.75rem';
            conditionBadge.style.borderRadius = '4px';
            conditionBadge.style.fontSize = '0.875rem';
            conditionBadge.textContent = conditionStatus.charAt(0).toUpperCase() + conditionStatus.slice(1);
            conditionCell.appendChild(conditionBadge);
            row.appendChild(conditionCell);

            const feedbackCell = document.createElement('td');
            feedbackCell.textContent = feedback.feedback_text || feedback.message || '';
            feedbackCell.style.maxWidth = '400px';
            row.appendChild(feedbackCell);

            // Status cell (reviewed or pending)
            const statusCell = document.createElement('td');
            const statusBadge = document.createElement('span');
            const reviewed = !!feedback.reviewed_at || feedback.status === 'reviewed' || feedback.status === 'resolved';
            statusBadge.textContent = reviewed ? 'Reviewed' : 'Pending';
            statusBadge.className = reviewed ? 'status-badge status-approved' : 'status-badge status-pending';
            statusCell.appendChild(statusBadge);
            row.appendChild(statusCell);

            const dateCell = document.createElement('td');
            dateCell.textContent = formatDateTime(feedback.created_at);
            dateCell.style.whiteSpace = 'nowrap';
            row.appendChild(dateCell);

            // Librarian action
            if (isLibrarian) {
                const actionCell = document.createElement('td');
                actionCell.style.textAlign = 'right';
                if (reviewed) {
                    const reviewedText = document.createElement('span');
                    reviewedText.style.color = '#0f766e';
                    reviewedText.style.fontWeight = '600';
                    reviewedText.textContent = 'Received';
                    actionCell.appendChild(reviewedText);
                } else {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-primary';
                    btn.textContent = 'Receive';
                    btn.style.padding = '0.35rem 0.75rem';
                    btn.addEventListener('click', () => handleReceive(feedback.id, btn));
                    actionCell.appendChild(btn);
                }
                row.appendChild(actionCell);
            }

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
        if (!dateTimeString) return '—';
        const date = new Date(dateTimeString);
        if (isNaN(date.getTime())) return '—';
        return date.toLocaleString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
    }

    async function handleReceive(feedbackId, btn) {
        if (!confirm('Mark this feedback as received?')) return;
        btn.disabled = true;
        try {
            const res = await fetch('../api/acknowledge_feedback.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ feedback_id: feedbackId })
            });
            const data = await res.json();
            if (data.success) {
                loadFeedback();
            } else {
                alert(data.message || 'Failed to update feedback');
                btn.disabled = false;
            }
        } catch (e) {
            alert('Failed to update feedback');
            btn.disabled = false;
        }
    }

    async function loadRooms() {
        try {
            const res = await fetch('../api/get_rooms.php');
            const data = await res.json();
            if (!data.success || !Array.isArray(data.rooms)) return;
            // keep placeholder
            data.rooms.forEach(r => {
                const opt = document.createElement('option');
                opt.value = r.id;
                opt.textContent = r.name;
                roomSelect.appendChild(opt);
            });
        } catch (e) {
            // silent fail
        }
    }
});
