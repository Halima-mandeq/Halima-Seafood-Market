<?php
// Admin/handlers/message_handler.php
include '../../Includes/db.php';
session_start();

header('Content-Type: application/json');

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$admin_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'search_users') {
    $query = sanitize($conn, $_GET['query']);
    
    // Complex query to get users AND their last message details (same as messages.php initial load)
    $sql = "SELECT DISTINCT u.id, u.full_name, u.role,
            (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id IN (SELECT id FROM users WHERE role='admin')) OR (sender_id IN (SELECT id FROM users WHERE role='admin') AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_msg,
            (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id IN (SELECT id FROM users WHERE role='admin')) OR (sender_id IN (SELECT id FROM users WHERE role='admin') AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_time,
            (SELECT is_read FROM messages WHERE sender_id = u.id AND receiver_id IN (SELECT id FROM users WHERE role='admin') ORDER BY created_at DESC LIMIT 1) as is_read
            FROM users u 
            WHERE u.role = 'user' AND u.full_name LIKE '%$query%'
            HAVING last_msg IS NOT NULL
            ORDER BY last_time DESC";

    $result = mysqli_query($conn, $sql);
    $users = [];
    while($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    echo json_encode($users);
    exit();
}

if ($action === 'fetch') {
    $receiver_id = intval($_GET['receiver_id']);
    
    // Mark messages as read
    mysqli_query($conn, "UPDATE messages SET is_read = 1 WHERE sender_id = $receiver_id AND receiver_id = $admin_id");

    $sql = "SELECT * FROM messages 
            WHERE (sender_id IN (SELECT id FROM users WHERE role='admin') AND receiver_id = $receiver_id) 
            OR (sender_id = $receiver_id AND receiver_id IN (SELECT id FROM users WHERE role='admin')) 
            ORDER BY created_at ASC";
    
    $result = mysqli_query($conn, $sql);
    $messages = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = $row;
    }

    echo json_encode(['success' => true, 'messages' => $messages]);
}

else if ($action === 'send') {
    $receiver_id = intval($_POST['receiver_id']);
    $message = sanitize($conn, $_POST['message']);

    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty.']);
        exit();
    }

    $sql = "INSERT INTO messages (sender_id, receiver_id, message, is_read) 
            VALUES ($admin_id, $receiver_id, '$message', 0)";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}
?>
