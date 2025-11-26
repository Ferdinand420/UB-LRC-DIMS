<?php
// API endpoint to get report statistics (Librarians only)
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

// Get date range from query params (optional)
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // Default: first day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Default: today

// Total reservations
$stmt = $conn->prepare("
    SELECT COUNT(*) as total,
           SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
           SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
           SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
           SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
    FROM reservations
    WHERE reservation_date BETWEEN ? AND ?
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$reservation_stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Room utilization
$stmt = $conn->prepare("
    SELECT r.name, COUNT(res.id) as reservation_count
    FROM rooms r
    LEFT JOIN reservations res ON r.id = res.room_id 
        AND res.reservation_date BETWEEN ? AND ?
        AND res.status IN ('approved', 'pending')
    GROUP BY r.id, r.name
    ORDER BY reservation_count DESC
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$room_utilization = [];
while ($row = $result->fetch_assoc()) {
    $room_utilization[] = $row;
}
$stmt->close();

// Top users (most active students)
$stmt = $conn->prepare("
    SELECT u.full_name, u.email, COUNT(r.id) as reservation_count
    FROM users u
    JOIN reservations r ON u.id = r.user_id
    WHERE r.reservation_date BETWEEN ? AND ?
    AND u.role = 'student'
    GROUP BY u.id, u.full_name, u.email
    ORDER BY reservation_count DESC
    LIMIT 10
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$top_users = [];
while ($row = $result->fetch_assoc()) {
    $top_users[] = $row;
}
$stmt->close();

// Feedback stats
$stmt = $conn->prepare("
    SELECT COUNT(*) as total,
           SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_feedback,
           SUM(CASE WHEN status = 'reviewed' THEN 1 ELSE 0 END) as reviewed,
           SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved
    FROM feedback
    WHERE created_at BETWEEN ? AND ?
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$feedback_stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Violations count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM violations WHERE created_at BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$violation_count = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Peak hours (when most reservations start)
$stmt = $conn->prepare("
    SELECT HOUR(start_time) as hour, COUNT(*) as count
    FROM reservations
    WHERE reservation_date BETWEEN ? AND ?
    AND status IN ('approved', 'pending')
    GROUP BY HOUR(start_time)
    ORDER BY count DESC
    LIMIT 5
");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
$peak_hours = [];
while ($row = $result->fetch_assoc()) {
    $peak_hours[] = [
        'hour' => $row['hour'],
        'count' => $row['count'],
        'time_range' => sprintf("%02d:00 - %02d:00", $row['hour'], $row['hour'] + 1)
    ];
}
$stmt->close();

echo json_encode([
    'success' => true,
    'date_range' => [
        'start' => $start_date,
        'end' => $end_date
    ],
    'reservations' => $reservation_stats,
    'room_utilization' => $room_utilization,
    'top_users' => $top_users,
    'feedback' => $feedback_stats,
    'violations' => $violation_count,
    'peak_hours' => $peak_hours
]);
?>
