<?php
// API endpoint to get feedback
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

$user_id = get_user_id();
$is_librarian = is_librarian();

// Librarians see all feedback, students see only their own
if ($is_librarian) {
    $sql = "
        SELECT f.id, f.message, f.status, f.created_at,
               u.full_name as user_name, u.email as user_email
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC
        LIMIT 100
    ";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "
        SELECT id, message, status, created_at
        FROM feedback
        WHERE user_id = ?
        ORDER BY created_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$feedback_list = [];

while ($row = $result->fetch_assoc()) {
    $feedback_list[] = $row;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'feedback' => $feedback_list
]);
?>
