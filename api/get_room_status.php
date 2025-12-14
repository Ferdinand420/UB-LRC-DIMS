<?php
/**
 * API Endpoint: Get Room Status
 * 
 * Returns real-time availability of all study rooms for the current date/time.
 * Includes occupancy status, reserved time slots, and waitlist counts.
 * Used by dashboard and room selection interfaces.
 * 
 * Response includes:
 * - room_id, room_name, capacity
 * - room_status: 'available' or 'occupied'
 * - current reservation details (if occupied)
 * - remaining_seconds: time until room is free
 * - waitlist_count: students waiting for this room
 */
ob_start();
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Get current date and time for availability calculation
$today = date('Y-m-d');
$now = date('H:i:s');

// Build query: Determine room occupancy based on approved reservations for current time slot
// CASE expression dynamically sets room_status: 'occupied' if current time falls within
// an approved reservation, otherwise 'available'
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

// Optimization: Fetch all waitlist counts in a single aggregate query instead of per-room
// This prevents N+1 query pattern that would otherwise query waitlist for each room in the loop
// GROUP BY room_id produces: [{room_id: 1, cnt: 2}, {room_id: 2, cnt: 0}, ...]
$wlSql = "SELECT room_id, COUNT(*) as cnt FROM waitlist WHERE status = 'waiting' GROUP BY room_id";
$wlStmt = $conn->prepare($wlSql);
$wlStmt->execute();
$wlResult = $wlStmt->get_result();
$waitlistCounts = [];  // Associative array: room_id => count for O(1) lookups
while ($wlRow = $wlResult->fetch_assoc()) {
    $waitlistCounts[$wlRow['room_id']] = (int)$wlRow['cnt'];
}
$wlStmt->close();

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
    // add waitlist count from pre-fetched data
    $row['waitlist_count'] = $waitlistCounts[$row['room_id']] ?? 0;
    $rooms[] = $row;
}
$stmt->close();

echo json_encode(['success' => true, 'rooms' => $rooms]);
ob_end_flush();
?>