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
function login_user(string $email, string $role = 'student', ?int $user_id = null): void {
    // Clear any existing session data and regenerate ID
    $_SESSION = [];
    session_regenerate_id(true);
    
    $role = strtolower($role);
    if (!in_array($role, ['student','librarian'], true)) {
        $role = 'student';
    }
    $_SESSION['role'] = $role;
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $user_id;
}

function authenticate_user(string $email, string $password): ?array {
    global $conn;
    try {
        if (!$conn) {
            require_once __DIR__ . '/../config/db.php';
        }
        $stmt = $conn->prepare("SELECT id, email, password_hash, role, full_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            error_log("Auth: User not found - $email");
            return null;
        }
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password_hash'])) {
            error_log("Auth: Invalid password for - $email");
            return null;
        }
        error_log("Auth: Success - $email as {$user['role']}");
        return $user;
    } catch (Exception $e) {
        error_log("Auth DB error: " . $e->getMessage());
        return null;
    }
}

function get_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
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