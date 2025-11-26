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
    SELECT v.id, v.description, v.created_at,
           u.full_name as student_name, u.email as student_email,
           r.name as room_name,
           l.full_name as logged_by_name
    FROM violations v
    JOIN users u ON v.user_id = u.id
    LEFT JOIN rooms r ON v.room_id = r.id
    JOIN users l ON v.logged_by = l.id
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
