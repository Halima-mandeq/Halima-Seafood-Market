<?php
// User/process_checkout.php
include '../Includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit();
}

// Basic Authentication Check (Optional: Guest checkout implies user_id might be null or guest account?)
// For now, if not logged in, we might associate with a generic guest user or require login. 
// Given previous context, guests were prompted to login for chat. 
// Standard flow: checkout requires login. 
// If user is not logged in, user_id will be null. The `orders` table `user_id` is NOT NULL?
// Let's check `database.sql`. `user_id` is NOT NULL. 
// So strictly speaking, user must be logged in. 
// I will check user_id.

if (!isset($_SESSION['user_id'])) {
    // Ideally redirect to login with return url, but for simplicity:
    echo "<script>alert('Please log in to complete your order.'); window.location.href = '../Auth/index.php?form=login';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_method = $_POST['payment_method'];

// Process each item
foreach ($_SESSION['cart'] as $item) {
    $product_id = $item['id'];
    $weight = $item['quantity'];
    $price_total = $item['price'] * $weight;
    
    // Insert into orders
    // Schema: user_id, product_id, weight_kg, total_price, status, created_at
    $status = 'Pending'; // Default
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, weight_kg, total_price, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iidds", $user_id, $product_id, $weight, $price_total, $status);
    $stmt->execute();
}

// Clear Cart
unset($_SESSION['cart']);

// Redirect to Success
header("Location: order_success.php");
exit();
?>
