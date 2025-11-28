<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ub_lrc_dims";

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection error. Please contact administrator.");
}

// Helper function to safely execute queries
function db_query($conn, $sql, $params = []) {
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}
?>