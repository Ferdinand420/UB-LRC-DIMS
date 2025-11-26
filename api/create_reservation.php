<?php
// API endpoint to create a new reservation
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

// Only allow students to create reservations
if (!is_student()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only students can create reservations']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$room_id = $data['room_id'] ?? null;
$reservation_date = $data['reservation_date'] ?? null;
$start_time = $data['start_time'] ?? null;
$end_time = $data['end_time'] ?? null;
$purpose = $data['purpose'] ?? '';

// Validate required fields
if (!$room_id || !$reservation_date || !$start_time || !$end_time) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Validate date is in the future
$today = date('Y-m-d');
if ($reservation_date < $today) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot reserve for past dates']);
    exit;
}

// Validate time range
if ($start_time >= $end_time) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'End time must be after start time']);
    exit;
}

// Check if room exists and is available
$stmt = $conn->prepare("SELECT id, name, status FROM rooms WHERE id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Room not found']);
    exit;
}

if ($room['status'] === 'maintenance') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Room is under maintenance']);
    exit;
}

// Check for overlapping reservations
$stmt = $conn->prepare("
    SELECT id FROM reservations 
    WHERE room_id = ? 
    AND reservation_date = ? 
    AND status IN ('pending', 'approved')
    AND (
        (start_time < ? AND end_time > ?) OR
        (start_time < ? AND end_time > ?) OR
        (start_time >= ? AND end_time <= ?)
    )
");
$stmt->bind_param("isssssss", $room_id, $reservation_date, $end_time, $start_time, $end_time, $start_time, $start_time, $end_time);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Room is already reserved for this time slot']);
    exit;
}

// Insert reservation
$user_id = get_user_id();
$stmt = $conn->prepare("
    INSERT INTO reservations (user_id, room_id, reservation_date, start_time, end_time, purpose, status) 
    VALUES (?, ?, ?, ?, ?, ?, 'pending')
");
$stmt->bind_param("iissss", $user_id, $room_id, $reservation_date, $start_time, $end_time, $purpose);

if ($stmt->execute()) {
    $reservation_id = $stmt->insert_id;
    echo json_encode([
        'success' => true, 
        'message' => 'Reservation submitted successfully. Awaiting approval.',
        'reservation_id' => $reservation_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create reservation']);
}

$stmt->close();
?>
