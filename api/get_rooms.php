<?php
// API endpoint to get available rooms
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

// Get all rooms
$sql = "SELECT room_id as id, room_name as name, capacity, status, description FROM rooms ORDER BY room_name";
$result = $conn->query($sql);

$rooms = [];
while ($row = $result->fetch_assoc()) {
    $rooms[] = $row;
}

echo json_encode([
    'success' => true,
    'rooms' => $rooms
]);
?>
