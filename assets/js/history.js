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
        const response = await fetch(`../api/get_history.php?type=${type}`);
        const data = await response.json();

        if (data.success && data.history) {
            displayHistory(data.history);
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
                    ${statusBadge}
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
        }

        timelineElement.appendChild(itemDiv);
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
