<?php
// reset-password.php
// Redirect to landing page and open Reset modal with token

$token = $_GET['token'] ?? '';
if ($token) {
    $dest = '../index.php?token=' . urlencode($token);
    header('Location: ' . $dest);
    exit;
}

// Fallback: no token provided
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container" style="max-width:520px;margin:3rem auto;padding:2rem;background:#fff;border-radius:10px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <h2>Reset Password</h2>
        <p>Invalid or missing token. Please request a new reset link.</p>
        <p><a class="btn btn-primary" href="../index.php" style="display:inline-block;margin-top:1rem;">Back to Home</a></p>
    </div>
</body>
</html>
