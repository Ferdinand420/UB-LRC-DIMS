<?php
echo "<h2>POST Debug</h2>";
echo "<pre>";
echo "POST data:\n";
print_r($_POST);
echo "\nSessions:\n";
session_start();
print_r($_SESSION);
echo "</pre>";
echo '<p><a href="/ub-lrc-dims/index.php">Back</a></p>';
?>