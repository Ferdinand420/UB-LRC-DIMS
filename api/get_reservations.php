<?php
// API endpoint to get user's reservations
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

// Librarians see all reservations, students see only their own
if ($is_librarian) {
    $sql = "
        SELECT r.id, r.reservation_date, r.start_time, r.end_time, r.purpose, r.status, r.created_at,
               rm.name as room_name, rm.capacity,
               u.full_name as user_name, u.email as user_email
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.reservation_date DESC, r.start_time DESC
        LIMIT 100
    ";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "
        SELECT r.id, r.reservation_date, r.start_time, r.end_time, r.purpose, r.status, r.created_at,
               rm.name as room_name, rm.capacity
        FROM reservations r
        JOIN rooms rm ON r.room_id = rm.id
        WHERE r.user_id = ?
        ORDER BY r.reservation_date DESC, r.start_time DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$reservations = [];
$reservationIds = [];

while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
    $reservationIds[] = $row['id'];
}

// Attach student IDs
if (!empty($reservationIds)) {
    $placeholders = implode(',', array_fill(0, count($reservationIds), '?'));
    $types = str_repeat('i', count($reservationIds));
    $stmt = $conn->prepare("SELECT reservation_id, student_id_value FROM reservation_students WHERE reservation_id IN ($placeholders)");
    if ($stmt) {
        $stmt->bind_param($types, ...$reservationIds);
        $stmt->execute();
        $res = $stmt->get_result();
        $studentMap = [];
        while ($row = $res->fetch_assoc()) {
            $rid = $row['reservation_id'];
            if (!isset($studentMap[$rid])) {
                $studentMap[$rid] = [];
            }
            $studentMap[$rid][] = $row['student_id_value'];
        }
        $stmt->close();
    } else {
        $studentMap = [];
    }

    foreach ($reservations as &$item) {
        $item['students'] = $studentMap[$item['id']] ?? [];
    }
    unset($item);
}

$stmt->close();

echo json_encode([
    'success' => true,
    'reservations' => $reservations
]);
?>
