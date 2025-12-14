<?php
/**
 * API Endpoint: Cancel Reservation
 * 
 * Allows students to cancel their pending or approved reservations.
 * Checks authorization (student can only cancel their own reservations)
 * and validates status (prevents cancelling already completed/rejected/cancelled reservations).
 * 
 * Request body: { "reservation_id": int }
 * Response: { "success": bool, "message": string }
 */
ob_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Verify authentication before processing request
session_start();
if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Parse request payload to extract reservation ID
$input = json_decode(file_get_contents('php://input'), true);
$reservation_id = $input['reservation_id'] ?? null;

if (!$reservation_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Reservation ID is required']);
    exit;
}

$user_id = get_user_id();

// Authorization check: Verify reservation belongs to logged-in student
// Uses prepared statement with type-safe binding (ii = integer, integer)
$stmt = $conn->prepare("SELECT status FROM reservations WHERE reservation_id = ? AND student_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Reservation not found or unauthorized']);
    exit;
}

$reservation = $result->fetch_assoc();

// Validation: Only pending or approved reservations can be cancelled
// Prevents cancelling completed, rejected, or already-cancelled reservations
if (!in_array($reservation['status'], ['pending', 'approved'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Only pending or approved reservations can be cancelled']);
    exit;
}

// Update reservation status to cancelled
$stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE reservation_id = ? AND student_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Reservation cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel reservation']);
}

$stmt->close();
$conn->close();
ob_end_flush();
?>
