<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if (!$token || !$password || !$confirm) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Missing fields']);
    exit;
}
if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Passwords do not match']);
    exit;
}
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Password must be at least 8 characters']);
    exit;
}

// Validate token
$stmt = $conn->prepare('SELECT pr.user_id, u.email, pr.expires_at FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = ?');
$stmt->bind_param('s', $token);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid token']);
    exit;
}
if (strtotime($row['expires_at']) < time()) {
    // expired
    $del = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
    $del->bind_param('s', $token);
    $del->execute();
    $del->close();
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Token expired']);
    exit;
}

// Update password
$newHash = password_hash($password, PASSWORD_BCRYPT);
$upd = $conn->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
$uid = (int)$row['user_id'];
$upd->bind_param('si', $newHash, $uid);
$upd->execute();
$upd->close();

// Consume token
$del = $conn->prepare('DELETE FROM password_resets WHERE token = ?');
$del->bind_param('s', $token);
$del->execute();
$del->close();

echo json_encode(['ok' => true]);
exit;
?>