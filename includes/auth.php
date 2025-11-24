<?php
// Basic session + role helpers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function get_role(): ?string {
    return $_SESSION['role'] ?? null;
}
function is_student(): bool { return get_role() === 'student'; }
function is_librarian(): bool { return get_role() === 'librarian'; }
function require_login(): void {
    if (!get_role()) {
        header('Location: /ub-lrc-dims/index.php');
        exit;
    }
}
function login_user(string $email, string $role = 'student'): void {
    $role = strtolower($role);
    if (!in_array($role, ['student','librarian'], true)) {
        $role = 'student';
    }
    $_SESSION['role'] = $role;
    $_SESSION['email'] = $email;
}
function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}
?>