<?php
require_once __DIR__ . '/../config/db.php';

$email = 'student@ub.edu.ph';

echo "<h2>Database Test</h2>";

try {
    $stmt = $conn->prepare("SELECT id, email, password_hash, role, full_name FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo "Rows found: " . $result->num_rows . "<br><br>";
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo "<pre>";
        echo "User data:\n";
        print_r([
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'full_name' => $user['full_name'],
            'hash_length' => strlen($user['password_hash']),
            'hash_starts_with' => substr($user['password_hash'], 0, 20)
        ]);
        echo "</pre>";
        
        // Test password verification
        $test_password = 'password123';
        $verify_result = password_verify($test_password, $user['password_hash']);
        echo "<br>Password verify result: " . ($verify_result ? 'SUCCESS' : 'FAILED') . "<br>";
        
        if (!$verify_result) {
            echo "<br>Hash in database: " . htmlspecialchars($user['password_hash']);
            echo "<br><br>Generate new hash: <a href='generate_hash.php'>Click here</a>";
        }
    } else {
        echo "User not found in database!<br>";
        echo "Check if the users table has data.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>