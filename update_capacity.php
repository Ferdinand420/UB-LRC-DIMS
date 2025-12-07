<?php
require_once __DIR__ . '/config/db.php';

$query = "UPDATE rooms SET capacity = 10 WHERE name LIKE 'Discussion Room%'";
if (mysqli_query($conn, $query)) {
    $affected = mysqli_affected_rows($conn);
    echo "Updated $affected room(s) to capacity 10\n";
} else {
    echo "Update failed: " . mysqli_error($conn);
}
?>
