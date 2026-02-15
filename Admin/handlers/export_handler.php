<?php
// Admin/handlers/export_handler.php
include '../../Includes/db.php';
session_start();

// Security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

// Set filename with current date
$filename = "Halima_Seafood_Report_" . date('Y-m-d_H-i') . ".csv";

// Headers for browser download as Excel/CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// Create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// --- SECTION 1: ORDERS REPORT ---
fputcsv($output, ['--- SALES & ORDERS REPORT ---']);
fputcsv($output, ['Order ID', 'Date', 'Customer Name', 'Product', 'Weight (KG)', 'Total Price', 'Status']);

$orders_query = mysqli_query($conn, "SELECT o.id, o.created_at, u.full_name, p.name, o.weight_kg, o.total_price, o.status 
                                     FROM orders o 
                                     JOIN users u ON o.user_id = u.id 
                                     JOIN products p ON o.product_id = p.id 
                                     ORDER BY o.created_at DESC");

while ($row = mysqli_fetch_assoc($orders_query)) {
    fputcsv($output, $row);
}

// Add some spacing
fputcsv($output, []);
fputcsv($output, []);

// --- SECTION 2: INVENTORY REPORT ---
fputcsv($output, ['--- INVENTORY & STOCK REPORT ---']);
fputcsv($output, ['Product Name', 'SKU', 'Category', 'Price/KG', 'Stock Level (KG)', 'Status']);

$products_query = mysqli_query($conn, "SELECT name, sku, category, price_per_kg, stock_level_kg, status FROM products ORDER BY name ASC");

while ($row = mysqli_fetch_assoc($products_query)) {
    fputcsv($output, $row);
}

// --- SECTION 3: CUSTOMER SUMMARIES ---
fputcsv($output, []);
fputcsv($output, []);
fputcsv($output, ['--- TOP CUSTOMERS (LIFETIME SPEND) ---']);
fputcsv($output, ['Customer Name', 'Email', 'Total Orders', 'Total Spent']);

$cust_query = mysqli_query($conn, "SELECT u.full_name, u.email, COUNT(o.id) as order_count, SUM(o.total_price) as total_spent
                                   FROM orders o 
                                   JOIN users u ON o.user_id = u.id 
                                   GROUP BY u.id 
                                   ORDER BY total_spent DESC");

while ($row = mysqli_fetch_assoc($cust_query)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>
