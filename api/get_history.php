<?php
// API endpoint to get history (reservations and feedback)
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
$type = $_GET['type'] ?? 'all'; // 'reservations', 'feedback', or 'all'

$history = [];
$reservationIds = [];

// Get reservations
if ($type === 'all' || $type === 'reservations') {
    if ($is_librarian) {
        $sql = "
            SELECT 'reservation' as type, r.id, r.reservation_date, r.start_time, r.end_time, 
                   r.status, r.created_at, r.approved_at, r.purpose,
                   rm.name as room_name,
                   u.full_name as user_name, u.email as user_email,
                   l.full_name as approved_by_name
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.id
            JOIN users u ON r.user_id = u.id
            LEFT JOIN users l ON r.approved_by = l.id
            WHERE r.status != 'pending'
            ORDER BY r.created_at DESC
            LIMIT 50
        ";
        $result = $conn->query($sql);
    } else {
        $sql = "
                 SELECT 'reservation' as type, r.id, r.reservation_date, r.start_time, r.end_time,
                     r.status, r.created_at, r.approved_at, r.purpose,
                   rm.name as room_name
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.id
            WHERE r.user_id = ?
            AND (r.reservation_date < CURDATE() OR r.status IN ('rejected', 'cancelled'))
            ORDER BY r.created_at DESC
            LIMIT 50
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
        $reservationIds[] = $row['id'];
    }
    
    if (isset($stmt)) $stmt->close();
}

// Get feedback
if ($type === 'all' || $type === 'feedback') {
    if ($is_librarian) {
        $sql = "
            SELECT 'feedback' as type, f.id, f.message, f.status, f.created_at,
                   u.full_name as user_name, u.email as user_email
            FROM feedback f
            JOIN users u ON f.user_id = u.id
            WHERE f.status IN ('reviewed', 'resolved')
            ORDER BY f.created_at DESC
            LIMIT 50
        ";
        $result = $conn->query($sql);
    } else {
        $sql = "
            SELECT 'feedback' as type, id, message, status, created_at
            FROM feedback
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT 50
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
    }

    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    
    if (isset($stmt)) $stmt->close();
}

// Attach student IDs for reservations
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

    foreach ($history as &$item) {
        if ($item['type'] === 'reservation') {
            $item['students'] = $studentMap[$item['id']] ?? [];
        }
    }
    unset($item);
}

// Sort by created_at descending
usort($history, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo json_encode([
    'success' => true,
    'history' => $history
]);
?>
