<?php
// User/handlers/chat_handler.php
include '../../Includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
session_write_close(); // Release session file lock to prevent blocking other requests

// Get Admin ID dynamically
$admin_query = mysqli_query($conn, "SELECT id FROM users WHERE role = 'admin' LIMIT 1");
$admin_row = mysqli_fetch_assoc($admin_query);
$admin_id = $admin_row['id'] ?? 1; // Fallback to 1 if not found
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'fetch') {
    $sql = "SELECT * FROM messages 
            WHERE (sender_id = $user_id AND receiver_id IN (SELECT id FROM users WHERE role = 'admin')) 
               OR (receiver_id = $user_id AND sender_id IN (SELECT id FROM users WHERE role = 'admin')) 
            ORDER BY created_at ASC";
    
    $result = mysqli_query($conn, $sql);
    $messages = [];
    
    while ($row = mysqli_fetch_assoc($result)) {
        $messages[] = [
            'id' => $row['id'],
            'sender_id' => $row['sender_id'],
            'message' => $row['message'],
            'created_at' => date('h:i A', strtotime($row['created_at'])),
            'is_me' => ($row['sender_id'] == $user_id)
        ];
    }
    
    echo json_encode($messages);
} 
elseif ($action === 'send') {
    $message = sanitize($conn, $_POST['message']); // Ensure sanitize function exists in db.php or use mysqli_real_escape_string
    
    if (!empty($message)) {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message) VALUES ($user_id, $admin_id, '$message')";
        if (mysqli_query($conn, $sql)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Empty message']);
    }
}
?>
