<?php
// API endpoint to get violations (Librarians only)
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

// Get all violations
$sql = "
    SELECT v.violation_id, v.description, v.violation_type, v.status, v.created_at,
           s.full_name as student_name, s.ub_mail as student_email,
           r.room_name,
           lib.full_name as logged_by_name
    FROM violations v
    LEFT JOIN students s ON v.student_id = s.student_id
    LEFT JOIN rooms r ON v.room_id = r.room_id
    LEFT JOIN librarians lib ON v.librarian_id = lib.librarian_id
    ORDER BY v.created_at DESC
    LIMIT 100
";

$result = $conn->query($sql);
$violations = [];

while ($row = $result->fetch_assoc()) {
    $violations[] = $row;
}

echo json_encode([
    'success' => true,
    'violations' => $violations
]);
?>
