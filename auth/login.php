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
    header('Location: /ub-lrc-dims/index.php?error=missing');
    exit;
}

// ✅ FIX: Validate email domain is @ub.edu.ph
if (!str_ends_with(strtolower($email), '@ub.edu.ph')) {
    error_log("Login failed - invalid email domain: $email");
    header('Location: /ub-lrc-dims/index.php?error=invalid_domain&email=' . urlencode($email));
    exit;
}

// Authenticate against database
$user = authenticate_user($email, $password);
error_log("Auth result: " . ($user ? "Success - Role: {$user['role']}" : "Failed"));

if (!$user) {
    error_log("Login failed - invalid credentials");
    header('Location: /ub-lrc-dims/index.php?error=invalid&email=' . urlencode($email));
    exit;
}

// Login with database role and data (ignore form role, trust DB)
login_user($user['email'], $user['role'], $user['id']);
error_log("Session set - Role: " . get_role() . ", Email: " . get_user_email());

if (get_role() === 'librarian') {
    error_log("Redirecting to librarian page");
    header('Location: /ub-lrc-dims/pages/librarian.php');
    exit;
}
error_log("Redirecting to dashboard");
header('Location: /ub-lrc-dims/pages/dashboard.php');
exit;
