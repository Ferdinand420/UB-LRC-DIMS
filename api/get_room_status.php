<?php
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Fetch rooms with current occupancy info (today)
$today = date('Y-m-d');
$now = date('H:i:s');

// ✅ FIX: Dynamically calculate room status based on current reservations
$sql = "
    SELECT 
        r.room_id, 
        r.room_name, 
        r.capacity,
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM reservations res 
                WHERE res.room_id = r.room_id 
                AND res.reservation_date = ? 
                AND res.status = 'approved'
                AND ? BETWEEN res.start_time AND res.end_time
            ) THEN 'occupied'
            ELSE 'available'
        END as room_status,
        res.reservation_id, 
        u.full_name as user_name, 
        u.ub_mail as user_email,
        res.start_time, 
        res.end_time
    FROM rooms r
    LEFT JOIN reservations res ON res.room_id = r.room_id 
        AND res.reservation_date = ? 
        AND res.status = 'approved'
        AND ? BETWEEN res.start_time AND res.end_time
    LEFT JOIN students u ON u.student_id = res.student_id
    ORDER BY r.room_name ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $today, $now, $today, $now);
$stmt->execute();
$result = $stmt->get_result();
$rooms = [];
while ($row = $result->fetch_assoc()) {
    // compute remaining time if occupied
    if ($row['reservation_id']) {
        $endTs = strtotime($today . ' ' . $row['end_time']);
        $remaining = max(0, $endTs - time());
        $row['remaining_seconds'] = $remaining;
    } else {
        $row['remaining_seconds'] = null;
    }
    // add waitlist count
    $wlStmt = $conn->prepare('SELECT COUNT(*) as cnt FROM waitlist WHERE room_id = ? AND status = "waiting"');
    $wlStmt->bind_param('i', $row['room_id']);
    $wlStmt->execute();
    $wlCnt = $wlStmt->get_result()->fetch_assoc()['cnt'] ?? 0;
    $wlStmt->close();
    $row['waitlist_count'] = (int)$wlCnt;
    $rooms[] = $row;
}
$stmt->close();

echo json_encode(['success' => true, 'rooms' => $rooms]);
?>