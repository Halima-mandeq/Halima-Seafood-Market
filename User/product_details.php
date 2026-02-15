<?php
// User/product_details.php
include '../Includes/db.php';
include '../Includes/price_helper.php';
session_start();
$current_page = 'shop';

if (!isset($_GET['id'])) {
    header("Location: shop.php");
    exit();
}

$product_id = $_GET['id'];
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: shop.php");
    exit();
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $qty = $_POST['quantity'] ?? 1;
    $user_id = $_SESSION['user_id'] ?? null;
    $price_data = get_product_price($conn, $product['id'], $user_id);
    
    $cart_item = [
        'id' => $product['id'],
        'name' => $product['name'],
        'price' => $price_data['price'],
        'image' => $product['image_path'],
        'quantity' => $qty
    ];
    
    // Check if item already exists
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $product['id']) {
            $item['quantity'] += $qty;
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }
    
    echo "<script>alert('Item added to cart!'); window.location.href='product_details.php?id=$product_id';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $product['name']; ?> | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Custom Accordion Style */
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="bg-gray-50">

    <?php include '../Includes/user_header.php'; ?>

    <div class="max-w-7xl mx-auto px-6 py-12">
        <div class="bg-white rounded-[2.5rem] p-10 shadow-sm border border-gray-100 grid grid-cols-1 lg:grid-cols-2 gap-16">
            
            <!-- LEFT: IMAGES -->
            <div class="space-y-6">
                <!-- Main Image -->
                <div class="h-[500px] w-full bg-gray-100 rounded-[2rem] overflow-hidden relative group">
                    <img src="../Images/products/<?php echo $product['image_path']; ?>" alt="<?php echo $product['name']; ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" onerror="this.src='../Images/products/default_fish.png'">
                    
                </div>

                <!-- Thumbnails (Static for now, using main image + placeholders) -->
                <div class="grid grid-cols-4 gap-4">
                    <div class="h-24 bg-gray-100 rounded-xl overflow-hidden cursor-pointer border-2 border-blue-600">
                        <img src="../Images/products/<?php echo $product['image_path']; ?>" class="w-full h-full object-cover">
                    </div>
                    <?php for($i=1; $i<=3; $i++): ?>
                    <div class="h-24 bg-gray-100 rounded-xl overflow-hidden cursor-pointer border border-transparent hover:border-gray-300 transition-all opacity-60 hover:opacity-100">
                        <img src="../Images/<?php echo ($i+2).'.jpg'; ?>" class="w-full h-full object-cover">
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- RIGHT: DETAILS -->
            <div class="flex flex-col h-full">
                <!-- Badges (Design Match) -->
                <div class="flex gap-3 mb-6">
                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fa-solid fa-leaf"></i> Wild Caught
                    </span>
                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-widest flex items-center gap-1.5">
                        <i class="fa-solid fa-water"></i> Sustainable
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 leading-tight mb-4">
                    <?php echo $product['name']; ?> <br>
                    <span class="text-gray-400 text-3xl font-bold">(Fresh, Never Frozen)</span>
                </h1>

                <?php
                $user_id = $_SESSION['user_id'] ?? null;
                $price_data = get_product_price($conn, $product['id'], $user_id);
                $display_price = $price_data['price'];
                ?>

                <div class="flex items-baseline gap-2 mb-8">
                    <?php if($price_data['is_special']): ?>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-red-500 uppercase tracking-widest bg-red-50 px-2 py-1 rounded w-fit mb-1">Special Offer For You!</span>
                            <div class="flex items-baseline gap-3">
                                <span id="totalPrice" data-price="<?php echo $display_price; ?>" class="text-4xl font-black text-red-500">$<?php echo number_format($display_price, 2); ?></span>
                                <span class="text-xl text-gray-400 font-bold line-through">$<?php echo number_format($product['price_per_kg'], 2); ?></span>
                                <span class="text-lg text-gray-400 font-medium">/ Per KG</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Expires: <?php echo date('M d, h:i A', strtotime($price_data['expires_at'])); ?></p>
                        </div>
                    <?php else: ?>
                        <span id="totalPrice" data-price="<?php echo $product['price_per_kg']; ?>" class="text-4xl font-black text-blue-600">$<?php echo number_format($product['price_per_kg'], 2); ?></span>
                        <span class="text-lg text-gray-400 font-medium">/ Per KG</span>
                    <?php endif; ?>
                </div>

                <p class="text-gray-500 leading-relaxed mb-10 text-lg">
                    <?php echo $product['description'] ? $product['description'] : "Sustainably sourced from the crystal clear, freezing waters. Our seafood is processed within hours of harvest to ensure peak freshness. High in Omega-3 fatty acids and protein, it features a rich, buttery flavor and firm texture that's perfect for grilling, pan-searing, or premium sushi preparations."; ?>
                </p>

                <!-- Quantity & Actions -->
                <form method="POST" class="mb-8">
                    <div class="flex flex-col sm:flex-row gap-6 mb-8">
                        <!-- Quantity -->
                        <div>
                            <label class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-2">Quantity (KG)</label>
                            <div class="flex items-center bg-gray-100 rounded-xl px-2 w-32 h-14">
                                <button type="button" onclick="adjustQty(-1)" class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-gray-900 font-bold text-lg">-</button>
                                <input type="number" name="quantity" id="qty" value="1" min="1" step="1" class="w-full bg-transparent text-center font-bold text-lg outline-none appearance-none">
                                <button type="button" onclick="adjustQty(1)" class="w-10 h-full flex items-center justify-center text-gray-500 hover:text-gray-900 font-bold text-lg">+</button>
                            </div>
                        </div>

                        <!-- Offer Price -->
                         <div>
                            <label class="block text-xs font-bold text-gray-900 uppercase tracking-widest mb-2">Your Offer ($/kg)</label>
                            <div class="flex items-center bg-gray-100 rounded-xl px-4 w-40 h-14 border border-transparent focus-within:border-blue-500 focus-within:bg-white transition-all">
                                <span class="text-gray-400 font-bold mr-1">$</span>
                                <input type="number" id="offerPrice" placeholder="<?php echo $product['price_per_kg']; ?>" step="0.01" class="w-full bg-transparent font-bold text-lg outline-none appearance-none text-gray-900">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" name="add_to_cart" class="flex-1 bg-blue-600 text-white h-14 rounded-xl font-bold text-lg shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] transition-all flex items-center justify-center gap-3">
                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                        </button>
                        
                        <button type="button" id="negotiateBtn" onclick="sendNegotiation()" class="flex-1 bg-white text-blue-600 border-2 border-blue-100 h-14 rounded-xl font-bold text-lg hover:border-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center gap-3">
                            <i class="fa-solid fa-hand-holding-dollar"></i> Negotiate
                        </button>
                    </div>
                </form>

                <p class="text-xs text-gray-400 font-medium italic mb-10">
                    Ordering for a restaurant or event? Negotiate bulk pricing directly with our sales team via chat.
                </p>

                <!-- Accordions -->
                <div class="space-y-4 border-t border-gray-100 pt-8 mt-auto">
                    <!-- Origin -->
                    <details class="group">
                        <summary class="flex justify-between items-center font-bold text-gray-900 cursor-pointer list-none">
                            <span>Origin & Traceability</span>
                            <span class="transition group-open:rotate-180"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                        </summary>
                        <div class="text-sm text-gray-500 mt-4 leading-relaxed group-open:animate-fadeIn">
                            Our <?php echo $product['name']; ?> is harvested from the pristine waters of the Indian Ocean. We track every batch from catch to crate. You can scan the QR code on your packaging to see the exact harvest date and location.
                        </div>
                    </details>
                    
                    <hr class="border-gray-100">
                    
                    <!-- Nutrition -->
                    <details class="group">
                        <summary class="flex justify-between items-center font-bold text-gray-900 cursor-pointer list-none">
                            <span>Nutritional Info (per 100g)</span>
                            <span class="transition group-open:rotate-180"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                        </summary>
                        <div class="text-sm text-gray-500 mt-4 leading-relaxed group-open:animate-fadeIn">
                             Calories: 208 <br> Protein: 20g <br> Fat: 13g <br> Omega-3: 1560mg
                        </div>
                    </details>
                    
                    <hr class="border-gray-100">
                    
                    <!-- Shipping -->
                    <details class="group">
                        <summary class="flex justify-between items-center font-bold text-gray-900 cursor-pointer list-none">
                            <span>Shipping & Handling</span>
                            <span class="transition group-open:rotate-180"><i class="fa-solid fa-chevron-down text-xs"></i></span>
                        </summary>
                        <div class="text-sm text-gray-500 mt-4 leading-relaxed group-open:animate-fadeIn">
                            Shipped in eco-friendly insulated packaging with gel packs to maintain 0-4°C temperature. Next-day delivery available for orders placed before 2 PM.
                        </div>
                    </details>
                </div>
                
                <hr class="border-gray-100 my-8">

                 <!-- Trust Badges -->
                 <div class="grid grid-cols-2 gap-6">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-shield-halved text-blue-600"></i>
                        <span class="text-xs font-bold text-gray-500 uppercase">Secure Payment</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-fish-fins text-blue-600"></i>
                        <span class="text-xs font-bold text-gray-500 uppercase">Freshness Guaranteed</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-truck-fast text-blue-600"></i>
                        <span class="text-xs font-bold text-gray-500 uppercase">Cold Chain Delivery</span>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include '../Includes/user_footer.php'; ?>

    <!-- Negotiation Modal -->
    <div id="negotiationModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-[2rem] w-full max-w-md p-8 shadow-2xl transform scale-95 transition-transform duration-300" id="modalContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-gray-900">Make an Offer</h3>
                <button onclick="closeModal()" class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="flex gap-4 items-center mb-6 bg-blue-50 p-4 rounded-2xl border border-blue-100">
                <img src="../Images/products/<?php echo $product['image_path']; ?>" class="w-16 h-16 object-cover rounded-xl bg-white" onerror="this.src='../Images/products/default_fish.png'">
                <div>
                    <h4 class="font-bold text-gray-900 leading-tight"><?php echo $product['name']; ?></h4>
                    <span class="text-xs text-blue-600 font-bold bg-blue-100 px-2 py-0.5 rounded-md mt-1 inline-block">Base: $<?php echo $product['price_per_kg']; ?>/kg</span>
                </div>
            </div>

            <form id="negotiationForm" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Quantity (KG)</label>
                        <input type="number" id="modalQty" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 outline-none focus:ring-2 focus:ring-blue-100" readonly>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Your Price ($)</label>
                        <input type="number" id="modalOffer" class="w-full bg-white border-2 border-blue-100 rounded-xl px-4 py-3 font-bold text-blue-600 outline-none focus:border-blue-500 transition-all" step="0.01" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Note (Optional)</label>
                    <textarea id="modalNote" rows="3" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 font-medium text-sm text-gray-900 outline-none focus:ring-2 focus:ring-blue-100 resize-none" placeholder="I'll buy regular if you accept this..."></textarea>
                </div>

                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                    <span id="btnText">Send Offer</span>
                    <i id="btnIcon" class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <?php include '../Includes/user_footer.php'; ?>

    <script>
        // Check Login Status from PHP for JS access
        const needsLogin = <?php echo isset($_SESSION['user_id']) ? 'false' : 'true'; ?>;
        const modal = document.getElementById('negotiationModal');
        const modalContent = document.getElementById('modalContent');

        function updatePrice() {
            const qtyInput = document.getElementById('qty');
            const priceSpan = document.getElementById('totalPrice');
            const basePrice = parseFloat(priceSpan.getAttribute('data-price'));
            const qty = parseFloat(qtyInput.value) || 1;
            
            const total = (basePrice * qty).toFixed(2);
            priceSpan.innerText = '$' + total;
        }

        function adjustQty(change) {
            const qtyInput = document.getElementById('qty');
            let newVal = parseFloat(qtyInput.value) + change;
            if (newVal >= 1) {
                qtyInput.value = newVal;
                updatePrice();
            }
        }
        
        // Open Modal Logic
        function sendNegotiation() {
            if (needsLogin) {
                window.location.href = '../Auth/index.php?form=login';
                return;
            }

            const qty = document.getElementById('qty').value;
            const offer = document.getElementById('offerPrice').value;
            const basePrice = "<?php echo $product['price_per_kg']; ?>";

            // Pre-fill Modal
            document.getElementById('modalQty').value = qty;
            document.getElementById('modalOffer').value = offer || basePrice;
            document.getElementById('modalNote').value = ''; // Clean slate

            // Show animation
            modal.classList.remove('hidden');
            // Small delay to allow display:block to apply before opacity transition
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
                modalContent.classList.add('scale-100');
            }, 10);
            
            document.getElementById('modalOffer').focus();
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100');
            modalContent.classList.add('scale-95');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Close on outside click
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });

        // Handle Direct Send
        document.getElementById('negotiationForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');
            const originalText = btnText.innerText;
            
            // Loading State
            btnText.innerText = "Sending...";
            btnIcon.className = "fa-solid fa-spinner fa-spin";
            
            const qty = document.getElementById('modalQty').value;
            const offer = parseFloat(document.getElementById('modalOffer').value).toFixed(2);
            const note = document.getElementById('modalNote').value.trim();
            const productName = "<?php echo addslashes($product['name']); ?>";
            const productImage = "<?php echo $product['image_path']; ?>";
            const totalOffer = (offer * qty).toFixed(2);

            // Construct Rich HTML Message
            let messageHTML = `
                <div class='negotiation-card p-4 bg-white rounded-2xl border border-blue-100 shadow-sm mb-2 cursor-pointer hover:bg-blue-50 transition-colors' data-product-id='<?php echo $product['id']; ?>' data-offer='${offer}'>
                    <div class='flex gap-4 items-center mb-3 pointer-events-none'>
                        <img src='../Images/products/${productImage}' class='w-16 h-16 object-cover rounded-xl bg-gray-50'>
                        <div>
                            <div class='text-[10px] font-bold text-blue-600 uppercase tracking-widest'>Negotiation Offer</div>
                            <h4 class='font-black text-gray-900 leading-tight'>${productName}</h4>
                        </div>
                    </div>
                    <div class='grid grid-cols-2 gap-2 text-sm pointer-events-none'>
                        <div class='bg-gray-50 p-2 rounded-lg'>
                            <span class='block text-[10px] text-gray-400 font-bold uppercase'>My Offer</span>
                            <span class='block font-black text-blue-600'>$${offer}/kg</span>
                        </div>
                        <div class='bg-gray-50 p-2 rounded-lg'>
                            <span class='block text-[10px] text-gray-400 font-bold uppercase'>Quantity</span>
                            <span class='block font-black text-gray-900'>${qty} KG</span>
                        </div>
                    </div>
                    <div class='mt-3 pt-3 border-t border-gray-100 flex justify-between items-end pointer-events-none'>
                        <span class='text-xs font-bold text-gray-400'>Total Offer</span>
                        <span class='font-black text-gray-900 text-lg'>$${totalOffer}</span>
                    </div>
                    <div class='mt-2 text-center'>
                       <span class='text-[10px] font-bold text-blue-400 uppercase tracking-widest'>Click to Accept/Edit</span>
                    </div>
                </div>`;

            if (note) {
                messageHTML += `<div class='text-sm font-medium text-gray-600 mt-2 p-2 bg-blue-50/50 rounded-lg border border-blue-50'>Note: "${note}"</div>`;
            }

            // Send via AJAX
            const formData = new FormData();
            formData.append('action', 'send');
            formData.append('message', messageHTML);

            try {
                const response = await fetch('handlers/chat_handler.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                if (data.success) {
                    // Redirect to chat
                    window.location.href = 'contact.php';
                } else {
                    alert('Error sending offer: ' + (data.error || 'Unknown error'));
                    btnText.innerText = originalText;
                    btnIcon.className = "fa-solid fa-paper-plane";
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Connection error. Please try again.');
                btnText.innerText = originalText;
                btnIcon.className = "fa-solid fa-paper-plane";
            }
        });

        // Listen for manual input
        document.getElementById('qty').addEventListener('input', updatePrice);
    </script>
</body>
</html>
