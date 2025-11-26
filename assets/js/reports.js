// Reports page functionality (Librarian only)
let currentReportData = null;

function loadReports() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    if (startDate > endDate) {
        alert('Start date must be before end date');
        return;
    }

    fetchReportData(startDate, endDate);
}

async function fetchReportData(startDate, endDate) {
    const loadingElement = document.getElementById('report-loading');
    const containerElement = document.getElementById('report-container');

    loadingElement.style.display = 'block';
    containerElement.style.display = 'none';

    try {
        const response = await fetch(`../api/get_report_stats.php?start_date=${startDate}&end_date=${endDate}`);
        const data = await response.json();

        if (data.success) {
            currentReportData = data;
            displayReport(data);
        } else {
            alert('Failed to load report data');
        }
    } catch (error) {
        console.error('Error loading report:', error);
        alert('Error loading report data');
    } finally {
        loadingElement.style.display = 'none';
    }
}

function displayReport(data) {
    const containerElement = document.getElementById('report-container');
    containerElement.style.display = 'block';

    // Display summary stats
    document.getElementById('stat-total-reservations').textContent = data.reservations.total || 0;
    document.getElementById('stat-approved').textContent = data.reservations.approved || 0;
    document.getElementById('stat-pending').textContent = data.reservations.pending || 0;
    document.getElementById('stat-violations').textContent = data.violations || 0;

    // Display room utilization
    displayRoomUtilization(data.room_utilization);

    // Display peak hours
    displayPeakHours(data.peak_hours);

    // Display top users
    displayTopUsers(data.top_users);
}

function displayRoomUtilization(rooms) {
    const container = document.getElementById('room-utilization-chart');
    
    if (!rooms || rooms.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #999; padding: 2rem;">No data available</p>';
        return;
    }

    // Create simple bar chart
    const maxCount = Math.max(...rooms.map(r => r.reservation_count));
    
    let html = '<div style="display: flex; flex-direction: column; gap: 1rem;">';
    rooms.forEach(room => {
        const percentage = maxCount > 0 ? (room.reservation_count / maxCount * 100) : 0;
        html += `
            <div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                    <span style="font-weight: 600;">${room.name}</span>
                    <span style="color: #666;">${room.reservation_count} reservations</span>
                </div>
                <div style="background: #e5e7eb; height: 24px; border-radius: var(--radius-md); overflow: hidden;">
                    <div style="background: linear-gradient(90deg, var(--color-primary), var(--color-gold)); height: 100%; width: ${percentage}%; transition: width 0.3s;"></div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function displayPeakHours(hours) {
    const container = document.getElementById('peak-hours-list');
    
    if (!hours || hours.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #999; padding: 2rem;">No data available</p>';
        return;
    }

    let html = '<div style="display: flex; flex-direction: column; gap: 0.75rem;">';
    hours.forEach((hour, index) => {
        html += `
            <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: #f9fafb; border-radius: var(--radius-md);">
                <div style="background: var(--color-primary); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                    ${index + 1}
                </div>
                <div style="flex: 1;">
                    <div style="font-weight: 600;">${hour.time_range}</div>
                    <div style="font-size: 0.875rem; color: #666;">${hour.count} reservations</div>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    container.innerHTML = html;
}

function displayTopUsers(users) {
    const tableElement = document.getElementById('top-users-table');
    const tbody = document.getElementById('top-users-tbody');
    const noDataElement = document.getElementById('no-users');

    if (!users || users.length === 0) {
        tableElement.style.display = 'none';
        noDataElement.style.display = 'block';
        return;
    }

    noDataElement.style.display = 'none';
    tableElement.style.display = 'table';
    tbody.innerHTML = '';

    users.forEach((user, index) => {
        const row = document.createElement('tr');

        const rankCell = document.createElement('td');
        rankCell.textContent = index + 1;
        rankCell.style.fontWeight = 'bold';
        rankCell.style.textAlign = 'center';
        row.appendChild(rankCell);

        const nameCell = document.createElement('td');
        nameCell.innerHTML = `<strong>${user.full_name}</strong><br><small style="color:#666;">${user.email}</small>`;
        row.appendChild(nameCell);

        const countCell = document.createElement('td');
        countCell.textContent = user.reservation_count;
        countCell.style.fontWeight = 'bold';
        countCell.style.textAlign = 'center';
        row.appendChild(countCell);

        tbody.appendChild(row);
    });
}

// Load report on page load
document.addEventListener('DOMContentLoaded', function() {
    loadReports();
});
