<?php
/**
 * API Endpoint: Get Feedback
 * 
 * Retrieves feedback submissions based on user role:
 * - Librarians: All feedback for review and acknowledgment
 * - Students: Only their own submitted feedback
 * 
 * Feedback data includes room condition (clean/dirty/damaged),
 * student comment, and librarian review status.
 * 
 * Response: { "success": bool, "feedback": [...] }
 */
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Verify user is authenticated
$user_id = get_user_id();
$is_librarian = is_librarian();
$role = get_role();

if (!$role) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!$is_librarian && !$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Apply role-based filtering: Librarians see all, students see only their own
if ($is_librarian) {
    $sql = "
        SELECT f.feedback_id as id, f.condition_status, f.feedback_text, f.status, f.reviewed_at, f.reviewed_by, f.created_at,
               s.full_name as user_name, s.ub_mail as user_email,
               rm.room_name
        FROM feedback f
        LEFT JOIN students s ON f.student_id = s.student_id
        LEFT JOIN rooms rm ON f.room_id = rm.room_id
        ORDER BY f.created_at DESC
        LIMIT 100
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }
} else {
    $sql = "
        SELECT f.feedback_id as id, f.condition_status, f.feedback_text, f.status, f.reviewed_at, f.reviewed_by, f.created_at,
               s.full_name as user_name, s.ub_mail as user_email,
               rm.room_name
        FROM feedback f
        LEFT JOIN students s ON f.student_id = s.student_id
        LEFT JOIN rooms rm ON f.room_id = rm.room_id
        WHERE f.student_id = ?
        ORDER BY f.created_at DESC
    ";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit;
    }
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
ob_end_flush();
?>
