<?php
// API endpoint to add user to waitlist
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure user is logged in
$user_id = get_user_id();
if (!$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Only students can join waitlist
if (is_librarian()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Librarians cannot join waitlist']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$room_id = $data['room_id'] ?? null;
$preferred_date = $data['preferred_date'] ?? null;
$preferred_time = $data['preferred_time'] ?? null;

// Validate input
if (!$room_id || !$preferred_date || !$preferred_time) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Room ID, preferred date, and time are required']);
    exit;
}

// Validate room exists
$sql = "SELECT id, name FROM rooms WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Room not found']);
    exit;
}
$stmt->close();

// Check if user is already on waitlist for this room and date
$sql = "SELECT id FROM waitlist WHERE user_id = ? AND room_id = ? AND preferred_date = ? AND status = 'waiting'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $user_id, $room_id, $preferred_date);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'You are already on the waitlist for this room on this date']);
    exit;
}
$stmt->close();

// Add to waitlist
$sql = "INSERT INTO waitlist (user_id, room_id, preferred_date, preferred_time, status) VALUES (?, ?, ?, ?, 'waiting')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiss", $user_id, $room_id, $preferred_date, $preferred_time);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Successfully added to waitlist'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to add to waitlist'
    ]);
}

$stmt->close();
?>
