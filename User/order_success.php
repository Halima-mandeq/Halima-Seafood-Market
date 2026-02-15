<?php
// User/order_success.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed | Halima Seafood</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-[#f8fafc] flex items-center justify-center h-screen">

    <div class="bg-white p-12 rounded-[2.5rem] shadow-xl text-center max-w-md w-full border border-gray-100">
        <div class="h-24 w-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-check text-4xl text-green-600"></i>
        </div>
        <h1 class="text-3xl font-black text-gray-900 mb-2">Order Confirmed!</h1>
        <p class="text-gray-500 mb-8 font-medium">Your fresh seafood is being prepared. We will send you an update when it ships.</p>
        
        <div class="space-y-3">
            <a href="shop.php" class="block w-full bg-blue-600 text-white py-4 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                Continue Shopping
            </a>
            <a href="index.php" class="block w-full bg-white text-gray-600 border border-gray-200 py-4 rounded-2xl font-bold hover:bg-gray-50 transition-all">
                Return Home
            </a>
        </div>
    </div>

</body>
</html>
