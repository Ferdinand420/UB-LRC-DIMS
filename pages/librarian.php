<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Librarian Panel - DIMS</title>
	<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
	<?php include __DIR__ . '/../includes/header.php'; ?>
  <div class="container">
    <?php include __DIR__ . '/../includes/sidebar.php'; render_sidebar('librarian'); ?>
		<main class="main-content">
			<header>
				<h1>Librarian Panel</h1>
			</header>
			<div class="card">
				<div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap;">
					<div style="display:flex; align-items:center; gap:0.5rem;">
						<h3 style="margin:0;">Pending Approvals</h3>
						<span id="pending-approvals-badge" style="min-width:2.5rem; text-align:center; padding:0.25rem 0.5rem; border-radius:999px; font-weight:700; font-size:0.9rem; background:#f0f4f8; color:#334155;">--</span>
					</div>
					<button id="pending-approvals-btn" class="btn btn-primary" style="padding:0.5rem 1.1rem; display:none;">Review</button>
				</div>
				<p id="pending-approvals-text" style="margin:0.5rem 0 0; color:#475569;">Loading pending approvals...</p>
			</div>
			<div class="card" style="margin-top:1.5rem;">
				<div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; flex-wrap:wrap;">
					<h3 style="margin:0;">Recent Activity</h3>
					<span id="recent-activity-status" style="font-size:0.9rem; color:#64748b;">Loading…</span>
				</div>
				<ul id="recent-activity-list" style="margin:0.75rem 0 0; padding-left:1.1rem; line-height:1.5; list-style: none;"></ul>
			</div>
		</main>
	</div>
	<footer class="landing-footer">&copy; <?php echo date('Y'); ?> UB LRC-DIMS.</footer>
	<script src="../assets/js/sidebar.js"></script>
	<script src="../assets/js/script.js"></script>
	<script>
(function pendingApprovalsCard() {
  const textEl = document.getElementById('pending-approvals-text');
  const btnEl = document.getElementById('pending-approvals-btn');
  const badgeEl = document.getElementById('pending-approvals-badge');
  if (!textEl) return;

  function setBadge(countText, bg, color) {
    if (!badgeEl) return;
    badgeEl.textContent = countText;
    badgeEl.style.background = bg;
    badgeEl.style.color = color;
  }

  fetch('../api/get_pending_reservations.php')
    .then(r => r.ok ? r.json() : Promise.reject())
    .then(data => {
      if (!data.success || !Array.isArray(data.reservations)) throw new Error('bad');
      const count = data.reservations.length;
      setBadge(String(count), count === 0 ? '#e8f5e9' : '#fff7ed', count === 0 ? '#166534' : '#9a3412');

      if (count === 0) {
        textEl.textContent = 'No pending reservations.';
        if (btnEl) btnEl.style.display = 'none';
      } else if (count === 1) {
        textEl.textContent = '1 reservation awaiting action.';
        if (btnEl) btnEl.style.display = 'inline-block';
      } else {
        textEl.textContent = `${count} reservations awaiting action.`;
        if (btnEl) btnEl.style.display = 'inline-block';
      }
    })
    .catch(() => {
      textEl.textContent = 'Unable to load pending reservations right now.';
      if (btnEl) btnEl.style.display = 'none';
      setBadge('--', '#f0f4f8', '#334155');
    });

  if (btnEl) {
    btnEl.addEventListener('click', () => {
      window.location.href = 'approvals.php';
    });
  }
})();

(function recentActivityCard() {
  const listEl = document.getElementById('recent-activity-list');
  const statusEl = document.getElementById('recent-activity-status');
  if (!listEl || !statusEl) return;

  const typeColors = {
    reservation: { bg: '#e0f2fe', color: '#0ea5e9', label: 'Reservation' },
    feedback: { bg: '#fef3c7', color: '#d97706', label: 'Feedback' },
    violation: { bg: '#fee2e2', color: '#dc2626', label: 'Violation' }
  };

  function fmtDateTime(dt) {
    const d = new Date(dt);
    return d.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit', hour12: true });
  }

  function renderItem(item) {
    const li = document.createElement('li');
    li.style.marginBottom = '0.65rem';
    const meta = typeColors[item.activity_type] || { bg: '#eef2ff', color: '#4338ca', label: 'Update' };
    const badge = `<span style="display:inline-block; min-width:90px; text-align:center; padding:0.25rem 0.6rem; border-radius:999px; font-weight:700; font-size:0.8rem; background:${meta.bg}; color:${meta.color};">${meta.label}</span>`;

    let title = '';
    if (item.activity_type === 'reservation') {
      const status = item.status ? item.status.charAt(0).toUpperCase() + item.status.slice(1) : 'Pending';
      title = `${item.user_name ? item.user_name + ' – ' : ''}${item.room_name || 'Room'} (${status})`;
    } else if (item.activity_type === 'feedback') {
      title = `${item.user_name ? item.user_name + ' – ' : ''}${item.message || 'Feedback submitted'}`;
    } else if (item.activity_type === 'violation') {
      title = `${item.user_name ? item.user_name + ' – ' : ''}${item.description || item.violation_type || 'Violation logged'}`;
    }

    li.innerHTML = `
      <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
        ${badge}
        <div style="flex:1; min-width:220px; color:#0f172a;">${title}</div>
        <div style="font-size:0.85rem; color:#64748b; white-space:nowrap;">${fmtDateTime(item.created_at)}</div>
      </div>
    `;
    return li;
  }

  fetch('../api/get_recent_activity.php')
    .then(r => r.ok ? r.json() : Promise.reject())
    .then(data => {
      if (!data.success || !Array.isArray(data.activities)) throw new Error('bad');
      const items = data.activities;
      if (items.length === 0) {
        statusEl.textContent = 'No recent activity.';
        listEl.innerHTML = '';
        return;
      }
      statusEl.textContent = '';
      listEl.innerHTML = '';
      items.slice(0, 8).forEach(it => listEl.appendChild(renderItem(it)));
    })
    .catch(() => {
      statusEl.textContent = 'Unable to load recent activity.';
      listEl.innerHTML = '';
    });
})();
	</script>
</body>
</html>