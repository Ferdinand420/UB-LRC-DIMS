<?php
/**
 * API Endpoint: Create Reservation
 * 
 * Creates a new room reservation for the authenticated student.
 * Performs validation on:
 * - Date/time availability
 * - Capacity constraints
 * - Time conflicts
 * - Room status
 * 
 * Only accessible to students (RBAC)
 */
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

/**
 * Ensure reservation_students table exists
 * This table tracks which students are part of a group reservation
 * (for backward compatibility with earlier schema versions)
 */
function ensure_reservation_students_table(mysqli $conn): void {
    $sql = "CREATE TABLE IF NOT EXISTS reservation_students (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reservation_id INT NOT NULL,
        student_id_value VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
        INDEX idx_reservation (reservation_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $conn->query($sql);
}

// AUTHORIZATION: Ensure user is logged in
if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// AUTHORIZATION: Only students can create reservations
if (!is_student()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Only students can create reservations']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$room_id = $data['room_id'] ?? null;
$reservation_date = $data['reservation_date'] ?? null;
$start_time = $data['start_time'] ?? null;
$end_time = $data['end_time'] ?? null;
$purpose = $data['purpose'] ?? '';
$student_ids = isset($data['student_ids']) && is_array($data['student_ids']) ? array_filter($data['student_ids']) : [];

// Validate required fields
if (!$room_id || !$reservation_date || !$start_time || !$end_time) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}


// Validate date is in the future
$today = date('Y-m-d');
if ($reservation_date < $today) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cannot reserve for past dates']);
    exit;
}

// Validate time range
if ($start_time >= $end_time) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'End time must be after start time']);
    exit;
}

// ✅ FIX: Validate time is within 07:00 - 17:00
if ($start_time < '07:00:00' || $end_time > '17:00:00') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Reservations must be between 7:00 AM and 5:00 PM']);
    exit;
}

// ✅ FIX: Validate capacity - max 10 students
if (count($student_ids) > 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Maximum 10 students per reservation']);
    exit;
}

// Check if room exists and is available
$stmt = $conn->prepare("SELECT room_id, room_name, capacity FROM rooms WHERE room_id = ?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$room) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Room not found']);
    exit;
}

if ($room['status'] === 'maintenance') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Room is under maintenance']);
    exit;
}

// ✅ FIX: Validate student count doesn't exceed room capacity
if (count($student_ids) > $room['capacity']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Number of students exceeds room capacity of ' . $room['capacity']]);
    exit;
}

// Check for overlapping reservations (simple interval overlap rule)
$stmt = $conn->prepare("
    SELECT reservation_id FROM reservations 
    WHERE room_id = ? 
    AND reservation_date = ? 
    AND status IN ('pending', 'approved')
    AND (start_time < ? AND end_time > ?)
");
$stmt->bind_param("isss", $room_id, $reservation_date, $end_time, $start_time);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Room is already reserved for this time slot']);
    exit;
}

// Validate student IDs
if (empty($student_ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide at least one student ID']);
    exit;
}



// Disable auto-commit and start transaction
$conn->autocommit(false);
$conn->begin_transaction();

try {
    $student_id = get_user_id();
    $group_members_str = !empty($student_ids) ? implode(',', $student_ids) : null;
    
    $stmt = $conn->prepare("
        INSERT INTO reservations (student_id, room_id, reservation_date, start_time, end_time, purpose, group_members, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iisssss", $student_id, $room_id, $reservation_date, $start_time, $end_time, $purpose, $group_members_str);
    
    if (!$stmt->execute()) {
        throw new Exception("Reservation insert failed: " . $stmt->error);
    }
    
    $reservation_id = $stmt->insert_id;
    $stmt->close();

    $conn->commit();
    $conn->autocommit(true);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Reservation submitted successfully. Awaiting approval.',
        'reservation_id' => $reservation_id
    ]);
} catch (Exception $e) {
    $conn->rollback();
    $conn->autocommit(true);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create reservation: ' . $e->getMessage()]);
}
?>
