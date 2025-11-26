<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// Check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

// Get reservation ID from request
$input = json_decode(file_get_contents('php://input'), true);
$reservation_id = $input['reservation_id'] ?? null;

if (!$reservation_id) {
    echo json_encode(['success' => false, 'message' => 'Reservation ID is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Verify the reservation belongs to the user
$stmt = $conn->prepare("SELECT status FROM reservations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Reservation not found or unauthorized']);
    exit;
}

$reservation = $result->fetch_assoc();

// Only allow cancellation of pending or approved reservations
if (!in_array($reservation['status'], ['pending', 'approved'])) {
    echo json_encode(['success' => false, 'message' => 'Only pending or approved reservations can be cancelled']);
    exit;
}

// Update reservation status to cancelled
$stmt = $conn->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservation_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Reservation cancelled successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel reservation']);
}

$stmt->close();
$conn->close();
?>
