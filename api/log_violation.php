<?php
/**
 * API Endpoint: Log Violation
 * 
 * Allows librarians to record rule violations against students.
 * Violations include: no-show, late, damaged property, overcapacity
 * 
 * Authorization: Librarians only (RBAC)
 * Request body: { "student_email": string, "violation_type": string, "room_id": int, "description": string }
 * Response: { "success": bool, "message": string }
 */
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Authorization check: Only librarians can log violations
if (!is_librarian()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Librarians only']);
    exit;
}

// Parse request payload
$data = json_decode(file_get_contents('php://input'), true);

$student_email = trim($data['student_email'] ?? '');
$room_id = $data['room_id'] ?? null;
$violation_type = trim($data['violation_type'] ?? '');
$description = trim($data['description'] ?? '');

// Validate required fields
if (empty($student_email) || empty($violation_type) || empty($description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Student email, violation type, and description are required']);
    exit;
}

// Validate violation_type: Must be one of the ENUM values defined in schema
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
    $stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_id = ?");
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
