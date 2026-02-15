<?php
// Includes/user_header.php
$current_page = $current_page ?? 'home';
?>
<header class="bg-white border-b border-gray-50 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
        <!-- Logo -->
        <a href="../User/index.php" class="flex items-center gap-3">
            <img src="../Images/Logo.png" alt="Halima Seafood" class="h-10 w-auto">
            <span class="text-xl font-black text-gray-900 tracking-tighter">Halima Seafood</span>
        </a>

        <!-- Desktop Nav -->
        <nav class="hidden md:flex items-center gap-8">
            <a href="index.php" class="<?php echo $current_page == 'home' ? 'text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-blue-600'; ?> text-sm transition-colors">Home</a>
            <a href="shop.php" class="<?php echo $current_page == 'shop' ? 'text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-blue-600'; ?> text-sm transition-colors">Shop</a>
            <a href="about.php" class="<?php echo $current_page == 'about' ? 'text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-blue-600'; ?> text-sm transition-colors">About</a>
            <a href="contact.php" class="<?php echo $current_page == 'contact' ? 'text-blue-600 font-bold' : 'text-gray-500 font-medium hover:text-blue-600'; ?> text-sm transition-colors">Contact</a>
        </nav>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <div class="relative hidden sm:block">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search fresh catch..." class="bg-gray-50 border border-gray-100 py-2.5 pl-10 pr-4 rounded-full text-xs focus:ring-2 focus:ring-blue-100 transition-all outline-none w-64">
            </div>

            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="flex items-center gap-4">
                    <a href="cart.php" class="relative h-10 w-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-all">
                        <i class="fa-solid fa-bag-shopping"></i>
                        <span class="absolute -top-1 -right-1 h-4 w-4 bg-blue-600 text-white text-[10px] font-bold flex items-center justify-center rounded-full border-2 border-white">0</span>
                    </a>
                    <div class="h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                        <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                    </div>
                </div>
            <?php else: ?>
                <a href="../Auth/index.php?form=login" class="text-sm font-bold text-gray-500 hover:text-blue-600 transition-colors px-4 py-2">Login</a>
                <a href="../Auth/index.php?form=register" class="bg-blue-600 text-white px-6 py-2.5 rounded-full text-sm font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
                    Register <i class="fa-solid fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>
