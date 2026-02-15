<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>Debug Schema</h3>";

$conn = mysqli_connect("localhost", "root", "", "halima_seafood_db");

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
} else {
    echo "Connected.<br>";
}

function dumpTable($conn, $table) {
    echo "<h4>Table: $table</h4>";
    $res = mysqli_query($conn, "DESCRIBE $table");
    if($res) {
        echo "<table border='1'><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while($row = mysqli_fetch_assoc($res)) {
            echo "<tr>";
            foreach($row as $cell) echo "<td>$cell</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error describing $table: " . mysqli_error($conn);
    }
}

dumpTable($conn, 'users');
dumpTable($conn, 'messages');

echo "<h4>Table Contents (Raw)</h4>";
$raw_users = mysqli_query($conn, "SELECT * FROM users LIMIT 5");
if ($raw_users) {
    echo "Users (Top 5):<br>";
    while ($r = mysqli_fetch_assoc($raw_users)) {
        print_r($r);
        echo "<br><hr>";
    }
} else {
    echo "Select Users Error: " . mysqli_error($conn);
}
?>
