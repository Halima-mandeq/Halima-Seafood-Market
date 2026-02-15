<?php
// Includes/seed_reports.php
include 'db.php';

// Users: 3, 4, 5, 6 are customers
$customers = [3, 4, 5, 6];
// Products: 1, 2, 3, 4
$products = [
    1 => 24.99, // Salmon
    2 => 35.00, // Tuna
    3 => 18.50, // Prawns
    4 => 22.00  // Mackerel
];

echo "Seed started...<br>";

// Generate 20 random orders over the last 10 days
for ($i = 0; $i < 30; $i++) {
    $user_id = $customers[array_rand($customers)];
    $prod_id = array_rand($products);
    $price = $products[$prod_id];
    
    $weight = rand(2, 15) + (rand(0, 9) / 10);
    $total = $weight * $price;
    
    $status_options = ['Pending', 'Processing', 'Delivered', 'Delivered', 'Delivered', 'Cancelled'];
    $status = $status_options[array_rand($status_options)];
    
    // Random date in last 10 days
    $days_ago = rand(0, 10);
    $date = date('Y-m-d H:i:s', strtotime("-$days_ago days" . " " . rand(8, 20) . ":" . rand(0, 59)));
    
    $sql = "INSERT INTO orders (user_id, product_id, weight_kg, total_price, status, created_at) 
            VALUES ($user_id, $prod_id, $weight, $total, '$status', '$date')";
    
    if (mysqli_query($conn, $sql)) {
        echo "Order added for product $prod_id on $date<br>";
    } else {
        echo "Error: " . mysqli_error($conn) . "<br>";
    }
}

echo "Seeding completed.";
?>
