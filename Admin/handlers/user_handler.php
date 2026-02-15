<?php
// Admin/handlers/user_handler.php
include '../../Includes/db.php';
session_start();

header('Content-Type: application/json');

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['action']) && $_GET['action'] == 'search') {
    $query = sanitize($conn, $_GET['query']);
    $sql = "SELECT * FROM users WHERE full_name LIKE '%$query%' OR email LIKE '%$query%' OR username LIKE '%$query%' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $users = [];
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    echo json_encode($users);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $full_name = sanitize($conn, $_POST['full_name']);
        $email = sanitize($conn, $_POST['email']);
        $phone = isset($_POST['phone']) ? sanitize($conn, $_POST['phone']) : '';
        $username = sanitize($conn, $_POST['username']);
        $role = sanitize($conn, $_POST['role']);
        
        if (!in_array($role, ['admin', 'user'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
            exit();
        }

        $password = $_POST['password'];

        if (empty($full_name) || empty($email) || empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
            exit();
        }

        // Check if exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email' OR username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Email or Username already exists.']);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, phone_number, username, password, role) 
                VALUES ('$full_name', '$email', '$phone', '$username', '$hashed_password', '$role')";
        
        if (mysqli_query($conn, $sql)) {
            $new_id = mysqli_insert_id($conn);

            // AUTO-MESSAGE: Welcome the new user to the chat system
            // This ensures they appear in the message list immediately
            $welcome_msg = "Welcome to Halima Seafood Market! 🐟\nHow can we help you today?";
            $admin_sender_id = $_SESSION['user_id'];
            
            $msg_sql = "INSERT INTO messages (sender_id, receiver_id, message, is_read) 
                        VALUES ($admin_sender_id, $new_id, '$welcome_msg', 0)";
            mysqli_query($conn, $msg_sql);

            echo json_encode([
                'success' => true, 
                'message' => 'User created successfully.',
                'user' => [
                    'id' => $new_id,
                    'full_name' => $full_name,
                    'email' => $email,
                    'role' => $role,
                    'status' => 'active'
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
        }
    }
    else if ($action === 'edit') {
        $id = intval($_POST['id']);
        $full_name = sanitize($conn, $_POST['full_name']);
        $email = sanitize($conn, $_POST['email']);
        $username = sanitize($conn, $_POST['username']);
        $role = sanitize($conn, $_POST['role']);

        if (!in_array($role, ['admin', 'user'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
            exit();
        }

        $status = sanitize($conn, $_POST['status']);

        if (empty($full_name) || empty($email) || empty($username)) {
            echo json_encode(['success' => false, 'message' => 'Required fields are missing.']);
            exit();
        }

        // Check if exists for another user
        $check = mysqli_query($conn, "SELECT id FROM users WHERE (email = '$email' OR username = '$username') AND id != $id");
        if (mysqli_num_rows($check) > 0) {
            echo json_encode(['success' => false, 'message' => 'Email or Username already exists for another account.']);
            exit();
        }

        $sql = "UPDATE users SET full_name='$full_name', email='$email', username='$username', role='$role', status='$status' WHERE id=$id";
        
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true, 'message' => 'User updated successfully.', 'user' => [
                'id' => $id,
                'full_name' => $full_name,
                'email' => $email,
                'role' => $role,
                'status' => $status
            ]]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
        }
    }
    else if ($action === 'delete') {
        $id = intval($_POST['id']);
        
        // Don't allow deleting self
        if ($id === $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
            exit();
        }

        if (mysqli_query($conn, "DELETE FROM users WHERE id=$id")) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error deleting user.']);
        }
    }
}
?>
