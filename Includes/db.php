<?php
// Halima Seafood Market - Shared Database Configuration
// Includes/db.php

$host = "localhost";
$username = "root";
$password = "";
$database = "halima_seafood_db";

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Database Connection failed: " . mysqli_connect_error());
}

// Global functions can also be added here in a professional procedural way
function sanitize($conn, $data) {
    return mysqli_real_escape_string($conn, trim($data));
}
?>
