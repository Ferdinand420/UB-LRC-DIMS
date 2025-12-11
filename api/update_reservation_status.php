<?php
// API endpoint to approve/reject reservations
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
$reservation_id = $data['reservation_id'] ?? null;
$action = $data['action'] ?? ''; // 'approve' or 'reject'

// Validate
if (!$reservation_id || !in_array($action, ['approve', 'reject'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

// Check if reservation exists and is pending
$stmt = $conn->prepare("SELECT reservation_id, status FROM reservations WHERE reservation_id = ?");
$stmt->bind_param("i", $reservation_id);
$stmt->execute();
$result = $stmt->get_result();
$reservation = $result->fetch_assoc();
$stmt->close();

if (!$reservation) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Reservation not found']);
    exit;
}

if ($reservation['status'] !== 'pending') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Reservation is not pending']);
    exit;
}

// Update reservation status
$new_status = $action === 'approve' ? 'approved' : 'rejected';
$librarian_id = get_user_id();

$stmt = $conn->prepare("
    UPDATE reservations 
    SET status = ?, librarian_id = ?, approved_at = NOW() 
    WHERE reservation_id = ?
");
$stmt->bind_param("sii", $new_status, $librarian_id, $reservation_id);

if ($stmt->execute()) {
    $message = $action === 'approve' 
        ? 'Reservation approved successfully' 
        : 'Reservation rejected';
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'new_status' => $new_status
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update reservation']);
}

$stmt->close();
?>
