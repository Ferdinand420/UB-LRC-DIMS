<?php
// API endpoint to submit feedback
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

// Prevent librarians from submitting feedback
if (is_librarian()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Librarians cannot submit feedback']);
    exit;
}

// Get POST data
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    // fallback to form data
    $data = $_POST;
}
$condition_status = trim($data['condition_status'] ?? '');
$feedback_text = trim($data['feedback_text'] ?? '');
$room_id = isset($data['room_id']) && $data['room_id'] !== '' ? (int)$data['room_id'] : null;

// ✅ FIX: Validate condition_status is one of the allowed ENUM values
if (empty($condition_status)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Condition status is required']);
    exit;
}

$allowed_conditions = ['clean', 'dirty', 'damaged'];
if (!in_array(strtolower($condition_status), $allowed_conditions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid condition status. Must be one of: ' . implode(', ', $allowed_conditions)]);
    exit;
}

// Validate
if (empty($feedback_text)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback text is required']);
    exit;
}

if (!$room_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Room is required']);
    exit;
}

// Insert feedback (only students can submit feedback)
$student_id = get_user_id();
if (get_role() !== 'student') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only students can submit feedback']);
    exit;
}

$condition_status_lower = strtolower($condition_status);

// Legacy-safe: ensure room_id column exists (nullable)
$colRes = $conn->query("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'feedback' AND COLUMN_NAME = 'room_id'");
if ($colRes && $colRes->num_rows === 0) {
    $conn->query("ALTER TABLE feedback ADD COLUMN room_id INT NULL");
}
if ($colRes) { $colRes->close(); }

// Validate room exists
$roomStmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_id = ? LIMIT 1");
$roomStmt->bind_param('i', $room_id);
$roomStmt->execute();
$roomStmt->store_result();
if ($roomStmt->num_rows === 0) {
    $roomStmt->close();
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid room selected']);
    exit;
}
$roomStmt->close();

if ($room_id) {
    $stmt = $conn->prepare("INSERT INTO feedback (student_id, condition_status, feedback_text, room_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $student_id, $condition_status_lower, $feedback_text, $room_id);
} else {
    $stmt = $conn->prepare("INSERT INTO feedback (student_id, condition_status, feedback_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $student_id, $condition_status_lower, $feedback_text);
}

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your feedback! We will review it soon.',
        'feedback_id' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit feedback']);
}

$stmt->close();
?>
