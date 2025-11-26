<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Get all waitlist entries
$stmt = $conn->prepare("
    SELECT w.*, u.full_name, u.email, r.name as room_name
    FROM waitlist w
    JOIN users u ON w.user_id = u.id
    JOIN rooms r ON w.room_id = r.id
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
?>
