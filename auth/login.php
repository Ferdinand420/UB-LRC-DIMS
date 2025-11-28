<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'student';

// Debug log
error_log("Login attempt - Email: $email, Role: $role");

if (!$email || !$password) {
    error_log("Login failed - missing credentials");
    header('Location: /index.php?error=missing');
    exit;
}

// Authenticate against database (with fallback)
$user = authenticate_user($email, $password);
error_log("Auth result: " . ($user ? "Success - Role: {$user['role']}" : "Failed"));

if (!$user) {
    error_log("Login failed - invalid credentials");
    header('Location: /index.php?error=invalid&email=' . urlencode($email));
    exit;
}

// Login with database role (ignore form role, trust DB)
login_user($user['email'], $user['role'], $user['id']);
error_log("Session set - Role: " . get_role());

if (get_role() === 'librarian') {
    error_log("Redirecting to librarian page");
    header('Location: /pages/librarian.php');
    exit;
}
error_log("Redirecting to dashboard");
header('Location: /pages/dashboard.php');
exit;
