// History page functionality
let currentFilter = 'all';

function filterHistory(type) {
    currentFilter = type;
    
    // Update button states
    document.querySelectorAll('[id^="filter-"]').forEach(btn => {
        btn.style.opacity = '0.6';
        btn.style.background = '#6c757d';
    });
    document.getElementById(`filter-${type}`).style.opacity = '1';
    document.getElementById(`filter-${type}`).style.background = 'var(--color-primary)';
    
    loadHistory(type);
}

async function loadHistory(type = 'all') {
    const loadingElement = document.getElementById('history-loading');
    const timelineElement = document.getElementById('history-timeline');
    const noDataElement = document.getElementById('no-history');

    loadingElement.style.display = 'block';
    timelineElement.style.display = 'none';
    noDataElement.style.display = 'none';

    try {
        let allItems = [];
        
        // Fetch history (reservations, feedback)
        const historyResponse = await fetch(`../api/get_history.php?type=${type}`);
        const historyData = await historyResponse.json();
        if (historyData.success && historyData.history) {
            allItems = allItems.concat(historyData.history);
        }
        
        // Fetch violations if type is 'all' or 'violations'
        if (type === 'all' || type === 'violations') {
            const violationsResponse = await fetch('../api/get_violations.php');
            const violationsData = violationsResponse.ok ? await violationsResponse.json() : { success: false };
            if (violationsData.success && violationsData.violations) {
                const violationItems = violationsData.violations.map(v => ({
                    ...v,
                    type: 'violation',
                    created_at: v.created_at
                }));
                allItems = allItems.concat(violationItems);
            }
        }
        
        // Filter out pending items
        allItems = allItems.filter(item => item.type === 'violation' || item.status !== 'pending');
        
        // Sort all items by date descending
        allItems.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        
        if (allItems.length > 0) {
            displayHistory(allItems);
        } else {
            loadingElement.style.display = 'none';
            noDataElement.style.display = 'block';
        }
    } catch (error) {
        console.error('Error loading history:', error);
        loadingElement.textContent = 'Error loading history.';
    }
}

function displayHistory(history) {
    const loadingElement = document.getElementById('history-loading');
    const timelineElement = document.getElementById('history-timeline');
    const noDataElement = document.getElementById('no-history');

    loadingElement.style.display = 'none';

    if (!history || history.length === 0) {
        noDataElement.style.display = 'block';
        return;
    }

    timelineElement.style.display = 'block';
    timelineElement.innerHTML = '';

    history.forEach((item, index) => {
        const itemDiv = document.createElement('div');
        itemDiv.className = 'history-item';
        itemDiv.style.cssText = `
            padding: 1.25rem;
            background: ${index % 2 === 0 ? '#ffffff' : '#f9fafb'};
            border-left: 4px solid ${item.type === 'reservation' ? 'var(--color-primary)' : 'var(--color-gold)'};
            border-radius: var(--radius-md);
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        `;

        if (item.type === 'reservation') {
            const statusBadge = `<span class="status-badge status-${item.status}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>`;
            
            itemDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <div>
                        <strong style="color: var(--color-primary);">📅 Reservation</strong>
                        ${item.user_name ? `<br><small style="color: #666;">Student: ${item.user_name}</small>` : ''}
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        ${statusBadge}
                        <button class="view-reservation-btn" data-reservation-id="${item.id}" style="padding: 0.5rem 1rem; background: var(--color-primary); color: white; border: none; border-radius: var(--radius-sm); cursor: pointer; font-size: 0.875rem;">View</button>
                    </div>
                </div>
                <div style="margin: 0.5rem 0;">
                    <strong>${item.room_name}</strong><br>
                    <small>${formatDate(item.reservation_date)} • ${formatTime(item.start_time)} - ${formatTime(item.end_time)}</small>
                </div>
                ${item.approved_by_name ? `<div style="font-size: 0.813rem; color: #666;">Processed by: ${item.approved_by_name}</div>` : ''}
                <div style="font-size: 0.75rem; color: #999; margin-top: 0.5rem;">
                    ${formatDateTime(item.created_at)}
                </div>
            `;
        } else if (item.type === 'feedback') {
            const statusBadge = `<span class="status-badge status-${item.status}">${item.status.charAt(0).toUpperCase() + item.status.slice(1)}</span>`;
            
            itemDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <div>
                        <strong style="color: var(--color-gold);">💬 Feedback</strong>
                        ${item.user_name ? `<br><small style="color: #666;">From: ${item.user_name}</small>` : ''}
                    </div>
                    ${statusBadge}
                </div>
                <div style="margin: 0.5rem 0; font-size: 0.938rem;">
                    "${item.message}"
                </div>
                <div style="font-size: 0.75rem; color: #999; margin-top: 0.5rem;">
                    ${formatDateTime(item.created_at)}
                </div>
            `;
        } else if (item.type === 'violation') {
            itemDiv.style.borderLeftColor = '#dc2626';
            
            itemDiv.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.5rem;">
                    <div>
                        <strong style="color: #dc2626;">🚨 Violation</strong>
                        ${item.user_email ? `<br><small style="color: #666;">Student: ${item.user_email}</small>` : ''}
                    </div>
                    ${item.logged_by_name ? `<span style="font-size: 0.75rem; color: #666;">Recorded by: ${item.logged_by_name}</span>` : ''}
                </div>
                <div style="margin: 0.5rem 0;">
                    <strong>${item.room_name || 'Unknown Room'}</strong><br>
                    <small style="color: #666; margin-top: 0.25rem; display: block;">
                        <strong>Reason:</strong> ${item.description}
                    </small>
                </div>
                <div style="font-size: 0.75rem; color: #999; margin-top: 0.5rem;">
                    ${formatDateTime(item.created_at)}
                </div>
            `;
        }

        timelineElement.appendChild(itemDiv);
    });

    // Add event listeners to view buttons
    document.querySelectorAll('.view-reservation-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const reservationId = this.dataset.reservationId;
            // Navigate to reservations page with reservation details modal
            window.location.href = `reservations.php?view=${reservationId}`;
        });
    });
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

// Load history on page load
document.addEventListener('DOMContentLoaded', function() {
    filterHistory('all');
});
