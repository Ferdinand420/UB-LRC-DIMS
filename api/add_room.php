<?php
// API endpoint to add a new room (Librarian only)
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure user is librarian
if (!is_librarian()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Librarians only']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$capacity = intval($data['capacity'] ?? 0);
$status = $data['status'] ?? 'available';
$description = trim($data['description'] ?? '');

// Validate
if (empty($name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Room name is required']);
    exit;
}

if ($capacity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Capacity must be at least 1']);
    exit;
}

if (!in_array($status, ['available', 'maintenance'])) {
    $status = 'available';
}

// Check if room name already exists
$stmt = $conn->prepare("SELECT id FROM rooms WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Room name already exists']);
    exit;
}

// Insert room
$stmt = $conn->prepare("INSERT INTO rooms (name, capacity, status, description) VALUES (?, ?, ?, ?)");
$stmt->bind_param("siss", $name, $capacity, $status, $description);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Room added successfully',
        'room_id' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add room']);
}

$stmt->close();
?>
