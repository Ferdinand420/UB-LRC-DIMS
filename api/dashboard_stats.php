<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

// Require login
if (!get_role()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = get_user_id();
$role = get_role();

try {
    // Get reservation stats (filter by user if student)
    if ($role === 'student') {
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved
            FROM reservations 
            WHERE user_id = ?
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
        $stmt = $conn->prepare("SELECT COUNT(*) as feedback FROM feedback WHERE user_id = ?");
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
?>