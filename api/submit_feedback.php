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
$data = json_decode(file_get_contents('php://input'), true);
$message = trim($data['message'] ?? '');

// Validate
if (empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback message is required']);
    exit;
}

if (strlen($message) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback must be at least 10 characters']);
    exit;
}

// Insert feedback
$user_id = get_user_id();
$stmt = $conn->prepare("INSERT INTO feedback (user_id, message, status) VALUES (?, ?, 'new')");
$stmt->bind_param("is", $user_id, $message);

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
