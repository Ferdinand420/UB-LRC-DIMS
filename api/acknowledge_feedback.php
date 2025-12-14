<?php
// API endpoint to mark feedback as received/reviewed
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
ob_end_clean();

// Ensure user is librarian; allow legacy sessions with role check even if user_id missing
$role = get_role();
if ($role !== 'librarian') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized - Librarians only']);
    exit;
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
$feedback_id = 0;
if (is_array($data) && isset($data['feedback_id'])) {
    $feedback_id = (int)$data['feedback_id'];
} elseif (isset($_POST['feedback_id'])) {
    $feedback_id = (int)$_POST['feedback_id'];
}

if ($feedback_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid feedback ID']);
    exit;
}

$librarian_id = get_user_id();

// Detect feedback table shape (legacy/new)
function feedback_columns(mysqli $conn): array {
    $cols = [];
    $res = $conn->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'feedback'");
    if ($res) {
        while ($c = $res->fetch_assoc()) { $cols[] = $c['COLUMN_NAME']; }
        $res->close();
    }
    return $cols;
}

$columns = feedback_columns($conn);
$hasReviewedAt = in_array('reviewed_at', $columns, true);
$hasReviewedBy = in_array('reviewed_by', $columns, true);
$hasStatus = in_array('status', $columns, true);
$hasFeedbackId = in_array('feedback_id', $columns, true);
$hasId = in_array('id', $columns, true);

// Auto-migrate legacy feedback table to support acknowledgements
$migrationNeeded = !$hasReviewedAt || !$hasReviewedBy || !$hasStatus;
if ($migrationNeeded) {
    if (!$hasReviewedAt) {
        $conn->query("ALTER TABLE feedback ADD COLUMN reviewed_at TIMESTAMP NULL DEFAULT NULL");
    }
    if (!$hasReviewedBy) {
        $conn->query("ALTER TABLE feedback ADD COLUMN reviewed_by INT NULL");
    }
    if (!$hasStatus) {
        $conn->query("ALTER TABLE feedback ADD COLUMN status ENUM('new','reviewed','resolved') DEFAULT 'new'");
    }
    // Refresh column list after migration attempts
    $columns = feedback_columns($conn);
    $hasReviewedAt = in_array('reviewed_at', $columns, true);
    $hasReviewedBy = in_array('reviewed_by', $columns, true);
    $hasStatus = in_array('status', $columns, true);
}

$idColumn = $hasFeedbackId ? 'feedback_id' : ($hasId ? 'id' : null);
if (!$idColumn) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback schema not supported']);
    exit;
}

// Build SET clause depending on available columns
$setParts = [];
$types = '';
$params = [];
if ($hasReviewedAt) {
    $setParts[] = 'reviewed_at = NOW()';
}
if ($hasReviewedBy && $librarian_id) {
    $setParts[] = 'reviewed_by = ?';
    $types .= 'i';
    $params[] = $librarian_id;
}
if ($hasStatus) {
    $setParts[] = "status = 'reviewed'";
}

if (empty($setParts)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Feedback schema not supported']);
    exit;
}

$sql = "UPDATE feedback SET " . implode(', ', $setParts) . " WHERE $idColumn = ?";
$types .= 'i';
$params[] = $feedback_id;

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$ok = $stmt->execute();
$stmt->close();
if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Feedback marked as received.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update feedback.']);
}
ob_end_flush();
?>
?>
