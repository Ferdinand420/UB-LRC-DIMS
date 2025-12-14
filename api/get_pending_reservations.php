<?php
// API endpoint to get pending reservations for approval
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Ensure user is librarian
if (!is_librarian()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Librarians only']);
    exit;
}

// Get pending reservations
$sql = "
    SELECT r.reservation_id as id, r.reservation_date, r.start_time, r.end_time, r.purpose, r.status, r.created_at,
           rm.room_name, rm.capacity,
           s.student_id as user_id, s.full_name as user_name, s.ub_mail as user_email
    FROM reservations r
    JOIN rooms rm ON r.room_id = rm.room_id
    JOIN students s ON r.student_id = s.student_id
    WHERE r.status = 'pending'
    ORDER BY r.created_at ASC
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}
$stmt->execute();
$result = $stmt->get_result();
$reservations = [];

while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}
$stmt->close();

echo json_encode([
    'success' => true,
    'reservations' => $reservations
]);
ob_end_flush();
?>
