<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = mysqli_connect("localhost", "root", "", "halima_seafood_db");

if (!$conn) {
    die("Connect Error: " . mysqli_connect_error());
}

echo "<h3>All Users (ID | Name | Email | Role)</h3>";
$res = mysqli_query($conn, "SELECT id, full_name, email, role FROM users");
if ($res) {
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>{$row['full_name']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td>'{$row['role']}'</td>"; // Quotes to see hidden spaces
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
