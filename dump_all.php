<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "halima_seafood_db");
if (!$conn) die("Connect failed: " . mysqli_connect_error());

echo "Connected.<br>";

// Dump Messages
echo "<h3>Messages</h3>";
$res = mysqli_query($conn, "SELECT * FROM messages");
if (!$res) {
    echo "Query Failed: " . mysqli_error($conn);
} else {
    echo "Rows found: " . mysqli_num_rows($res) . "<br>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<pre>";
        print_r($row);
        echo "</pre><hr>";
    }
}
?>
