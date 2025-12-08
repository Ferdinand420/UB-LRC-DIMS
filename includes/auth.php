<?php
/**
 * Authentication & Authorization Helpers
 * 
 * Provides session management, role-based access control,
 * and user authentication functions.
 */

// Ensure session is started only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Get the current user's role from session
 * 
 * @return string|null 'student', 'librarian', or null if not logged in
 */
function get_role(): ?string {
    return $_SESSION['role'] ?? null;
}

/**
 * Check if current user is a student
 * 
 * @return bool True if user is logged in as student
 */
function is_student(): bool { return get_role() === 'student'; }

/**
 * Check if current user is a librarian
 * 
 * @return bool True if user is logged in as librarian
 */
function is_librarian(): bool { return get_role() === 'librarian'; }

/**
 * Require user to be logged in, redirect to index if not
 * 
 * @return void Exits if not logged in
 */
function require_login(): void {
    if (!get_role()) {
        header('Location: /ub-lrc-dims/index.php');
        exit;
    }
}

/**
 * Authenticate user with email and password
 * Verifies credentials against database using bcrypt hashing
 * 
 * @param string $email User email
 * @param string $password User password (plain text)
 * @return array|null User data on success, null on failure
 */
function authenticate_user(string $email, string $password): ?array {
    global $conn;
    try {
        if (!$conn) {
            require_once __DIR__ . '/../config/db.php';
        }
        // Fetch user with all necessary fields
        $stmt = $conn->prepare("SELECT id, email, password_hash, role, full_name FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            error_log("Auth: User not found - $email");
            return null;
        }
        $user = $result->fetch_assoc();
        // Verify password using bcrypt
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

/**
 * Log in a user by storing their data in session
 * Regenerates session ID for security
 * 
 * @param string $email User email
 * @param string $role User role (student|librarian)
 * @param int|null $user_id User ID from database
 * @return void
 */
function login_user(string $email, string $role = 'student', ?int $user_id = null): void {
    // Clear any existing session data for clean login
    $_SESSION = [];
    // Regenerate ID to prevent session fixation attacks
    session_regenerate_id(true);
    
    $role = strtolower($role);
    if (!in_array($role, ['student','librarian'], true)) {
        $role = 'student'; // Default to student if invalid
    }
    $_SESSION['role'] = $role;
    $_SESSION['email'] = $email;
    $_SESSION['user_id'] = $user_id;
}

/**
 * Get current user's ID from session
 * 
 * @return int|null User ID or null if not logged in
 */
function get_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Log out user by destroying session
 * Properly clears all session data and cookies
 * 
 * @return void
 */
function logout_user(): void {
    // Clear session array
    $_SESSION = [];
    // Also delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    // Destroy the session
    session_destroy();
}
?>