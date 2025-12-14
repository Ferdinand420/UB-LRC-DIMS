<?php
/**
 * API Endpoint: Submit Feedback
 * 
 * Allows students (only) to submit feedback about study room conditions.
 * Validates feedback data: condition status must be one of the ENUM values (clean/dirty/damaged),
 * feedback text is required, and room must exist and be valid.
 * 
 * Request body: { "condition_status": string, "feedback_text": string, "room_id": int }
 * Response: { "success": bool, "message": string }
 */
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Verify student is logged in
if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Prevent librarians from submitting feedback (they submit violations instead)
if (is_librarian()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Librarians cannot submit feedback']);
    exit;
}

// Parse request: Accept both JSON and form-encoded data for compatibility
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    // Fallback to form data if JSON decode fails
    $data = $_POST;
}
$condition_status = trim($data['condition_status'] ?? '');
$feedback_text = trim($data['feedback_text'] ?? '');
$room_id = isset($data['room_id']) && $data['room_id'] !== '' ? (int)$data['room_id'] : null;

// Validate condition_status: Must be one of the ENUM values defined in schema
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
ob_end_flush();
?>
