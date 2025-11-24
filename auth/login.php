<?php
require_once __DIR__ . '/../includes/auth.php';
// Very basic mock login (no password verification yet)
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? 'student';
if (!$email || !$password) {
    header('Location: /ub-lrc-dims/index.php?error=missing');
    exit;
}
login_user($email, $role);
if (get_role() === 'librarian') {
    header('Location: /ub-lrc-dims/pages/librarian.php');
    exit;
}
header('Location: /ub-lrc-dims/pages/dashboard.php');
exit;
