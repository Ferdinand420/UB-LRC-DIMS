<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

try {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    if (!is_array($data)) { $data = $_POST ?: []; }

    $email = trim($data['email'] ?? '');
    if ($email === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email is required']);
        exit;
    }

    // Find user
    $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Always respond success to avoid email enumeration
    if ($result->num_rows === 0) {
        echo json_encode(['success' => true, 'message' => 'If that email exists, a reset link has been sent.']);
        exit;
    }

    $user = $result->fetch_assoc();
    $user_id = (int)$user['id'];

    // Create token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

    $stmt = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
    $stmt->bind_param('iss', $user_id, $token, $expires);
    $stmt->execute();

    // Compose dev link for local use
    $dev_link = '/UB-LRC-DIMS/index.php?token=' . urlencode($token);

    echo json_encode([
        'success' => true,
        'message' => 'If that email exists, a reset link has been sent.',
        'link' => $dev_link
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
