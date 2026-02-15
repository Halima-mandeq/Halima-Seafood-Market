<?php
// User/shop.php
include '../Includes/db.php';
include '../Includes/price_helper.php';
session_start();
$current_page = 'shop';

// FILTER LOGIC
$category_filter = $_GET['category'] ?? '';
$sort_option = $_GET['sort'] ?? 'newest';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 9; // Products per page
$offset = ($page - 1) * $limit;

// Base query
$sql = "SELECT * FROM products WHERE 1=1";
$count_sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";

// Apply Category Filter
if (!empty($category_filter)) {
    $cat_safe = sanitize($conn, $category_filter);
    $sql .= " AND category = '$cat_safe'";
    $count_sql .= " AND category = '$cat_safe'";
}

// Apply Sorting
switch ($sort_option) {
    case 'price_low':
        $sql .= " ORDER BY price_per_kg ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY price_per_kg DESC";
        break;
    default: // newest
        $sql .= " ORDER BY created_at DESC";
        break;
}

// Apply Pagination
$sql .= " LIMIT $limit OFFSET $offset";

$result = mysqli_query($conn, $sql);
$count_result = mysqli_query($conn, $count_sql);
$total_products = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_products / $limit);

// Fetch Categories for Sidebar
$cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        /* Range slider styling could go here or use a library, keep simple for now */
    </style>
</head>
<body>

    <?php include '../Includes/user_header.php'; ?>

    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row gap-12">
        
        <!-- SIDEBAR -->
        <aside class="w-full md:w-64 flex-shrink-0 space-y-10">
            <!-- Categories -->
            <div>
                <h3 class="font-black text-gray-900 mb-6">Categories</h3>
                <ul class="space-y-3">
                    <li>
                        <a href="shop.php" class="<?php echo empty($category_filter) ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600 font-medium'; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all">
                            <i class="fa-solid fa-layer-group"></i> All Products
                        </a>
                    </li>
                    <?php while($cat = mysqli_fetch_assoc($cat_query)): ?>
                    <li>
                        <a href="shop.php?category=<?php echo urlencode($cat['category']); ?>" class="<?php echo $category_filter == $cat['category'] ? 'bg-blue-50 text-blue-600 font-bold' : 'text-gray-500 hover:text-blue-600 font-medium'; ?> flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all">
                            <i class="fa-solid fa-fish"></i> <?php echo $cat['category']; ?>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Price Range (Visual Only for now) -->
            <div>
                <h3 class="font-black text-gray-900 mb-6">Price Range (per KG)</h3>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="h-1 bg-gray-100 rounded-full relative mb-6">
                        <div class="absolute left-0 w-1/2 h-full bg-blue-600 rounded-full"></div>
                        <div class="absolute left-1/2 -translate-x-1/2 top-1/2 -translate-y-1/2 h-4 w-4 bg-blue-600 rounded-full border-2 border-white shadow-md"></div>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-gray-500">
                        <span>$10</span>
                        <span>$200</span>
                    </div>
                    <button class="w-full mt-6 bg-blue-600 text-white py-3 rounded-xl font-bold text-xs hover:bg-blue-700 transition-all">Apply Filters</button>
                </div>
            </div>

            <!-- Weekly Special Banner -->
            <div class="bg-blue-600 rounded-3xl p-8 text-white relative overflow-hidden shadow-xl shadow-blue-200">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                
                <h4 class="text-[10px] font-black uppercase tracking-widest mb-2 opacity-80">Weekly Special</h4>
                <h3 class="text-3xl font-black mb-4 leading-none">20% OFF</h3>
                <p class="text-xs font-medium text-blue-100 mb-6 leading-relaxed">On all Shellfish items this weekend only.</p>
                <a href="shop.php?category=Shellfish" class="inline-block bg-white text-blue-600 px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-50 transition-all">Shop Now</a>
            </div>
        </aside>

        <!-- MAIN PRODUCT GRID -->
        <main class="flex-1">
            <div class="mb-10">
                <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Fresh Seafood Market</h1>
                <p class="text-gray-500 font-medium">Premium quality sustainably sourced seafood delivered to your doorstep.</p>
            </div>

            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <p class="text-sm font-bold text-gray-400">Showing <span class="text-gray-900"><?php echo mysqli_num_rows($result); ?></span> of <span class="text-gray-900"><?php echo $total_products; ?></span> products</p>
                
                <form id="sortForm" class="flex items-center gap-3">
                    <label class="text-sm font-bold text-gray-400">Sort by:</label>
                    <select name="sort" onchange="document.getElementById('sortForm').submit()" class="bg-white border border-gray-100 text-sm font-bold text-gray-900 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer">
                        <option value="newest" <?php echo $sort_option == 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                        <option value="price_low" <?php echo $sort_option == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort_option == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                    <!-- Preserve category filter context -->
                    <?php if(!empty($category_filter)): ?><input type="hidden" name="category" value="<?php echo $category_filter; ?>"><?php endif; ?>
                </form>
            </div>

            <!-- Grid -->
            <?php if(mysqli_num_rows($result) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($prod = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white p-5 rounded-[2rem] shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border border-gray-100 group flex flex-col h-full">
                    <!-- Image -->
                    <div class="relative h-48 bg-gray-50 rounded-3xl mb-5 overflow-hidden flex items-center justify-center p-4">
                        <?php if($prod['status'] == 'Low Stock'): ?>
                            <span class="absolute top-3 left-3 bg-red-500 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm z-10">Low Stock</span>
                        <?php else: ?>
                            <span class="absolute top-3 left-3 bg-blue-600 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm z-10"><?php echo $prod['category']; ?></span>
                        <?php endif; ?>
                        
                        <a href="product_details.php?id=<?php echo $prod['id']; ?>" class="block w-full h-full">
                            <img src="../Images/products/<?php echo $prod['image_path']; ?>" 
                                 alt="<?php echo $prod['name']; ?>" 
                                 class="max-h-full max-w-full object-contain drop-shadow-md group-hover:scale-105 transition-transform duration-500 mx-auto"
                                 onerror="this.src='../Images/products/default_fish.png'">
                        </a>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <a href="product_details.php?id=<?php echo $prod['id']; ?>" class="hover:text-blue-600 transition-colors">
                                <h3 class="text-lg font-black text-gray-900 leading-tight"><?php echo $prod['name']; ?></h3>
                            </a>
                            <div class="flex items-center gap-1 text-xs font-bold text-yellow-400">
                                <i class="fa-solid fa-star"></i> <span>4.9</span>
                            </div>
                        </div>
                        
                        <?php
                            $user_id = $_SESSION['user_id'] ?? null;
                            $price_data = get_product_price($conn, $prod['id'], $user_id);
                            $display_price = $price_data['price'];
                        ?>
                        <div class="flex items-baseline gap-1 mb-3">
                            <?php if($price_data['is_special']): ?>
                                <span class="text-xl font-black text-red-500">$<?php echo number_format($display_price, 2); ?></span>
                                <span class="text-sm font-bold text-gray-400 line-through">$<?php echo number_format($prod['price_per_kg'], 2); ?></span>
                            <?php else: ?>
                                <span class="text-xl font-black text-blue-600">$<?php echo number_format($prod['price_per_kg'], 2); ?></span>
                                <span class="text-xs font-medium text-gray-400">/ kg</span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-xs text-gray-500 mb-6 line-clamp-2 leading-relaxed">
                            Premium quality sustainably sourced <?php echo strtolower($prod['name']); ?> delivered fresh.
                        </p>

                        <!-- Buttons -->
                        <div class="mt-auto flex gap-3">
                            <button class="flex-1 bg-blue-600 text-white py-3 rounded-xl font-bold text-xs hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                            </button>
                            <button onclick="sendQuickNegotiation('<?php echo $prod['id']; ?>', '<?php echo addslashes($prod['name']); ?>', '<?php echo $prod['price_per_kg']; ?>', '<?php echo $prod['image_path']; ?>')" class="h-10 w-10 border border-gray-200 rounded-xl flex items-center justify-center text-gray-400 hover:border-blue-600 hover:text-blue-600 transition-all" title="Message Us">
                                <i class="fa-solid fa-comments"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <div class="flex justify-center mt-12 gap-2">
                <!-- Prev -->
                <?php if($page > 1): ?>
                <a href="?page=<?php echo $page-1; ?>&sort=<?php echo $sort_option; ?>&category=<?php echo $category_filter; ?>" class="h-10 w-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>&sort=<?php echo $sort_option; ?>&category=<?php echo $category_filter; ?>" 
                   class="h-10 w-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all <?php echo $i == $page ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'bg-white border border-gray-100 text-gray-600 hover:bg-gray-50'; ?>">
                    <?php echo $i; ?>
                </a>
                <?php endfor; ?>

                <!-- Next -->
                <?php if($page < $total_pages): ?>
                <a href="?page=<?php echo $page+1; ?>&sort=<?php echo $sort_option; ?>&category=<?php echo $category_filter; ?>" class="h-10 w-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
                <div class="bg-white rounded-[2rem] p-12 text-center border border-gray-100">
                    <div class="h-20 w-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl text-gray-400">
                        <i class="fa-solid fa-basket-shopping"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-2">No Products Found</h3>
                    <p class="text-gray-400 text-sm max-w-xs mx-auto mb-8">We couldn't find any products matching your filters. Try clearing them to see more.</p>
                    <a href="shop.php" class="inline-block bg-blue-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-blue-700 transition-all">Clear Filters</a>
                </div>
            <?php endif; ?>

        </main>
    </div>

    <?php include '../Includes/user_footer.php'; ?>
    
    <script>
    async function sendQuickNegotiation(id, name, price, image) {
        // Construct Rich HTML Card
        const messageConfig = `
            <div class='negotiation-card flex gap-4 p-3 bg-white rounded-xl border border-gray-100 items-center shadow-sm mb-2 cursor-pointer hover:bg-blue-50 transition-colors' data-product-id='${id}' data-offer='${price}'>
                <img src='../Images/products/${image}' class='w-14 h-14 object-cover rounded-lg bg-gray-50 pointer-events-none'>
                <div class='pointer-events-none'>
                    <h4 class='font-bold text-gray-900 text-sm leading-tight'>${name}</h4>
                    <div class='font-black text-blue-600 text-sm mt-0.5'>$${price}/kg</div>
                    <div class='text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1'>Click to Edit Price</div>
                </div>
            </div>
            <div class='text-xs font-medium text-gray-600'>Hi, do you have this in stock?</div>
        `;
        
        const formData = new FormData();
        formData.append('action', 'send');
        formData.append('message', messageConfig);

        try {
            const res = await fetch('handlers/chat_handler.php', { method: 'POST', body: formData });
            const data = await res.json();
            if(data.success) {
                window.location.href = 'contact.php';
            } else {
                alert('Login required to chat.'); // Likely reason for failure if not logged in
                window.location.href = '../Auth/index.php?form=login';
            }
        } catch(e) {
            console.error(e);
            window.location.href = 'contact.php';
        }
    }
    </script>
</body>
</html>
