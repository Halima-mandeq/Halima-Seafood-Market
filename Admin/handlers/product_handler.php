<?php
// Admin/handlers/product_handler.php
include '../../Includes/db.php';
session_start();

header('Content-Type: application/json');

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

if ($action === 'search') {
    $query = sanitize($conn, $_GET['query']);
    $sql = "SELECT * FROM products WHERE name LIKE '%$query%' OR sku LIKE '%$query%' OR category LIKE '%$query%' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    
    $products = [];
    while($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
    echo json_encode($products);
    exit();
}

if ($action === 'create') {
    $name = sanitize($conn, $_POST['name']);
    $sku = sanitize($conn, $_POST['sku']);
    $category = sanitize($conn, $_POST['category']);
    $price = floatval($_POST['price_per_kg']);
    $stock = floatval($_POST['stock_level_kg']);
    $status = sanitize($conn, $_POST['status']);
    
    // Handle Image Upload
    $image_path = 'default_fish.png';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . uniqid() . '.' . $ext;
        $target = '../../Images/products/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image_path = $filename;
        }
    }

    $sql = "INSERT INTO products (sku, name, category, price_per_kg, stock_level_kg, status, image_path) 
            VALUES ('$sku', '$name', '$category', $price, $stock, '$status', '$image_path')";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Product added successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
}

else if ($action === 'update') {
    $id = intval($_POST['id']);
    $name = sanitize($conn, $_POST['name']);
    $sku = sanitize($conn, $_POST['sku']);
    $category = sanitize($conn, $_POST['category']);
    $price = floatval($_POST['price_per_kg']);
    $stock = floatval($_POST['stock_level_kg']);
    $status = sanitize($conn, $_POST['status']);

    $sql = "UPDATE products SET sku='$sku', name='$name', category='$category', 
            price_per_kg=$price, stock_level_kg=$stock, status='$status' WHERE id=$id";
    
    if (mysqli_query($conn, $sql)) {
        echo json_encode(['success' => true, 'message' => 'Product updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
}

else if ($action === 'delete') {
    $id = intval($_POST['id']);
    if (mysqli_query($conn, "DELETE FROM products WHERE id=$id")) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting product.']);
    }
}
?>
