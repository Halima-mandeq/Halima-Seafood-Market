<?php
// Includes/setup.php
include_once __DIR__ . '/db.php';

$sql = file_get_contents(__DIR__ . '/../database.sql');

if (mysqli_multi_query($conn, $sql)) {
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($conn));
    echo "Database setup successfully!";
} else {
    echo "Error setting up database: " . mysqli_error($conn);
}
?>
