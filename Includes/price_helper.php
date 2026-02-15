<?php
// Includes/price_helper.php

function get_product_price($conn, $product_id, $user_id = null) {
    if ($user_id) {
        // Check for active special price
        $query = "SELECT special_price, expires_at FROM special_prices 
                  WHERE product_id = ? AND user_id = ? AND expires_at > NOW() 
                  ORDER BY created_at DESC LIMIT 1";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            return [
                'price' => $row['special_price'],
                'is_special' => true,
                'expires_at' => $row['expires_at']
            ];
        }
    }
    
    // Fallback to base price
    $query = "SELECT price_per_kg FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return [
        'price' => $row['price_per_kg'],
        'is_special' => false,
        'expires_at' => null
    ];
}
?>
