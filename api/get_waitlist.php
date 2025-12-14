<?php
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');
session_start();

if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Get all waitlist entries
$stmt = $conn->prepare("
    SELECT w.*, s.full_name, s.ub_mail as email, r.room_name
    FROM waitlist w
    LEFT JOIN students s ON w.student_id = s.student_id
    JOIN rooms r ON w.room_id = r.room_id
    ORDER BY w.created_at ASC
");

$stmt->execute();
$result = $stmt->get_result();

$waitlist = [];
while ($row = $result->fetch_assoc()) {
    $waitlist[] = $row;
}

echo json_encode([
    'success' => true,
    'waitlist' => $waitlist
]);

$stmt->close();
$conn->close();
ob_end_flush();
?>
