<?php
// API endpoint to get feedback
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure user is logged in; allow librarians without requiring numeric user_id (some older sessions)
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

// Discover feedback table shape to support both new and legacy schemas
$columns = [];
$colResult = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'feedback'");
if ($colResult) {
    while ($c = $colResult->fetch_assoc()) {
        $columns[] = $c['COLUMN_NAME'];
    }
    $colResult->close();
}

$hasCondition = in_array('condition_status', $columns, true);
$hasFeedbackText = in_array('feedback_text', $columns, true);
$hasMessage = in_array('message', $columns, true);
$hasReviewedAt = in_array('reviewed_at', $columns, true);
$hasReviewedBy = in_array('reviewed_by', $columns, true);
$hasStudentId = in_array('student_id', $columns, true);
$hasUserId = in_array('user_id', $columns, true);
$hasFeedbackId = in_array('feedback_id', $columns, true);
$hasId = in_array('id', $columns, true);
$hasCreatedAt = in_array('created_at', $columns, true);
$hasStatus = in_array('status', $columns, true);
$hasRoomId = in_array('room_id', $columns, true);

$studentColumn = $hasStudentId ? 'student_id' : ($hasUserId ? 'user_id' : null);

// Build select pieces based on available columns
$idSelect = $hasFeedbackId ? 'f.feedback_id' : ($hasId ? 'f.id' : 'NULL');
$selectParts = ["$idSelect as id", $hasCreatedAt ? 'f.created_at' : 'NOW() as created_at'];
$selectParts[] = $hasCondition ? "f.condition_status" : "NULL as condition_status";
if ($hasFeedbackText) {
    $selectParts[] = "f.feedback_text";
} elseif ($hasMessage) {
    $selectParts[] = "f.message as feedback_text";
} else {
    $selectParts[] = "'' as feedback_text";
}
$selectParts[] = $hasReviewedAt ? "f.reviewed_at" : "NULL as reviewed_at";
$selectParts[] = $hasReviewedBy ? "f.reviewed_by" : "NULL as reviewed_by"; // This line is unchanged
$selectParts[] = $hasStatus ? "f.status" : "NULL as status";
$selectParts[] = $hasRoomId ? "f.room_id" : "NULL as room_id";

// Join info for user names/emails (best-effort)
$userJoin = '';
$userName = "'' as user_name";
$userEmail = "'' as user_email";
if ($studentColumn === 'student_id') {
    $userJoin = " LEFT JOIN students s ON f.student_id = s.student_id";
    $userName = "s.full_name as user_name";
    $userEmail = "s.ub_mail as user_email";
} elseif ($studentColumn === 'user_id') {
    $userJoin = " LEFT JOIN users u ON f.user_id = u.id";
    $userName = "u.full_name as user_name";
    $userEmail = "u.email as user_email";
}

// Room join (best-effort)
$roomJoin = '';
$roomSelect = "'' as room_name";
if ($hasRoomId) {
    // detect room name column
    $roomCols = [];
    $rc = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'rooms'");
    if ($rc) {
        while ($r = $rc->fetch_assoc()) { $roomCols[] = $r['COLUMN_NAME']; }
        $rc->close();
    }
    $roomNameCol = in_array('room_name', $roomCols, true) ? 'room_name' : (in_array('name', $roomCols, true) ? 'name' : null);
    if ($roomNameCol) {
        $roomJoin = " LEFT JOIN rooms rm ON f.room_id = rm.room_id";
        $roomSelect = "rm.$roomNameCol as room_name";
    }
}

// Librarians see all feedback, students see only their own
if ($is_librarian) {
    $reviewerSelect = $hasReviewedBy ? 'l.full_name as reviewed_by_name' : "NULL as reviewed_by_name";
    $reviewerJoin = $hasReviewedBy ? ' LEFT JOIN librarians l ON f.reviewed_by = l.librarian_id' : '';
    $sql = "
        SELECT " . implode(', ', $selectParts) . ",
               $userName,
               $userEmail,
               $roomSelect,
               $reviewerSelect
        FROM feedback f
        $userJoin
        $roomJoin
        $reviewerJoin
        ORDER BY f.created_at DESC
        LIMIT 100
    ";
    $stmt = $conn->prepare($sql);
} else {
    if (!$studentColumn) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Feedback schema not supported']);
        exit;
    }
    $sql = "
         SELECT " . implode(', ', $selectParts) . ",
             $userName,
             $userEmail,
             $roomSelect
         FROM feedback f
         $userJoin
         $roomJoin
         WHERE f.$studentColumn = ?
         ORDER BY f.created_at DESC
        ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$feedback_list = [];

while ($row = $result->fetch_assoc()) {
    $feedback_list[] = $row;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'feedback' => $feedback_list
]);
?>
