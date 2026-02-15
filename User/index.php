<?php
// User/index.php
include '../Includes/db.php';
session_start();
$current_page = 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halima Seafood Market | Fresh Ocean Catch</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-gradient {
            background: linear-gradient(to right, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.1) 100%);
        }
    </style>
</head>
<body class="bg-white">

    <?php include '../Includes/user_header.php'; ?>

    <!-- HERO SECTION -->
    <section class="relative h-[600px] flex items-center overflow-hidden">
        <!-- Background Image -->
        <div class="absolute inset-0 z-0">
            <img src="../Images/1.jpg" alt="Fresh Seafood" class="w-full h-full object-cover">
            <div class="absolute inset-0 hero-gradient"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">
            <span class="bg-blue-600 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest mb-6 inline-block">Daily Fresh Catch</span>
            <h1 class="text-5xl md:text-7xl font-black text-white leading-[1.1] mb-6 max-w-2xl text-shadow-lg">
                Premium Fresh Catch <br>
                Delivered to Your Door
            </h1>
            <p class="text-gray-200 text-lg md:text-xl font-medium mb-10 max-w-xl leading-relaxed">
                Experience the ocean's finest, sourced sustainably from local fisheries and delivered fresh to your doorstep within 24 hours.
            </p>
            <div class="flex gap-4">
                <a href="shop.php" class="bg-blue-600 text-white px-8 py-4 rounded-full font-bold text-sm hover:bg-blue-700 transition-all shadow-xl shadow-blue-900/20">
                    Shop Fresh Market
                </a>
                <a href="#specials" class="bg-white/10 backdrop-blur-md border border-white/20 text-white px-8 py-4 rounded-full font-bold text-sm hover:bg-white hover:text-gray-900 transition-all">
                    View Today's Specials
                </a>
            </div>
        </div>
    </section>

    <!-- FEATURES BAR -->
    <section class="bg-white py-10 border-b border-gray-50">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="flex items-center gap-4 group">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-certificate"></i>
                </div>
                <div>
                    <h4 class="font-black text-gray-900 text-sm">Certified Quality</h4>
                    <p class="text-xs text-gray-500 font-medium">Premium Standards</p>
                </div>
            </div>
            <div class="flex items-center gap-4 group">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-truck-fast"></i>
                </div>
                <div>
                    <h4 class="font-black text-gray-900 text-sm">24h Delivery</h4>
                    <p class="text-xs text-gray-500 font-medium">Always Fresh</p>
                </div>
            </div>
            <div class="flex items-center gap-4 group">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-leaf"></i>
                </div>
                <div>
                    <h4 class="font-black text-gray-900 text-sm">Sustainable</h4>
                    <p class="text-xs text-gray-500 font-medium">Eco-friendly Fishing</p>
                </div>
            </div>
            <div class="flex items-center gap-4 group">
                <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <div>
                    <h4 class="font-black text-gray-900 text-sm">Expert Support</h4>
                    <p class="text-xs text-gray-500 font-medium">Dedicated Service</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURED PRODUCTS -->
    <section class="py-20 bg-[#fefbf6]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Featured Catch</h2>
                    <p class="text-gray-500">Explore our most popular seasonal selections sourced just for you.</p>
                </div>
                <a href="shop.php" class="text-blue-600 font-bold text-sm hover:text-blue-700 flex items-center gap-2">
                    View All Products <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php
                // Fetch 3 random active products
                // If products table is empty, we will use placeholders
                $products_sql = "SELECT * FROM products WHERE status != 'Out of Stock' ORDER BY RAND() LIMIT 3";
                $products_result = mysqli_query($conn, $products_sql);
                
                if (mysqli_num_rows($products_result) > 0):
                    while($prod = mysqli_fetch_assoc($products_result)):
                ?>
                <!-- Product Card -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-100 group">
                    <div class="relative h-64 bg-gray-50 rounded-3xl mb-6 overflow-hidden flex items-center justify-center p-6">
                        <span class="absolute top-4 left-4 bg-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">
                            <?php echo $prod['category']; ?>
                        </span>
                        <?php if($prod['status'] == 'Low Stock'): ?>
                            <span class="absolute top-4 right-4 bg-orange-500 text-white px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm">Low Stock</span>
                        <?php endif; ?>
                        
                        <a href="product_details.php?id=<?php echo $prod['id']; ?>" class="block w-full h-full">
                            <img src="../Images/products/<?php echo $prod['image_path']; ?>" 
                                 alt="<?php echo $prod['name']; ?>" 
                                 class="max-h-full max-w-full object-contain drop-shadow-xl group-hover:scale-110 transition-transform duration-500 mx-auto"
                                 onerror="this.src='../Images/products/default_fish.png'">
                        </a>
                    </div>
                    
                    <a href="product_details.php?id=<?php echo $prod['id']; ?>" class="hover:text-blue-600 transition-colors">
                        <h3 class="text-xl font-black text-gray-900 mb-1"><?php echo $prod['name']; ?></h3>
                    </a>
                    <div class="flex items-baseline gap-1 mb-4">
                        <span class="text-2xl font-black text-blue-600">$<?php echo number_format($prod['price_per_kg'], 2); ?></span>
                        <span class="text-sm font-medium text-gray-400">/ kg</span>
                    </div>
                    
                    <p class="text-sm text-gray-500 mb-6 line-clamp-2">
                        Premium quality <?php echo strtolower($prod['name']); ?>, freshly sourced and perfect for your next meal.
                    </p>

                    <button class="w-full bg-[#0f172a] text-white py-4 rounded-xl font-bold text-sm hover:bg-blue-600 transition-all shadow-lg shadow-gray-200 flex items-center justify-center gap-2 group-hover:shadow-blue-200">
                        <i class="fa-solid fa-basket-shopping"></i> Add to Cart
                    </button>
                </div>
                <?php endwhile; else: ?>
                    <!-- Fallback if no products -->
                    <div class="col-span-3 text-center py-20 text-gray-400 font-medium italic">
                        No products available at the moment. Check back soon!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- HERITAGE SECTION -->
    <section class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-20 items-center">
                <div class="relative">
                    <div class="absolute -top-10 -left-10 h-64 w-64 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
                    <div class="relative z-10 rounded-[2.5rem] overflow-hidden shadow-2xl skew-y-3 hover:skew-y-0 transition-transform duration-700">
                        <img src="../Images/2.jpg" alt="Our Heritage" class="w-full h-full object-cover ml-10 brightness-90 hover:scale-105 transition-transform duration-700" onerror="this.src='../Images/6.jpg'">
                    </div>
                </div>
                <div>
                    <span class="text-blue-600 font-black text-xs uppercase tracking-[0.2em] mb-4 block">Our Heritage</span>
                    <h2 class="text-4xl md:text-5xl font-black text-gray-900 mb-8 leading-tight">
                        From the Ocean <br>
                        to Your Table
                    </h2>
                    <p class="text-gray-500 leading-relaxed mb-6">
                        At Halima Seafood Market, our commitment to quality is unparalleled. Since our founding, we have maintained direct relationships with ethical local fishers who share our passion for the sea.
                    </p>
                    <p class="text-gray-500 leading-relaxed mb-10">
                        Every selection is hand-inspected by our experts to ensure only the highest grade seafood reaches our customers. We believe in transparency, sustainability, and the unmatched taste of truly fresh catch.
                    </p>
                    
                    <div class="grid grid-cols-2 gap-8 mb-10">
                        <div>
                            <h3 class="text-4xl font-black text-blue-600 mb-1">100%</h3>
                            <p class="text-xs font-bold text-gray-900 uppercase">Sustainable Sourcing</p>
                        </div>
                        <div>
                            <h3 class="text-4xl font-black text-blue-600 mb-1">24h</h3>
                            <p class="text-xs font-bold text-gray-900 uppercase">Dock to Door</p>
                        </div>
                    </div>

                    <a href="about.php" class="inline-block border-2 border-gray-100 px-8 py-3.5 rounded-full font-bold text-sm text-gray-600 hover:border-blue-600 hover:text-blue-600 transition-all">
                        Learn More About Us
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER -->
    <section class="py-20 px-6">
        <div class="max-w-7xl mx-auto bg-blue-600 rounded-[3rem] p-12 md:p-20 text-center relative overflow-hidden shadow-2xl shadow-blue-200">
            <!-- Decorative Circles -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-5 rounded-full translate-x-1/3 translate-y-1/3"></div>

            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Join the Catch of the Day</h2>
                <p class="text-blue-100 text-lg mb-10 font-medium">Subscribe to our newsletter to receive weekly fresh catch alerts, exclusive recipes, and market discounts.</p>
                
                <form class="flex flex-col md:flex-row gap-4">
                    <input type="email" placeholder="Enter your email address" class="flex-1 bg-white border-none py-4 px-8 rounded-full text-sm font-medium focus:ring-4 focus:ring-blue-400 outline-none shadow-lg">
                    <button type="button" class="bg-[#0f172a] text-white px-10 py-4 rounded-full font-bold text-sm hover:bg-gray-900 transition-all shadow-lg hover:shadow-xl">
                        Subscribe Now
                    </button>
                </form>
            </div>
        </div>
    </section>

    <?php include '../Includes/user_footer.php'; ?>

</body>
</html>
