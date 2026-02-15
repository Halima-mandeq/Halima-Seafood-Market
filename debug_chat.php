<?php
// Enable full error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging DB Connection
if (!file_exists('Includes/db.php')) {
    die("Error: Includes/db.php not found!");
}
include 'Includes/db.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
} else {
    echo "<h3>Database Connected Successfully</h3>";
}

// Users Debug
echo "<h1>Users Table</h1>";
$users = mysqli_query($conn, "SELECT * FROM users");
if (!$users) {
    echo "Query Error: " . mysqli_error($conn);
} else {
    $count = mysqli_num_rows($users);
    echo "<p>Total Users Found: $count</p>";
    
    if ($count > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>";
        while($u = mysqli_fetch_assoc($users)) {
            echo "<tr>";
            echo "<td>" . $u['id'] . "</td>";
            echo "<td>" . $u['full_name'] . "</td>";
            echo "<td>" . $u['email'] . "</td>";
            echo "<td>" . $u['role'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

// Messages Debug
echo "<h1>Last 20 Messages</h1>";
$msgs = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC LIMIT 20");
if (!$msgs) {
    echo "Query Error: " . mysqli_error($conn);
} else {
    $count = mysqli_num_rows($msgs);
    echo "<p>Total Messages Found: $count</p>";

    if ($count > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Sender</th><th>Receiver</th><th>Message</th><th>Time</th></tr>";
        while($m = mysqli_fetch_assoc($msgs)) {
            echo "<tr>";
            echo "<td>" . $m['id'] . "</td>";
            echo "<td>" . $m['sender_id'] . "</td>";
            echo "<td>" . $m['receiver_id'] . "</td>";
            echo "<td>" . $m['message'] . "</td>";
            echo "<td>" . $m['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}
?>
