<?php
// Comprehensive Login Debug Tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Full Login Debug</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; font-weight: bold; }
    .failed { color: red; font-weight: bold; }
    .section { background: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #7d0920; }
    pre { background: #fff; padding: 10px; overflow-x: auto; }
</style>";

// Test credentials
$test_email = 'student@ub.edu.ph';
$test_password = 'password123';

echo "<div class='section'>";
echo "<h3>1. Database Connection Test</h3>";
try {
    require '../config/db.php';
    echo "<p class='success'>✓ Database connected</p>";
    echo "<p>Database: ub_lrc_dims</p>";
} catch (Exception $e) {
    echo "<p class='failed'>✗ Connection failed: " . $e->getMessage() . "</p>";
    exit;
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>2. User Lookup Test</h3>";
echo "<p>Looking up: <strong>$test_email</strong></p>";
$stmt = $conn->prepare("SELECT id, email, password_hash, role, full_name FROM users WHERE email = ?");
$stmt->bind_param('s', $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p class='failed'>✗ User not found in database</p>";
    echo "<p>Check if seed.sql was imported correctly</p>";
    exit;
}

$user = $result->fetch_assoc();
echo "<p class='success'>✓ User found</p>";
echo "<pre>" . print_r([
    'id' => $user['id'],
    'email' => $user['email'],
    'role' => $user['role'],
    'full_name' => $user['full_name']
], true) . "</pre>";
$stmt->close();
echo "</div>";

echo "<div class='section'>";
echo "<h3>3. Password Hash Analysis</h3>";
$hash = $user['password_hash'];
echo "<p>Hash length: <strong>" . strlen($hash) . "</strong> characters ";
if (strlen($hash) === 60) {
    echo "<span class='success'>(correct)</span></p>";
} else {
    echo "<span class='failed'>(should be 60)</span></p>";
}
echo "<p>Hash starts: <code>" . substr($hash, 0, 20) . "...</code></p>";
echo "<p>Hash ends: <code>..." . substr($hash, -10) . "</code></p>";
echo "<p>Full hash:</p>";
echo "<pre style='word-break: break-all;'>$hash</pre>";
echo "</div>";

echo "<div class='section'>";
echo "<h3>4. Password Verification Test</h3>";
echo "<p>Testing password: <strong>$test_password</strong></p>";
$verify_result = password_verify($test_password, $hash);
if ($verify_result) {
    echo "<p class='success'>✓ PASSWORD VERIFICATION SUCCESS</p>";
} else {
    echo "<p class='failed'>✗ PASSWORD VERIFICATION FAILED</p>";
    echo "<p>This means the hash in the database does not match 'password123'</p>";
    echo "<p>Generating new hash now...</p>";
    
    $new_hash = password_hash($test_password, PASSWORD_BCRYPT);
    echo "<p>New hash generated: <code>$new_hash</code></p>";
    echo "<p>Length: " . strlen($new_hash) . "</p>";
    
    // Update database
    $update_stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $update_stmt->bind_param('ss', $new_hash, $test_email);
    if ($update_stmt->execute()) {
        echo "<p class='success'>✓ Database updated with new hash</p>";
        
        // Verify again
        if (password_verify($test_password, $new_hash)) {
            echo "<p class='success'>✓ New hash verified successfully</p>";
        }
    } else {
        echo "<p class='failed'>✗ Failed to update database</p>";
    }
    $update_stmt->close();
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>5. Authentication Function Test</h3>";
require_once '../includes/auth.php';
$auth_result = authenticate_user($test_email, $test_password);
if ($auth_result) {
    echo "<p class='success'>✓ authenticate_user() returned user data</p>";
    echo "<pre>" . print_r($auth_result, true) . "</pre>";
} else {
    echo "<p class='failed'>✗ authenticate_user() returned NULL</p>";
    echo "<p>Check Apache error logs for details</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h3>6. Apache Error Log (last 20 lines)</h3>";
$error_log = 'C:/xampp/apache/logs/error.log';
if (file_exists($error_log)) {
    $lines = file($error_log);
    $recent = array_slice($lines, -20);
    echo "<pre style='font-size: 11px;'>" . htmlspecialchars(implode('', $recent)) . "</pre>";
} else {
    echo "<p>Error log not found at: $error_log</p>";
}
echo "</div>";

if ($auth_result) {
    echo "<div class='section' style='background: #d4edda; border-color: #28a745;'>";
    echo "<h3 style='color: #155724;'>✓ All Tests Passed!</h3>";
    echo "<p>Login should work now. Test credentials:</p>";
    echo "<ul>";
    echo "<li>Email: <strong>student@ub.edu.ph</strong></li>";
    echo "<li>Password: <strong>password123</strong></li>";
    echo "</ul>";
    echo "<p><a href='../index.php' style='display: inline-block; padding: 10px 20px; background: #7d0920; color: white; text-decoration: none; border-radius: 5px;'>Go to Login Page</a></p>";
    echo "</div>";
}
?>
