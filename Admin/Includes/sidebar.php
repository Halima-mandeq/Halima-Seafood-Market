<?php
// Admin/Includes/sidebar.php
if (!isset($current_page)) $current_page = '';
$admin_name = $_SESSION['full_name'] ?? "Admin Account";
$admin_role = $_SESSION['role'] ?? "Admin";
?>
<!-- SIDEBAR -->
<aside class="w-64 bg-white border-r border-gray-100 flex flex-col p-6 fixed h-full z-10">
    <!-- Logo -->
    <div class="flex items-center gap-3 mb-10 px-2">
        <img src="../Images/Logo.png" alt="Logo" class="h-8 w-8 object-contain">
        <div>
            <h2 class="font-extrabold text-[#0c4a6e] leading-tight">Halima Seafood</h2>
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Admin Dashboard</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-2">
        <a href="index.php" class="sidebar-link <?php echo $current_page == 'dashboard' ? 'active' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl'; ?> flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>
        <a href="users.php" class="sidebar-link <?php echo $current_page == 'users' ? 'active' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl'; ?> flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all">
            <i class="fa-solid fa-users"></i> User Management
        </a>
        <a href="products.php" class="sidebar-link <?php echo $current_page == 'products' ? 'active' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl'; ?> flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all">
            <i class="fa-solid fa-fish"></i> Products
        </a>
        <a href="orders.php" class="sidebar-link <?php echo $current_page == 'orders' ? 'active' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl'; ?> flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all">
            <i class="fa-solid fa-cart-shopping"></i> Orders
        </a>
        <a href="messages.php" class="sidebar-link <?php echo $current_page == 'messages' ? 'active' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl'; ?> flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all">
            <i class="fa-solid fa-envelope"></i> Messages
        </a>
        <a href="reports.php" class="sidebar-link <?php echo $current_page == 'reports' ? 'active' : 'text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-xl'; ?> flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all">
            <i class="fa-solid fa-chart-simple"></i> Reports
        </a>
    </nav>

    <!-- User Profile -->
    <div class="mt-auto bg-gray-50 p-4 rounded-2xl flex items-center gap-3 group relative">
        <div class="h-10 w-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 font-black relative overflow-hidden">
            <?php echo strtoupper(substr($admin_name, 0, 1) . substr(explode(' ', $admin_name)[1] ?? '', 0, 1)); ?>
            <span class="absolute -bottom-1 -right-1 h-3 w-3 bg-green-500 rounded-full border-2 border-white"></span>
        </div>
        <div class="overflow-hidden">
            <h4 class="text-xs font-bold text-gray-900 truncate"><?php echo $admin_name; ?></h4>
            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest"><?php echo $admin_role; ?> Account</p>
        </div>
        <a href="../Auth/handlers/logout_handler.php" class="ml-auto text-gray-300 hover:text-red-500 transition-all">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>
    </div>
</aside>
