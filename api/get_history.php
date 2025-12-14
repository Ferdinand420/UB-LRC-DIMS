<?php
/**
 * API Endpoint: Get History
 * 
 * Retrieves historical records (reservations, feedback, violations) based on user role and filter.
 * Supports filtering by type: 'reservations', 'feedback', 'violations', or 'all'
 * 
 * Librarians see all records; Students see only their own.
 * Uses prepared statements for all database queries (security).
 * 
 * Query parameters: ?type=reservations|feedback|violations|all
 * Response: { "success": bool, "history": [...] }
 */
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Verify authentication
if (!get_user_id()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = get_user_id();
$is_librarian = is_librarian();
$type = $_GET['type'] ?? 'all';  // Query parameter: 'reservations', 'feedback', 'violations', or 'all'

$history = [];  // Accumulates results from each section

// Fetch Reservations
// Status filter: Exclude pending (show only completed/approved/rejected/cancelled)
if ($type === 'all' || $type === 'reservations') {
    if ($is_librarian) {
        // Librarians see all non-pending reservations with approval details
        $sql = "
                        SELECT 'reservation' as type, r.reservation_id as id, r.reservation_date, r.start_time, r.end_time, 
                                     r.status, r.created_at, r.approved_at, r.purpose, r.group_members,
                                     rm.room_name,
                                     s.full_name as user_name, s.ub_mail as user_email,
                                     l.full_name as approved_by_name
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.room_id
            JOIN students s ON r.student_id = s.student_id
            LEFT JOIN librarians l ON r.librarian_id = l.librarian_id
            WHERE r.status != 'pending'
            ORDER BY r.created_at DESC
            LIMIT 50
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "
                 SELECT 'reservation' as type, r.reservation_id as id, r.reservation_date, r.start_time, r.end_time,
                     r.status, r.created_at, r.approved_at, r.purpose, r.group_members,
                   rm.room_name
            FROM reservations r
            JOIN rooms rm ON r.room_id = rm.room_id
            WHERE r.student_id = ?
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
        // Map group_members to student_ids array for UI
        if (!empty($row['group_members'])) {
            $row['student_ids'] = explode(',', $row['group_members']);
        } else {
            $row['student_ids'] = [];
        }
        $history[] = $row;
    }
    
    if (isset($stmt)) $stmt->close();
}

// Get feedback
if ($type === 'all' || $type === 'feedback') {
    if ($is_librarian) {
        $sql = "
            SELECT 'feedback' as type, f.feedback_id as id, f.condition_status, f.feedback_text, f.created_at,
                   s.full_name as user_name, s.ub_mail as user_email
            FROM feedback f
            JOIN students s ON f.student_id = s.student_id
            ORDER BY f.created_at DESC
            LIMIT 50
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "
            SELECT 'feedback' as type, feedback_id as id, condition_status, feedback_text, created_at
            FROM feedback
            WHERE student_id = ?
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

// Get violations
if ($type === 'all' || $type === 'violations') {
    if ($is_librarian) {
        $sql = "
            SELECT 'violation' as type, v.violation_id as id, v.description, v.violation_type, v.status, v.created_at,
                   s.full_name as user_name, s.ub_mail as user_email,
                   r.room_name,
                   l.full_name as logged_by_name
            FROM violations v
            LEFT JOIN students s ON v.student_id = s.student_id
            LEFT JOIN rooms r ON v.room_id = r.room_id
            LEFT JOIN librarians l ON v.librarian_id = l.librarian_id
            ORDER BY v.created_at DESC
            LIMIT 50
        ";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error']);
            exit;
        }
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $sql = "
            SELECT 'violation' as type, v.violation_id as id, v.description, v.violation_type, v.status, v.created_at,
                   r.room_name,
                   l.full_name as logged_by_name
            FROM violations v
            LEFT JOIN rooms r ON v.room_id = r.room_id
            LEFT JOIN librarians l ON v.librarian_id = l.librarian_id
            WHERE v.student_id = ?
            ORDER BY v.created_at DESC
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

// Sort by created_at descending
usort($history, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

echo json_encode([
    'success' => true,
    'history' => $history
]);
ob_end_flush();
?>
