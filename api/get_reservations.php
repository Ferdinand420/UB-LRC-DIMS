<?php
// API endpoint to get user's reservations
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure user is logged in
if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = get_user_id();
$is_librarian = is_librarian();

// Librarians see all reservations, students see only their own
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

while ($row = $result->fetch_assoc()) {
    // Map group_members to student_ids array for UI
    if (!empty($row['group_members'])) {
        $row['student_ids'] = explode(',', $row['group_members']);
    } else {
        $row['student_ids'] = [];
    }
    $reservations[] = $row;
}
$stmt->close();

echo json_encode([
    'success' => true,
    'reservations' => $reservations
]);
?>
