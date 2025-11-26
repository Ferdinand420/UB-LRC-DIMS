<?php
// Fix Password Hash
// This script generates a proper bcrypt hash and updates the database

require_once '../config/db.php';

// Generate proper bcrypt hash for 'password123'
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<h2>Password Hash Fix</h2>";
echo "<p><strong>Password:</strong> password123</p>";
echo "<p><strong>New Hash:</strong> $hash</p>";
echo "<p><strong>Hash Length:</strong> " . strlen($hash) . " characters</p>";

// Update all users in seed data with this hash
$emails = ['student@ub.edu.ph', 'student2@ub.edu.ph', 'staff@ub.edu.ph', 'lib@ub.edu.ph'];

echo "<h3>Updating Database...</h3>";

foreach ($emails as $email) {
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $hash, $email);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>✓ Updated $email</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to update $email: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

echo "<hr>";
echo "<h3>Verification Test</h3>";

// Test the first user
$stmt = $conn->prepare("SELECT id, email, password_hash, role FROM users WHERE email = ?");
$test_email = 'student@ub.edu.ph';
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $stored_hash = $row['password_hash'];
    $verify = password_verify('password123', $stored_hash);
    
    echo "<p><strong>Test User:</strong> {$row['email']}</p>";
    echo "<p><strong>Stored Hash:</strong> $stored_hash</p>";
    echo "<p><strong>Hash Length:</strong> " . strlen($stored_hash) . " characters</p>";
    echo "<p><strong>Password Verify:</strong> " . ($verify ? '<span style="color: green; font-weight: bold;">SUCCESS ✓</span>' : '<span style="color: red; font-weight: bold;">FAILED ✗</span>') . "</p>";
    
    if ($verify) {
        echo "<hr>";
        echo "<h3 style='color: green;'>✓ Login is now fixed!</h3>";
        echo "<p><a href='../index.php' style='display: inline-block; padding: 10px 20px; background: #7d0920; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    }
} else {
    echo "<p style='color: red;'>User not found!</p>";
}

$stmt->close();
?>
