<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) { $data = $_POST ?: []; }

    $token = trim($data['token'] ?? '');
    $password = (string)($data['password'] ?? '');

    if ($token === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Token and password are required']);
        exit;
    }

    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
        exit;
    }

    // Find token
    $stmt = $conn->prepare('SELECT user_id, expires_at FROM password_resets WHERE token = ?');
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid or expired token']);
        exit;
    }
    $row = $result->fetch_assoc();

    if (strtotime($row['expires_at']) < time()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Token has expired']);
        exit;
    }

    // Update password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
    $stmt->bind_param('si', $hash, $row['user_id']);
    $stmt->execute();

    // Delete token
    $stmt = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
    $stmt->bind_param('s', $token);
    $stmt->execute();

    echo json_encode(['success' => true, 'message' => 'Password has been reset successfully']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
