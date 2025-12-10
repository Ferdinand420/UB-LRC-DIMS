<?php
// API endpoint to get recent activity across the system
header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure user is logged in; allow librarians even if user_id missing in older sessions
$user_id = get_user_id();
$is_librarian = is_librarian();
$role = get_role();

if (!$role) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!$is_librarian && !$user_id) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Introspect schema to support legacy/new tables
function table_has($conn, $table, $column) {
    $stmt = $conn->prepare("SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1");
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

$hasStudentId = table_has($conn, 'reservations', 'student_id');
$hasUserIdRes = table_has($conn, 'reservations', 'user_id');
$hasRoomName = table_has($conn, 'rooms', 'room_name');
$roomNameCol = $hasRoomName ? 'room_name' : (table_has($conn, 'rooms', 'name') ? 'name' : null);

$hasFeedbackText = table_has($conn, 'feedback', 'feedback_text');
$hasMessage = table_has($conn, 'feedback', 'message');
$hasCondition = table_has($conn, 'feedback', 'condition_status');
$hasStudentIdFb = table_has($conn, 'feedback', 'student_id');
$hasUserIdFb = table_has($conn, 'feedback', 'user_id');

$hasViolations = table_has($conn, 'violations', 'violation_id');
$hasStudentIdViol = table_has($conn, 'violations', 'student_id');
$hasUserIdViol = table_has($conn, 'violations', 'user_id');
$hasViolationType = table_has($conn, 'violations', 'violation_type');
$hasLoggedBy = table_has($conn, 'violations', 'logged_by');

$activities = [];

// Reservations
if ($hasStudentId || $hasUserIdRes) {
    $select = "r.reservation_id, r.reservation_date, r.start_time, r.status, r.created_at";
    if ($roomNameCol) {
        $select .= ", rm.$roomNameCol as room_name";
    } else {
        $select .= ", NULL as room_name";
    }
    $userName = $hasStudentId ? 's.full_name' : ($hasUserIdRes ? 'u.full_name' : "''");
    $select .= ", $userName as user_name, 'reservation' as activity_type";

    $join = $roomNameCol ? " JOIN rooms rm ON r.room_id = rm.room_id" : '';
    if ($hasStudentId) {
        $join .= " LEFT JOIN students s ON r.student_id = s.student_id";
    } elseif ($hasUserIdRes) {
        $join .= " LEFT JOIN users u ON r.user_id = u.id";
    }

    if ($is_librarian) {
        $sql = "SELECT $select FROM reservations r$join ORDER BY r.created_at DESC LIMIT 20";
        $stmt = $conn->prepare($sql);
    } else {
        $idCol = $hasStudentId ? 'student_id' : 'user_id';
        $sql = "SELECT $select FROM reservations r$join WHERE r.$idCol = ? ORDER BY r.created_at DESC LIMIT 20";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $user_id);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $activities[] = $row;
    }
    $stmt->close();
}

// Feedback
if ($hasFeedbackText || $hasMessage) {
    $msgCol = $hasFeedbackText ? 'feedback_text' : 'message';
    $select = "f.feedback_id, f.$msgCol as message, f.created_at, 'feedback' as activity_type";
    $select .= $hasCondition ? ", f.condition_status" : ", NULL as condition_status";
    $userJoin = '';
    $userName = "''";
    if ($hasStudentIdFb) {
        $userJoin = " LEFT JOIN students s ON f.student_id = s.student_id";
        $userName = 's.full_name';
    } elseif ($hasUserIdFb) {
        $userJoin = " LEFT JOIN users u ON f.user_id = u.id";
        $userName = 'u.full_name';
    }
    $select .= ", $userName as user_name";

    if ($is_librarian) {
        $sql = "SELECT $select FROM feedback f$userJoin ORDER BY f.created_at DESC LIMIT 20";
        $stmt = $conn->prepare($sql);
    } else {
        if ($hasStudentIdFb) {
            $sql = "SELECT $select FROM feedback f$userJoin WHERE f.student_id = ? ORDER BY f.created_at DESC LIMIT 10";
        } elseif ($hasUserIdFb) {
            $sql = "SELECT $select FROM feedback f$userJoin WHERE f.user_id = ? ORDER BY f.created_at DESC LIMIT 10";
        } else {
            $sql = null;
        }
        if ($sql) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
        } else {
            $stmt = null;
        }
    }
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $activities[] = $row;
        }
        $stmt->close();
    }
}

// Violations (librarian only in this view)
if ($is_librarian && $hasViolations) {
    $select = "v.violation_id, v.created_at, 'violation' as activity_type";
    $select .= $hasViolationType ? ", v.violation_type" : ", NULL as violation_type";
    $select .= table_has($conn, 'violations', 'description') ? ", v.description" : ", NULL as description";
    $select .= table_has($conn, 'violations', 'status') ? ", v.status" : ", NULL as status";

    $join = '';
    $userName = "''";
    if ($hasStudentIdViol) {
        $join .= " LEFT JOIN students s ON v.student_id = s.student_id";
        $userName = 's.full_name';
    } elseif ($hasUserIdViol) {
        $join .= " LEFT JOIN users u ON v.user_id = u.id";
        $userName = 'u.full_name';
    }
    $select .= ", $userName as user_name";

    if ($roomNameCol && table_has($conn, 'violations', 'room_id')) {
        $join .= " LEFT JOIN rooms rm ON v.room_id = rm.room_id";
        $select .= ", rm.$roomNameCol as room_name";
    } else {
        $select .= ", NULL as room_name";
    }

    $sql = "SELECT $select FROM violations v$join ORDER BY v.created_at DESC LIMIT 20";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
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
