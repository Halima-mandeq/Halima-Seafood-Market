<?php
// Admin/handlers/price_handler.php
session_start();
include '../../Includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $product_id = intval($_POST['product_id']);
    $price = floatval($_POST['price']);
    
    if ($user_id <= 0 || $product_id <= 0 || $price <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid input']);
        exit();
    }

    // Set expiration to 24 hours from now
    $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));

    // Check if entry exists for this user/product
    $check_sql = "SELECT id FROM special_prices WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $check_sql);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        // Update existing
        $sql = "UPDATE special_prices SET special_price = ?, expires_at = ?, created_at = NOW() WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "dsii", $price, $expires_at, $user_id, $product_id);
    } else {
        // Insert new
        $sql = "INSERT INTO special_prices (user_id, product_id, special_price, expires_at) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iids", $user_id, $product_id, $price, $expires_at);
    }

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
}
?>
