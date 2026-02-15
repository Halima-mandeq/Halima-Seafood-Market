<?php
// User/cart.php
include '../Includes/db.php';
session_start();
$current_page = 'cart';

// Handlers
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $id = $_POST['product_id'];
        
        if ($_POST['action'] === 'remove') {
            foreach ($_SESSION['cart'] as $key => $item) {
                if ($item['id'] == $id) {
                    unset($_SESSION['cart'][$key]);
                    break;
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index
        } 
        elseif ($_POST['action'] === 'update_qty') {
            $new_qty = (float)$_POST['quantity'];
            if ($new_qty < 1) $new_qty = 1;
            
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $id) { // Fix: compare with item id, not key
                    $item['quantity'] = $new_qty;
                    break;
                }
            }
        }
    }
    // Post-Redirect-Get to prevent resubmission
    header("Location: cart.php");
    exit();
}

// Calculate Totals
$subtotal = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
}

$tax = $subtotal * 0.08;
$shipping = ($subtotal > 100 || $subtotal == 0) ? 0 : 5.00;
$total = $subtotal + $tax + $shipping;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="bg-[#f8fafc]">

    <?php include '../Includes/user_header.php'; ?>

    <div class="max-w-7xl mx-auto px-6 py-12">
        <h1 class="text-4xl font-black text-gray-900 mb-2">Your Cart</h1>
        <a href="shop.php" class="text-gray-500 hover:text-blue-600 font-medium text-sm flex items-center gap-2 mb-10 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Continue Shopping
        </a>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="bg-white rounded-[2rem] p-12 text-center border border-gray-100 shadow-sm">
                <div class="h-24 w-24 bg-blue-50 text-blue-200 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">
                    <i class="fa-solid fa-basket-shopping"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-4">Your cart is empty</h2>
                <p class="text-gray-500 mb-8">Looks like you haven't added any fresh catches yet.</p>
                <a href="shop.php" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="flex flex-col lg:flex-row gap-12">
                
                <!-- LEFT: CART ITEMS -->
                <div class="flex-1">
                    <!-- Headings -->
                    <div class="grid grid-cols-12 gap-4 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6 px-4">
                        <div class="col-span-6">Product Details</div>
                        <div class="col-span-2 text-center hidden md:block">Price/KG</div>
                        <div class="col-span-2 text-center hidden md:block">Weight (KG)</div>
                        <div class="col-span-2 text-right hidden md:block">Subtotal</div>
                    </div>

                    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-50">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="p-6 grid grid-cols-1 md:grid-cols-12 gap-6 items-center group hover:bg-gray-50/50 transition-colors">
                                <!-- Product Info -->
                                <div class="col-span-6 flex gap-6">
                                    <div class="h-24 w-24 bg-gray-100 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-100">
                                        <img src="../Images/products/<?php echo $item['image']; ?>" class="w-full h-full object-cover" onerror="this.src='../Images/products/default_fish.png'">
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-gray-900 leading-tight mb-1"><?php echo $item['name']; ?></h3>
                                        <p class="text-xs text-gray-500 font-medium mb-3">Freshly caught, premium grade</p>
                                        <form method="POST" class="inline-block">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <button type="submit" class="text-[10px] font-black text-red-500 uppercase tracking-widest flex items-center gap-1.5 hover:text-red-700 transition-colors">
                                                <i class="fa-solid fa-trash-can"></i> Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="col-span-2 text-center font-bold text-gray-900 hidden md:block">
                                    $<?php echo number_format($item['price'], 2); ?>
                                </div>

                                <!-- Quantity -->
                                <div class="col-span-2 flex justify-center">
                                    <form method="POST" class="flex items-center bg-gray-100 rounded-lg h-10 px-1 w-24">
                                        <input type="hidden" name="action" value="update_qty">
                                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                        
                                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-blue-600 font-bold transition-colors">-</button>
                                        <div class="flex-1 text-center font-bold text-sm text-gray-900"><?php echo $item['quantity']; ?></div>
                                        <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="w-8 h-full flex items-center justify-center text-gray-500 hover:text-blue-600 font-bold transition-colors">+</button>
                                    </form>
                                </div>

                                <!-- Subtotal -->
                                <div class="col-span-2 text-right font-black text-gray-900 text-lg hidden md:block">
                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Shipping Note -->
                    <div class="mt-6 bg-blue-50 border border-blue-100 rounded-2xl p-6 flex items-start gap-4">
                        <div class="h-10 w-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center flex-shrink-0 text-lg">
                            <i class="fa-solid fa-truck-fast"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-gray-900 text-sm mb-1">Free Shipping for orders over $100</h4>
                            <p class="text-xs text-gray-500 font-medium">Add <span class="text-blue-600 font-bold">$<?php echo max(0, 100 - $subtotal); ?></span> more to your cart to qualify for free express delivery.</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: ORDER SUMMARY -->
                <div class="w-full lg:w-96 flex-shrink-0">
                    <div class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-gray-100 border border-gray-100 sticky top-24">
                        <h2 class="text-xl font-black text-gray-900 mb-8">Order Summary</h2>

                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between items-center text-sm font-medium text-gray-500">
                                <span>Subtotal (<?php echo count($_SESSION['cart']); ?> items)</span>
                                <span class="text-gray-900 font-bold">$<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium text-gray-500">
                                <span>Estimated Tax (8%)</span>
                                <span class="text-gray-900 font-bold">$<?php echo number_format($tax, 2); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium text-gray-500">
                                <span>Estimated Shipping</span>
                                <span class="text-gray-900 font-bold">$<?php echo number_format($shipping, 2); ?></span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100 mb-8">
                            <div class="flex justify-between items-end">
                                <span class="text-lg font-black text-gray-900">Total</span>
                                <div class="text-right">
                                    <span class="block text-3xl font-black text-blue-600 leading-none">$<?php echo number_format($total, 2); ?></span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1 block">Includes all taxes</span>
                                </div>
                            </div>
                        </div>

                        <a href="checkout.php" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-bold text-sm hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center justify-center gap-3 mb-6">
                            Proceed to Checkout <i class="fa-solid fa-arrow-right"></i>
                        </a>

                        <div class="flex items-center justify-center gap-2 text-gray-300 text-2xl">
                            <i class="fa-brands fa-cc-visa hover:text-gray-400 transition-colors"></i>
                            <i class="fa-brands fa-cc-mastercard hover:text-gray-400 transition-colors"></i>
                            <i class="fa-brands fa-cc-amex hover:text-gray-400 transition-colors"></i>
                            <i class="fa-brands fa-apple-pay hover:text-gray-400 transition-colors"></i>
                        </div>
                        <p class="text-[10px] text-gray-400 font-bold text-center mt-4 uppercase tracking-widest">Secure SSL Encrypted Checkout</p>

                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>

    <?php include '../Includes/user_footer.php'; ?>

</body>
</html>
