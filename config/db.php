<?php
// Database configuration - supports environment variables with fallback to defaults
// For production: copy .env.example to .env and update values
// For development: defaults work with XAMPP
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
$dbname = getenv('DB_NAME') ?: "ub_lrc_dims";

// Create connection with error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Set UTF-8 encoding for all queries
    $conn->set_charset('utf8mb4');
} catch (Exception $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Database connection error. Please contact administrator.");
}

/**
 * Execute prepared statement safely
 * Note: This helper is defined but currently not widely used.
 * All API endpoints use individual prepared statements for clarity.
 * 
 * @param mysqli $conn Database connection
 * @param string $sql SQL query with ? placeholders
 * @param array $params Query parameters (all cast to string)
 * @return mysqli_stmt Prepared statement
 */
function db_query($conn, $sql, $params = []) {
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        // Note: This assumes all parameters are strings
        // For type-specific binding, use individual bind_param calls
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt;
}
?>