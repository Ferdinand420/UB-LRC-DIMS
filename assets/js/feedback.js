// Feedback page functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('feedback-form');
    const messageDiv = document.getElementById('feedback-message');
    const tableContainer = document.getElementById('feedback-container');
    const loadingElement = document.getElementById('feedback-loading');
    const noDataElement = document.getElementById('no-feedback');

    // Load existing feedback
    loadFeedback();

    // Handle form submission
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const feedbackText = document.getElementById('feedback-text').value;

            try {
                const response = await fetch('../api/submit_feedback.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ message: feedbackText })
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
                console.error('Error:', error);
            }
        });
    }

    async function loadFeedback() {
        try {
            const response = await fetch('../api/get_feedback.php');
            const data = await response.json();

            if (data.success && data.feedback) {
                displayFeedback(data.feedback);
            }
        } catch (error) {
            console.error('Error loading feedback:', error);
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
        const showStudent = table.querySelector('thead th:first-child').textContent === 'Student';

        feedbackList.forEach(feedback => {
            const row = document.createElement('tr');

            if (showStudent) {
                const studentCell = document.createElement('td');
                studentCell.textContent = feedback.user_name || feedback.user_email;
                row.appendChild(studentCell);
            }

            const messageCell = document.createElement('td');
            messageCell.textContent = feedback.message;
            messageCell.style.maxWidth = '400px';
            row.appendChild(messageCell);

            const statusCell = document.createElement('td');
            const statusBadge = document.createElement('span');
            statusBadge.className = `status-badge status-${feedback.status}`;
            statusBadge.textContent = feedback.status.charAt(0).toUpperCase() + feedback.status.slice(1);
            statusCell.appendChild(statusBadge);
            row.appendChild(statusCell);

            const dateCell = document.createElement('td');
            dateCell.textContent = formatDateTime(feedback.created_at);
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
