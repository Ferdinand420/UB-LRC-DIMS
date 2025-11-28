<?php
// Database configuration (supports local dev and Cloud Run/Cloud SQL)
$servername = getenv('DB_HOST') ?: 'localhost';
$username   = getenv('DB_USER') ?: 'root';
$password   = getenv('DB_PASSWORD') ?: '';
$dbname     = getenv('DB_NAME') ?: 'ub_lrc_dims';
$dbport     = (int)(getenv('DB_PORT') ?: 3306);
$dbsocket   = getenv('DB_SOCKET') ?: null; // e.g. /cloudsql/PROJECT:REGION:INSTANCE

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname, $dbport, $dbsocket ?: null);
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    http_response_code(500);
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