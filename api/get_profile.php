<?php
// API endpoint to get user profile
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

// Get user data from appropriate table
if ($role === 'librarian') {
    $stmt = $conn->prepare("SELECT librarian_id as id, ub_mail as email, full_name, 'librarian' as role, created_at FROM librarians WHERE librarian_id = ?");
} else {
    $stmt = $conn->prepare("SELECT student_id as id, ub_mail as email, full_name, 'student' as role, created_at FROM students WHERE student_id = ?");
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Get statistics
$stats = [];

// Reservation count by status (only for students)
if ($role === 'student') {
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM reservations WHERE student_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['reservations'] = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Feedback count
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM feedback WHERE student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['feedback_count'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
} else {
    // For librarians, show different stats
    $stats['reservations'] = ['total' => 0, 'approved' => 0, 'pending' => 0, 'rejected' => 0, 'cancelled' => 0];
    $stats['feedback_count'] = 0;
}

// Violations count (only for students)
if ($role === 'student') {
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM violations WHERE student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stats['violations_count'] = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
} else {
    $stats['violations_count'] = 0;
}

// Recent activity (only for students)
if ($role === 'student') {
    $stmt = $conn->prepare("
        SELECT r.reservation_id, r.reservation_date, r.start_time, r.status, rm.room_name, r.created_at
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.room_id
        WHERE r.student_id = ?
        ORDER BY r.created_at DESC
        LIMIT 5
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $recent_reservations = [];
    while ($row = $result->fetch_assoc()) {
        $recent_reservations[] = $row;
    }
    $stmt->close();
} else {
    $recent_reservations = [];
}

echo json_encode([
    'success' => true,
    'user' => $user,
    'stats' => $stats,
    'recent_reservations' => $recent_reservations
]);
?>
