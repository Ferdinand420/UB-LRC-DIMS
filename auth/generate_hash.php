<?php
// Generate correct password hash for 'password123'
$password = 'password123';
$hash = password_hash($password, PASSWORD_BCRYPT);
echo "New hash for 'password123':\n";
echo $hash . "\n\n";
echo "Copy this and update your seed.sql file";
?>