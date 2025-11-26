<?php
// Reset ALL test user passwords to 'password123'
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config/db.php';

echo "<h2>Reset All Test Passwords</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .success { color: green; }
    .failed { color: red; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background: #7d0920; color: white; }
</style>";

// Generate ONE hash for all users
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);

echo "<p><strong>Password:</strong> password123</p>";
echo "<p><strong>New Hash:</strong> <code>$hash</code></p>";
echo "<p><strong>Hash Length:</strong> " . strlen($hash) . " characters</p>";

// Test the hash immediately
$test_verify = password_verify($password, $hash);
echo "<p><strong>Immediate Verification:</strong> " . ($test_verify ? "<span class='success'>✓ SUCCESS</span>" : "<span class='failed'>✗ FAILED</span>") . "</p>";

if (!$test_verify) {
    echo "<p class='failed'>ERROR: Generated hash doesn't verify! PHP configuration issue.</p>";
    exit;
}

echo "<hr>";
echo "<h3>Updating All Users...</h3>";

// Get all users
$result = $conn->query("SELECT id, email, role FROM users ORDER BY id");
$users = $result->fetch_all(MYSQLI_ASSOC);

echo "<table>";
echo "<tr><th>ID</th><th>Email</th><th>Role</th><th>Update Status</th><th>Verify Test</th></tr>";

foreach ($users as $user) {
    echo "<tr>";
    echo "<td>{$user['id']}</td>";
    echo "<td>{$user['email']}</td>";
    echo "<td>{$user['role']}</td>";
    
    // Update password
    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $user['id']);
    
    if ($stmt->execute()) {
        echo "<td class='success'>✓ Updated</td>";
        
        // Immediately read back and verify
        $check_stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $check_stmt->bind_param("i", $user['id']);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $stored = $check_result->fetch_assoc();
        
        $verify = password_verify($password, $stored['password_hash']);
        if ($verify) {
            echo "<td class='success'>✓ Verified</td>";
        } else {
            echo "<td class='failed'>✗ Failed (hash: " . substr($stored['password_hash'], 0, 20) . "...)</td>";
        }
        $check_stmt->close();
    } else {
        echo "<td class='failed'>✗ Update failed</td>";
        echo "<td>-</td>";
    }
    $stmt->close();
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h3>Final Authentication Test</h3>";

require_once '../includes/auth.php';

$test_email = 'student@ub.edu.ph';
echo "<p>Testing: <strong>$test_email</strong> with password: <strong>password123</strong></p>";

$auth_result = authenticate_user($test_email, $password);

if ($auth_result) {
    echo "<p class='success'>✓✓✓ AUTHENTICATION SUCCESSFUL!</p>";
    echo "<pre>" . print_r($auth_result, true) . "</pre>";
    echo "<hr>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 5px; border: 2px solid #28a745;'>";
    echo "<h3 style='color: #155724; margin-top: 0;'>✓ Login Fixed!</h3>";
    echo "<p>All user passwords have been reset to: <strong>password123</strong></p>";
    echo "<p>Test accounts:</p>";
    echo "<ul>";
    foreach ($users as $user) {
        echo "<li>{$user['email']} ({$user['role']})</li>";
    }
    echo "</ul>";
    echo "<p><a href='../index.php' style='display: inline-block; padding: 12px 24px; background: #7d0920; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>Go to Login Page</a></p>";
    echo "</div>";
} else {
    echo "<p class='failed'>✗ AUTHENTICATION STILL FAILING</p>";
    echo "<p>Check that auth.php is using the correct database connection</p>";
}
?>
