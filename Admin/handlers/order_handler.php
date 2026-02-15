<?php
// Admin/handlers/order_handler.php
include '../../Includes/db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'search') {
    $query = sanitize($conn, $_GET['query']);
    
    // Search by Order ID, Customer Name, or Product Name
    $sql = "SELECT o.*, u.full_name as customer_name, p.name as product_name 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            JOIN products p ON o.product_id = p.id 
            WHERE o.id LIKE '%$query%' 
               OR u.full_name LIKE '%$query%' 
               OR p.name LIKE '%$query%'
            ORDER BY o.created_at DESC";
            
    $result = mysqli_query($conn, $sql);
    
    $orders = [];
    while($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    echo json_encode($orders);
    exit();
}
?>
