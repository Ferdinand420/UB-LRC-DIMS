<?php
// API endpoint to log a violation (Librarians only)
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

$student_email = trim($data['student_email'] ?? '');
$room_id = $data['room_id'] ?? null;
$violation_type = trim($data['violation_type'] ?? '');
$description = trim($data['description'] ?? '');

// Validate
if (empty($student_email) || empty($violation_type) || empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Student email, violation type, and description are required']);
    exit;
}

// ✅ FIX: Validate violation_type is one of the allowed ENUM values
$allowed_types = ['no-show', 'late', 'damaged property', 'overcapacity'];
if (!in_array(strtolower($violation_type), $allowed_types)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid violation type. Must be one of: ' . implode(', ', $allowed_types)]);
    exit;
}

// Find student user
$stmt = $conn->prepare("SELECT student_id as id, full_name FROM students WHERE ub_mail = ?");
$stmt->bind_param("s", $student_email);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Student not found']);
    exit;
}

// If room_id provided, verify it exists
if ($room_id) {
    $stmt = $conn->prepare("SELECT id FROM rooms WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $room_id = null; // Set to null if room doesn't exist
    }
    $stmt->close();
}

// ✅ FIX: Log violation with violation_type ENUM and status
$librarian_id = get_user_id();
$violation_type_lower = strtolower($violation_type);
$stmt = $conn->prepare("INSERT INTO violations (student_id, room_id, librarian_id, violation_type, status, description) VALUES (?, ?, ?, ?, 'pending', ?)");
$stmt->bind_param("iiiss", $student['id'], $room_id, $librarian_id, $violation_type_lower, $description);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Violation logged successfully',
        'violation_id' => $stmt->insert_id
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to log violation']);
}

$stmt->close();
?>
