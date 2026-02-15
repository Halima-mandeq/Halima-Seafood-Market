<?php
require_once 'Includes/db.php';

$username = 'buzzer';
$password = 'password123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$email = 'buzzer@halimaseafood.com';
$role = 'admin';

echo "Attempting to recover/create admin user '$username'...\n";

// Check if user exists
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing
    echo "User '$username' found. Updating password...\n";
    $update_stmt = $conn->prepare("UPDATE users SET password = ?, role = 'admin' WHERE username = ?");
    $update_stmt->bind_param("ss", $hashed_password, $username);
    if ($update_stmt->execute()) {
        echo "SUCCESS: Password updated for user '$username'.\n";
        echo "New Password: $password\n";
    } else {
        echo "ERROR: Could not update password: " . $conn->error . "\n";
    }
} else {
    // Create new
    echo "User '$username' not found. Creating new admin user...\n";
    $insert_stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password, role) VALUES (?, ?, ?, ?, ?)");
    $fullName = "Admin Buzzer";
    $insert_stmt->bind_param("sssss", $fullName, $email, $username, $hashed_password, $role);
    if ($insert_stmt->execute()) {
        echo "SUCCESS: User '$username' created.\n";
        echo "Password: $password\n";
    } else {
        echo "ERROR: Could not create user: " . $conn->error . "\n";
    }
}

echo "Done.\n";
?>
