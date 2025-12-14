<?php
/**
 * API Endpoint: Dashboard Statistics
 * 
 * Provides dashboard statistics based on user role:
 * - Students: Their own reservation counts and feedback status
 * - Librarians: System-wide stats (all reservations, violations, feedback)
 * 
 * Returns counts for pending/approved/total items to display in dashboard widgets.
 * Used by dashboard.php for real-time stats display.
 */
ob_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
ob_end_clean();

// Verify user is authenticated
if (!get_role()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = get_user_id();
$role = get_role();

try {
    // Retrieve reservation statistics
    // For students: filtered to their own reservations
    // For librarians: aggregated across all students
    if ($role === 'student') {
        // Student dashboard: Count their reservations by status
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
            FROM reservations 
            WHERE student_id = ?
        ");
        $stmt->bind_param('i', $user_id);
    } else {
        // Librarians see all
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
            FROM reservations
        ");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $stats = $result->fetch_assoc();
    
    // Get feedback count
    if ($role === 'student') {
        $stmt = $conn->prepare("SELECT COUNT(*) as feedback FROM feedback WHERE student_id = ?");
        $stmt->bind_param('i', $user_id);
    } else {
        $stmt = $conn->prepare("SELECT COUNT(*) as feedback FROM feedback");
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $feedback = $result->fetch_assoc();
    
    echo json_encode([
        'total' => (int)$stats['total'],
        'pending' => (int)$stats['pending'],
        'approved' => (int)$stats['approved'],
        'feedback' => (int)$feedback['feedback']
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
    error_log("Dashboard stats error: " . $e->getMessage());
}
ob_end_flush();
?>