<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

$email = $_POST['email'] ?? '';
if (!$email) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Email is required']);
    exit;
}

// Find user
$stmt = $conn->prepare('SELECT id, email FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

// Respond success even if user not found to avoid enumeration
if (!$user) {
    echo json_encode(['ok' => true]);
    exit;
}

// Create password_resets table if not exists (demo safety)
$conn->query('CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_token (token),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

// Generate token
$token = bin2hex(random_bytes(32));
$expiresAt = date('Y-m-d H:i:s', time() + 60 * 60); // 1 hour

// Upsert: remove existing tokens for user
$del = $conn->prepare('DELETE FROM password_resets WHERE user_id = ?');
$del->bind_param('i', $user['id']);
$del->execute();
$del->close();

$ins = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
$ins->bind_param('iss', $user['id'], $token, $expiresAt);
$ins->execute();
$ins->close();

// In a real app, send email. For demo, return token.
echo json_encode([
    'ok' => true,
    'token' => $token
]);
exit;
?>