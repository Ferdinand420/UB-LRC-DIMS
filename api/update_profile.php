<?php
// API endpoint to update user profile
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
$role = get_role();
$data = json_decode(file_get_contents('php://input'), true);

$full_name = trim($data['full_name'] ?? '');

// Validate
if (empty($full_name)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full name is required']);
    exit;
}

if (strlen($full_name) < 3) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Full name must be at least 3 characters']);
    exit;
}

// Update user based on role
if ($role === 'librarian') {
    $stmt = $conn->prepare("UPDATE librarians SET full_name = ? WHERE librarian_id = ?");
} else {
    $stmt = $conn->prepare("UPDATE students SET full_name = ? WHERE student_id = ?");
}
$stmt->bind_param("si", $full_name, $user_id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully'
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update profile']);
}

$stmt->close();
?>
