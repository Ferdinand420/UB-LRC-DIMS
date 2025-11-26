<?php
// API endpoint to get recent activity across the system
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
$is_librarian = is_librarian();
$activities = [];

// Get recent reservations
if ($is_librarian) {
    // Librarians see all reservations
    $sql = "
        SELECT r.id, r.reservation_date, r.start_time, r.status, r.created_at,
               rm.name as room_name,
               u.full_name as user_name,
               'reservation' as activity_type
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
        LIMIT 20
    ";
    $stmt = $conn->prepare($sql);
} else {
    // Students see only their own reservations
    $sql = "
        SELECT r.id, r.reservation_date, r.start_time, r.status, r.created_at,
               rm.name as room_name,
               'reservation' as activity_type
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
        LIMIT 20
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}
$stmt->close();

// Get recent feedback (librarians see all, students see their own)
if ($is_librarian) {
    $sql = "
        SELECT f.id, f.message, f.status, f.created_at,
               u.full_name as user_name,
               'feedback' as activity_type
        FROM feedback f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC
        LIMIT 20
    ";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "
        SELECT f.id, f.message, f.status, f.created_at,
               'feedback' as activity_type
        FROM feedback f
        WHERE f.user_id = ?
        ORDER BY f.created_at DESC
        LIMIT 20
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}
$stmt->close();

// Get violations (only for librarians or if student has violations)
if ($is_librarian) {
    $sql = "
        SELECT v.id, v.description, v.created_at,
               u.full_name as user_name,
               rm.name as room_name,
               'violation' as activity_type
        FROM violations v
        JOIN users u ON v.user_id = u.id
        JOIN rooms rm ON v.room_id = rm.id
        ORDER BY v.created_at DESC
        LIMIT 20
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
}

// Sort all activities by created_at timestamp (most recent first)
usort($activities, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Limit to 15 most recent activities
$activities = array_slice($activities, 0, 15);

echo json_encode([
    'success' => true,
    'activities' => $activities
]);
?>
