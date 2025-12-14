<?php
/**
 * API Endpoint: Get Reservations
 * 
 * Retrieves reservation records based on user role:
 * - Librarians: All reservations (for admin/approval management)
 * - Students: Only their own reservations
 * 
 * Returns up to 100 most recent reservations with room and student details.
 * Used by dashboard to display reservation history.
 */
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Verify user is authenticated before processing
if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = get_user_id();
$is_librarian = is_librarian();

// Apply role-based filtering: Librarians see all, students see only their own
if ($is_librarian) {
    $sql = "
        SELECT r.reservation_id, r.reservation_date, r.start_time, r.end_time, r.purpose, r.status, r.created_at,
               r.group_members,
               rm.room_name, rm.capacity,
               s.full_name as user_name, s.ub_mail as user_email
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.room_id
        LEFT JOIN students s ON r.student_id = s.student_id
        ORDER BY r.reservation_date DESC, r.start_time DESC
        LIMIT 100
    ";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "
        SELECT r.reservation_id, r.reservation_date, r.start_time, r.end_time, r.purpose, r.status, r.created_at,
               r.group_members,
               rm.room_name, rm.capacity,
               s.ub_mail as user_email
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.room_id
        JOIN students s ON r.student_id = s.student_id
        WHERE r.student_id = ?
        ORDER BY r.reservation_date DESC, r.start_time DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$reservations = [];

// Process results: Convert comma-separated student IDs to array for frontend
while ($row = $result->fetch_assoc()) {
    // The 'group_members' field stores comma-separated student IDs
    // Convert to array for easier UI handling (e.g., displaying multiple names)
    if (!empty($row['group_members'])) {
        $row['student_ids'] = explode(',', $row['group_members']);
    } else {
        $row['student_ids'] = [];  // Solo reservation, only requester
    }
    $reservations[] = $row;
}
$stmt->close();

echo json_encode([
    'success' => true,
    'reservations' => $reservations
]);
ob_end_flush();
?>
