<?php
// Admin/index.php
include '../Includes/db.php';
session_start();

// Basic security check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/index.php");
    exit();
}

$current_page = 'dashboard';
$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['full_name'] ?? "Admin User";

// Fetch Counts
$user_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users"))[0] ?? 0;
$product_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0] ?? 0;
$order_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0] ?? 0;
$unread_msg_count = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM messages WHERE is_read = 0 AND receiver_id = $admin_id"))[0] ?? 0;

// Fetch Recent Messages with Customer Details
$sql_msgs = "SELECT m.*, u.full_name as customer_name 
             FROM messages m 
             JOIN users u ON m.sender_id = u.id 
             WHERE m.receiver_id = $admin_id 
             ORDER BY m.created_at DESC LIMIT 4";
$recent_messages = mysqli_query($conn, $sql_msgs);

// Fetch Top Products (using stock level as a proxy for 'active' products)
$top_products = mysqli_query($conn, "SELECT * FROM products ORDER BY stock_level_kg DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Halima Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-link.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-radius: 12px;
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <?php include 'Includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 ml-64 p-8">
        
        <!-- Header -->
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Dashboard</h1>
            <div class="flex items-center gap-6">
                <!-- Search and Notifications removed -->
            </div>
        </header>

        <!-- Welcome Section -->
        <section class="mb-10">
            <h2 class="text-4xl font-black text-gray-900 mb-1 tracking-tight">Welcome back, <?php echo explode(' ', $admin_name)[0]; ?> 👋</h2>
            <p class="text-gray-400 text-sm font-bold uppercase tracking-widest mt-2">Halima Seafood Market Management Hub</p>
        </section>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-6 mb-10">
            <!-- Total Users -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-blue-50/50 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl transition-all group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="text-xs font-black text-green-500 bg-green-50 px-2 py-1 rounded-lg">+12%</span>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Users</h3>
                <p class="text-4xl font-black text-gray-900 tracking-tighter"><?php echo number_format($user_count); ?></p>
            </div>

            <!-- Fish Products -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-blue-50/50 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl transition-all group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fa-solid fa-fish"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Fish Products</h3>
                <p class="text-4xl font-black text-gray-900 tracking-tighter"><?php echo number_format($product_count); ?></p>
            </div>

            <!-- Total Orders -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-blue-50/50 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl transition-all group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fa-solid fa-file-invoice"></i>
                    </div>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Total Orders</h3>
                <p class="text-4xl font-black text-gray-900 tracking-tighter"><?php echo number_format($order_count); ?></p>
            </div>

            <!-- Unread Messages -->
            <div class="bg-white p-6 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-xl hover:shadow-blue-50/50 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <div class="h-14 w-14 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-2xl transition-all group-hover:bg-orange-600 group-hover:text-white">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <?php if($unread_msg_count > 0): ?>
                        <span class="bg-red-50 text-red-500 text-[10px] font-black px-2 py-1 rounded-lg">Action Needed</span>
                    <?php endif; ?>
                </div>
                <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-widest mb-1">Unread Alerts</h3>
                <p class="text-4xl font-black text-gray-900 tracking-tighter"><?php echo number_format($unread_msg_count); ?></p>
            </div>
        </div>

        <!-- Lower Section Grid -->
        <div class="grid grid-cols-12 gap-8">
            <!-- Recent Messages -->
            <div class="col-span-8 bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 flex flex-col">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Recent Inquiries</h3>
                    <a href="messages.php" class="text-blue-600 font-black text-[10px] uppercase tracking-widest border-b-2 border-blue-100 hover:border-blue-600 transition-all">View All Conversations</a>
                </div>
                <div class="flex-1 space-y-6">
                    <?php while($msg = mysqli_fetch_assoc($recent_messages)): ?>
                    <div class="flex items-center justify-between group p-2 hover:bg-gray-50 rounded-2xl transition-all">
                        <div class="flex items-center gap-4 text-left">
                            <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center text-sm font-black text-gray-400">
                                <?php echo strtoupper(substr($msg['customer_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900"><?php echo $msg['customer_name']; ?></h4>
                                <p class="text-xs text-gray-400 line-clamp-1 italic"><?php echo $msg['message']; ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-gray-300 uppercase"><?php echo date('h:i A', strtotime($msg['created_at'])); ?></p>
                            <?php if(!$msg['is_read']): ?>
                                <span class="inline-block mt-1 h-2 w-2 bg-blue-600 rounded-full"></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-span-4 bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Top Stock</h3>
                    <span class="text-gray-400 font-black text-[10px] uppercase tracking-widest">Inventory</span>
                </div>
                <div class="space-y-8 mb-10">
                    <?php while($prod = mysqli_fetch_assoc($top_products)): ?>
                    <div class="flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="h-14 w-14 bg-gray-100 rounded-2xl overflow-hidden relative shadow-sm">
                                <img src="../Images/products/<?php echo $prod['image_path']; ?>" alt="<?php echo $prod['name']; ?>" class="h-full w-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900"><?php echo $prod['name']; ?></h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">
                                    <?php echo $prod['category']; ?> • <span class="text-blue-600"><?php echo number_format($prod['stock_level_kg'], 1); ?> KG</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>

                <a href="products.php" class="block w-full text-center bg-gray-900 text-white py-5 rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl shadow-gray-100">
                    Manage Full Inventory
                </a>
            </div>
        </div>

    </main>

</body>
</html>
