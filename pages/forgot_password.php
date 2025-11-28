<?php
// forgot_password.php
// Handles password reset requests for UB-LRC-DIMS

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (!$email) {
        $error = 'Please enter your email.';
    } else {
        // Check if user exists
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $error = 'No account found with that email.';
        } else {
            // Generate a reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $user = $result->fetch_assoc();
            $stmt = $conn->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)');
            $stmt->bind_param('iss', $user['id'], $token, $expires);
            $stmt->execute();
            // Send email (pseudo-code)
            // mail($email, 'Password Reset', "Reset link: https://yourdomain.com/index.php?token=$token");
            $dev_link = '../index.php?token=' . urlencode($token);
            $success = 'A reset link has been sent to your email. For development, you can also use this link now: <a href="' . htmlspecialchars($dev_link) . '">Reset Password</a>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width:400px;margin:3rem auto;padding:2rem;background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <h2>Forgot Password</h2>
        <?php if (!empty($error)): ?>
            <div class="error" style="color:red;"><?= htmlspecialchars($error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="success" style="color:green;"><?= $success ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required style="width:100%;margin-bottom:1rem;">
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Reset Link</button>
        </form>
        <div style="margin-top:1rem;"><a href="student-login.php">Back to Login</a></div>
    </div>
</body>
</html>
