// Profile page functionality
let profileData = null;

async function loadProfile() {
    const loadingElement = document.getElementById('profile-loading');
    const infoElement = document.getElementById('profile-info');

    try {
        const response = await fetch('../api/get_profile.php');
        const data = await response.json();

        if (data.success) {
            profileData = data;
            displayProfile(data);
        } else {
            loadingElement.textContent = 'Error loading profile';
        }
    } catch (error) {
        loadingElement.textContent = 'Error loading profile';
    }
}

function displayProfile(data) {
    const loadingElement = document.getElementById('profile-loading');
    const infoElement = document.getElementById('profile-info');
    const activityElement = document.getElementById('recent-activity');
    const noActivityElement = document.getElementById('no-activity');

    loadingElement.style.display = 'none';
    infoElement.style.display = 'block';

    // Fill in user information
    document.getElementById('full-name').value = data.user.full_name || '';
    document.getElementById('user-email').textContent = data.user.email;
    document.getElementById('user-role').textContent = data.user.role.charAt(0).toUpperCase() + data.user.role.slice(1);
    document.getElementById('user-since').textContent = formatDate(data.user.created_at);

    // Display statistics
    document.getElementById('stat-total').textContent = data.stats.reservations.total || 0;
    document.getElementById('stat-approved').textContent = data.stats.reservations.approved || 0;
    document.getElementById('stat-feedback').textContent = data.stats.feedback_count || 0;
    document.getElementById('stat-violations').textContent = data.stats.violations_count || 0;

    // Display recent activity
    if (data.recent_reservations && data.recent_reservations.length > 0) {
        activityElement.innerHTML = '';
        data.recent_reservations.forEach(reservation => {
            const itemDiv = document.createElement('div');
            itemDiv.style.cssText = `
                padding: 1rem;
                background: #f9fafb;
                border-radius: var(--radius-md);
                margin-bottom: 0.75rem;
                border-left: 4px solid var(--color-primary);
            `;

            const statusBadge = `<span class="status-badge status-${reservation.status}">${reservation.status.charAt(0).toUpperCase() + reservation.status.slice(1)}</span>`;

            itemDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                    <strong>${reservation.room_name}</strong>
                    ${statusBadge}
                </div>
                <div style="font-size: 0.875rem; color: #666;">
                    ${formatDate(reservation.reservation_date)} at ${formatTime(reservation.start_time)}
                </div>
                <div style="font-size: 0.75rem; color: #999; margin-top: 0.25rem;">
                    Booked ${formatDateTime(reservation.created_at)}
                </div>
            `;

            activityElement.appendChild(itemDiv);
        });
    } else {
        activityElement.style.display = 'none';
        noActivityElement.style.display = 'block';
    }
}

async function updateProfile() {
    const fullName = document.getElementById('full-name').value;
    const messageDiv = document.getElementById('profile-message');

    if (!fullName || fullName.trim().length < 3) {
        showProfileMessage('Full name must be at least 3 characters', 'error');
        return;
    }

    try {
        const response = await fetch('../api/update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ full_name: fullName })
        });

        const data = await response.json();

        if (data.success) {
            showProfileMessage(data.message, 'success');
            // Reload profile to show updated data
            setTimeout(() => loadProfile(), 1000);
        } else {
            showProfileMessage(data.message, 'error');
        }
    } catch (error) {
        showProfileMessage('Error updating profile. Please try again.', 'error');
    }
}

function showProfileMessage(message, type) {
    const messageDiv = document.getElementById('profile-message');
    
    messageDiv.className = type === 'success' ? 'success-message' : 'error-message';
    messageDiv.textContent = message;
    messageDiv.style.display = 'block';

    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleString('en-US', { 
        month: 'short', 
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

// Load profile on page load
document.addEventListener('DOMContentLoaded', function() {
    loadProfile();
});
