<?php
// Includes/reinit_db.php
include_once __DIR__ . '/db.php';

// Disable foreign key checks
mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

$tables = ['messages', 'orders', 'products', 'users'];
foreach ($tables as $table) {
    mysqli_query($conn, "DROP TABLE IF EXISTS $table");
}

mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

// Run setup
include_once __DIR__ . '/setup.php';

echo "\nDatabase re-initialized with new schema and base products!";
?>
