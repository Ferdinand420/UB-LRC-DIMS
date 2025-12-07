const REFRESH_MS = 30000; // 30s auto refresh
let currentController = null;

async function loadDashboardStats() {
    if (currentController) {
        currentController.abort();
    }
    currentController = new AbortController();
    const signal = currentController.signal;
    try {
        // set temporary loading indicators (non-intrusive)
        loadingDots('total-reservations');
        loadingDots('pending-reservations');
        loadingDots('approved-reservations');
        loadingDots('total-feedback');
        const [resReservations, resFeedback] = await Promise.all([
            fetch('../api/get_reservations.php', { signal }),
            fetch('../api/get_feedback.php', { signal })
        ]);
        if (!resReservations.ok || !resFeedback.ok) throw new Error('Network error');
        const [reservationsData, feedbackData] = await Promise.all([
            resReservations.json(),
            resFeedback.json()
        ]);
        
        const reservations = reservationsData.reservations || [];
        const feedback = feedbackData.feedback || [];
        
        const total = reservations.length;
        let pending = 0, approved = 0;
        for (let i = 0; i < reservations.length; i++) {
            const s = reservations[i].status;
            if (s === 'pending') pending++;
            else if (s === 'approved') approved++;
        }
        updateText('total-reservations', total);
        updateText('pending-reservations', pending);
        updateText('approved-reservations', approved);
        updateText('total-feedback', feedback.length);
    } catch (e) {
        if (e.name === 'AbortError') {
            // silently ignore aborted fetch
            return;
        }
        updateText('total-reservations', '0');
        updateText('pending-reservations', '0');
        updateText('approved-reservations', '0');
        updateText('total-feedback', '0');
        console.error('loadDashboardStats failed', e);
    }
}

async function loadRoomAvailability() {
    try {
        updateText('available-rooms-count', 'Loading...');
        const response = await fetch('../api/get_rooms.php');
        const data = await response.json();

        if (data.success && data.rooms) {
            const availableRooms = data.rooms.filter(room => room.status === 'available');
            updateText('available-rooms-count', `${availableRooms.length} of ${data.rooms.length} rooms available`);
        } else {
            updateText('available-rooms-count', 'No data');
        }
    } catch (error) {
        console.error('Error loading room availability:', error);
        updateText('available-rooms-count', 'Check console for errors');
    }
}

async function loadWaitlist() {
    try {
        updateText('waitlist-count', 'Loading...');
        const response = await fetch('../api/get_waitlist.php');
        const data = await response.json();

        if (data.success) {
            const count = data.waitlist ? data.waitlist.length : 0;
            updateText('waitlist-count', count === 0 ? 'No active waitlist' : `${count} ${count === 1 ? 'person' : 'people'} waiting`);
        } else {
            updateText('waitlist-count', 'No data');
        }
    } catch (error) {
        console.error('Error loading waitlist:', error);
        updateText('waitlist-count', 'Check console for errors');
    }
}

async function loadRecentActivity() {
    try {
        const response = await fetch('../api/get_recent_activity.php');
        const data = await response.json();

        const activityList = document.getElementById('activity-list');
        if (!activityList) return;

        if (data.success && data.activities && data.activities.length > 0) {
            activityList.innerHTML = data.activities.map(activity => {
                const timestamp = new Date(activity.created_at);
                const timeStr = formatTimestamp(timestamp);
                
                let icon, title, description, statusClass;
                
                if (activity.activity_type === 'reservation') {
                    icon = '📅';
                    const userName = activity.user_name ? ` by ${activity.user_name}` : '';
                    title = `Reservation ${activity.status}${userName}`;
                    const date = new Date(activity.reservation_date);
                    const dateStr = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                    description = `${activity.room_name} - ${dateStr} at ${activity.start_time.substring(0, 5)}`;
                    statusClass = `status-${activity.status}`;
                } else if (activity.activity_type === 'feedback') {
                    icon = '💬';
                    const userName = activity.user_name ? ` from ${activity.user_name}` : '';
                    title = `Feedback submitted${userName}`;
                    description = activity.message.length > 80 ? activity.message.substring(0, 80) + '...' : activity.message;
                    statusClass = `status-${activity.status}`;
                } else if (activity.activity_type === 'violation') {
                    icon = '⚠';
                    title = `Violation logged for ${activity.user_name}`;
                    description = `${activity.room_name}: ${activity.description.substring(0, 60)}...`;
                    statusClass = 'status-violation';
                }
                
                return `
                    <div class="activity-item">
                        <div class="activity-icon">${icon}</div>
                        <div class="activity-content">
                            <div class="activity-title">${title}</div>
                            <div class="activity-description">${description}</div>
                            <div class="activity-time">${timeStr}</div>
                        </div>
                        <div class="activity-status ${statusClass}">${activity.status || ''}</div>
                    </div>
                `;
            }).join('');
        } else {
            activityList.innerHTML = '<p style="color: var(--color-text-muted); text-align: center; padding: 2rem;">No recent activity</p>';
        }
    } catch (error) {
        console.error('Error loading recent activity:', error);
        const activityList = document.getElementById('activity-list');
        if (activityList) {
            activityList.innerHTML = '<p style="color: var(--color-danger); text-align: center; padding: 2rem;">Failed to load activity</p>';
        }
    }
}

function formatTimestamp(date) {
    const now = new Date();
    const diff = now - date;
    const minutes = Math.floor(diff / 60000);
    const hours = Math.floor(minutes / 60);
    const days = Math.floor(hours / 24);
    
    if (minutes < 1) return 'Just now';
    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days < 7) return `${days}d ago`;
    
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function updateText(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value;
}

function loadingDots(id) {
    const el = document.getElementById(id);
    if (el) el.textContent = '...';
}

// Modal functions
async function showRoomAvailabilityModal() {
    const modal = document.getElementById('roomAvailabilityModal');
    const content = document.getElementById('roomAvailabilityContent');
    
    modal.style.display = 'flex';
    content.innerHTML = '<p style="text-align: center; padding: 2rem;">Loading room data...</p>';
    
    try {
        const response = await fetch('../api/get_rooms.php');
        const data = await response.json();
        
        if (data.success && data.rooms) {
            const rooms = data.rooms;
            content.innerHTML = `
                <div class="room-list">
                    ${rooms.map(room => {
                        const statusClass = room.status === 'available' ? 'status-approved' : 'status-cancelled';
                        const statusIcon = room.status === 'available' ? '✓' : '✗';
                        const isUnavailable = room.status !== 'available';
                        
                        return `
                            <div class="room-card">
                                <div class="room-card-header">
                                    <h3>${room.name}</h3>
                                    <span class="activity-status ${statusClass}">${statusIcon} ${room.status}</span>
                                </div>
                                <div class="room-card-body">
                                    <p style="margin-top: 1.5rem;"><strong>Description:</strong> ${room.description || 'No description available'}</p>
                                    ${isUnavailable ? `
                                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                                            <p style="color: var(--color-text-muted); margin-bottom: 0.75rem;">This room is currently unavailable. Join the waitlist:</p>
                                            <form class="waitlist-form" onsubmit="joinWaitlist(event, ${room.id}, '${room.name}')">
                                                <div style="display: grid; gap: 0.75rem; grid-template-columns: 1fr 1fr;">
                                                    <input type="date" name="preferred_date" required min="${new Date().toISOString().split('T')[0]}" style="padding: 0.5rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                                                    <input type="time" name="preferred_time" required style="padding: 0.5rem; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                                                </div>
                                                <button type="submit" style="margin-top: 0.75rem; width: 100%; padding: 0.65rem; background: var(--color-primary); color: #fff; border: none; border-radius: var(--radius-sm); font-weight: 600; cursor: pointer; transition: background 0.2s;">
                                                    Join Waitlist
                                                </button>
                                            </form>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
        } else {
            content.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--color-danger);">Failed to load room data</p>';
        }
    } catch (error) {
        console.error('Error loading room availability modal:', error);
        content.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--color-danger);">Error loading data</p>';
    }
}

function closeRoomAvailabilityModal() {
    document.getElementById('roomAvailabilityModal').style.display = 'none';
}

async function showWaitlistModal() {
    const modal = document.getElementById('waitlistModal');
    const content = document.getElementById('waitlistContent');
    
    modal.style.display = 'flex';
    content.innerHTML = '<p style="text-align: center; padding: 2rem;">Loading waitlist data...</p>';
    
    try {
        const response = await fetch('../api/get_waitlist.php');
        const data = await response.json();
        
        if (data.success && data.waitlist && data.waitlist.length > 0) {
            content.innerHTML = `
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Email</th>
                            <th>Room</th>
                            <th>Preferred Date</th>
                            <th>Preferred Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${data.waitlist.map(item => {
                            const date = new Date(item.preferred_date);
                            const dateStr = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                            const time = item.preferred_time.substring(0, 5);
                            return `
                                <tr>
                                    <td>${item.full_name}</td>
                                    <td>${item.email}</td>
                                    <td>${item.room_name}</td>
                                    <td>${dateStr}</td>
                                    <td>${time}</td>
                                    <td><span class="activity-status status-pending">${item.status}</span></td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
        } else {
            content.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--color-text-muted);">No active waitlist entries</p>';
        }
    } catch (error) {
        console.error('Error loading waitlist modal:', error);
        content.innerHTML = '<p style="text-align: center; padding: 2rem; color: var(--color-danger);">Error loading data</p>';
    }
}

function closeWaitlistModal() {
    document.getElementById('waitlistModal').style.display = 'none';
}

async function joinWaitlist(event, roomId, roomName) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const preferredDate = formData.get('preferred_date');
    const preferredTime = formData.get('preferred_time');
    
    // Disable submit button to prevent double submission
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Joining...';
    
    try {
        const response = await fetch('../api/add_to_waitlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                room_id: roomId,
                preferred_date: preferredDate,
                preferred_time: preferredTime
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(`Successfully added to waitlist for ${roomName}!`);
            form.reset();
            // Reload the modal to show updated state
            await showRoomAvailabilityModal();
            // Refresh waitlist count
            await loadWaitlist();
        } else {
            alert(data.message || 'Failed to join waitlist');
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    } catch (error) {
        console.error('Error joining waitlist:', error);
        alert('An error occurred while joining the waitlist');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

// Close modals when clicking outside
window.onclick = function(event) {
    const roomModal = document.getElementById('roomAvailabilityModal');
    const waitlistModal = document.getElementById('waitlistModal');
    if (event.target === roomModal) {
        closeRoomAvailabilityModal();
    } else if (event.target === waitlistModal) {
        closeWaitlistModal();
    }
}

loadDashboardStats();
loadRoomAvailability();
loadWaitlist();
loadRecentActivity();
setInterval(loadDashboardStats, REFRESH_MS);
setInterval(loadRoomAvailability, REFRESH_MS);
setInterval(loadWaitlist, REFRESH_MS);
setInterval(loadRecentActivity, REFRESH_MS);
