<?php
include 'Includes/db.php';

$sql = "CREATE TABLE IF NOT EXISTS special_prices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    special_price DECIMAL(10, 2) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
)";

if (mysqli_query($conn, $sql)) {
    echo "Table special_prices created successfully.";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}
?>
