<?php
// Admin/orders.php
include '../Includes/db.php';
session_start();

// Basic security check (admin only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/index.php");
    exit();
}

$current_page = 'orders';
$admin_name = $_SESSION['full_name'] ?? "Admin Account";
$filter = $_GET['status'] ?? 'All';

// Build Query based on filter
$where_clause = "";
if ($filter !== 'All') {
    $where_clause = " WHERE o.status = '" . mysqli_real_escape_string($conn, $filter) . "'";
}

$sql = "SELECT o.*, u.full_name as customer_name, p.name as product_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        JOIN products p ON o.product_id = p.id 
        $where_clause 
        ORDER BY o.created_at DESC";

$orders_query = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Halima Admin</title>
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
        .filter-btn.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-color: #dbeafe;
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <?php include 'Includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 ml-64 p-8">
        
        <!-- Header -->
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Orders Management</h1>
                <p class="text-sm text-gray-500 font-medium mt-1">Review and update customer seafood orders</p>
            </div>
            <div class="flex items-center gap-6">
                <a href="handlers/export_handler.php" class="bg-white border border-gray-100 px-6 py-3 rounded-xl text-sm font-bold text-gray-600 flex items-center gap-3 hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fa-solid fa-download"></i> Export Data
                </a>
            </div>
        </header>

        <!-- Filters & Search -->
        <div class="flex justify-between items-center mb-10">
            <div class="relative w-96">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="orderSearchInput" placeholder="Search by Order ID, customer, or product..." class="w-full bg-white border border-gray-100 py-3 pl-12 pr-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none shadow-sm">
            </div>
            </div>
            <div class="flex bg-white rounded-xl p-1 border border-gray-100 shadow-sm">
                <?php 
                    $statuses = ['All', 'Pending', 'Processing', 'Delivered', 'Cancelled'];
                    foreach($statuses as $s): 
                        $active = ($filter === $s) ? 'active' : '';
                ?>
                <a href="?status=<?php echo $s; ?>" class="px-5 py-2 text-sm font-bold rounded-lg transition-all filter-btn <?php echo $active; ?> text-gray-500 hover:text-blue-600">
                    <?php echo $s === 'All' ? 'All Orders' : $s; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-widest border-b border-gray-50">
                        <th class="px-8 py-6">Order ID</th>
                        <th class="px-8 py-6">Date</th>
                        <th class="px-8 py-6">Customer</th>
                        <th class="px-8 py-6">Product (Fish)</th>
                        <th class="px-8 py-6">Weight (KG)</th>
                        <th class="px-8 py-6">Total Price</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="orderTableBody">
                    <?php while($order = mysqli_fetch_assoc($orders_query)): ?>
                    <tr class="hover:bg-gray-50/50 transition-all group">
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-blue-600">#ORD-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></span>
                        </td>
                        <td class="px-8 py-5 text-sm font-medium text-gray-500">
                            <?php echo date('M d, Y', strtotime($order['created_at'])); ?>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-bold text-gray-900"><?php echo $order['customer_name']; ?></span>
                        </td>
                        <td class="px-8 py-5 text-sm font-medium text-gray-500"><?php echo $order['product_name']; ?></td>
                        <td class="px-8 py-5 text-sm font-medium text-gray-500"><?php echo number_format($order['weight_kg'], 1); ?> kg</td>
                        <td class="px-8 py-5 text-sm font-black text-gray-900">$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td class="px-8 py-5 text-sm">
                            <?php 
                                $status_classes = [
                                    'Pending' => 'text-gray-500',
                                    'Processing' => 'text-blue-500',
                                    'Delivered' => 'text-green-500',
                                    'Cancelled' => 'text-red-400'
                                ];
                                $color = $status_classes[$order['status']] ?? 'text-gray-500';
                            ?>
                            <span class="font-bold <?php echo $color; ?>"><?php echo $order['status']; ?></span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <button class="h-8 w-8 text-gray-400 hover:text-blue-600 rounded-lg transition-all flex items-center justify-center">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination Placeholder -->
            <div class="px-8 py-6 border-t border-gray-50 flex justify-start items-center gap-2">
                <button class="h-9 w-9 border border-gray-100 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 transition-all cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left text-xs"></i>
                </button>
                <button class="h-9 w-9 bg-blue-600 text-white rounded-lg flex items-center justify-center text-sm font-bold">1</button>
                <button class="h-9 w-9 border border-gray-100 rounded-lg flex items-center justify-center text-sm font-bold text-gray-500 hover:bg-gray-50">2</button>
                <button class="h-9 w-9 border border-gray-100 rounded-lg flex items-center justify-center text-sm font-bold text-gray-500 hover:bg-gray-50">3</button>
                <button class="h-9 w-9 border border-gray-100 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 transition-all">
                    <i class="fa-solid fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

    </main>

    <script>
        // LIVE SEARCH
        document.getElementById('orderSearchInput').addEventListener('input', function() {
            const query = this.value;
            const tbody = document.getElementById('orderTableBody');

            fetch(`handlers/order_handler.php?action=search&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="8" class="px-8 py-5 text-center text-gray-400">No orders found.</td></tr>';
                        return;
                    }

                    data.forEach(order => {
                        const statusColors = {
                            'Pending': 'text-gray-500',
                            'Processing': 'text-blue-500',
                            'Delivered': 'text-green-500',
                            'Cancelled': 'text-red-400'
                        };
                        const color = statusColors[order.status] || 'text-gray-500';

                        // Format Date
                        const date = new Date(order.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: '2-digit' });

                        const tr = `
                        <tr class="hover:bg-gray-50/50 transition-all group">
                            <td class="px-8 py-5">
                                <span class="text-sm font-black text-blue-600">#ORD-${String(order.id).padStart(4, '0')}</span>
                            </td>
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">${date}</td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-gray-900">${order.customer_name}</span>
                            </td>
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">${order.product_name}</td>
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">${parseFloat(order.weight_kg).toFixed(1)} kg</td>
                            <td class="px-8 py-5 text-sm font-black text-gray-900">$${parseFloat(order.total_price).toFixed(2)}</td>
                            <td class="px-8 py-5 text-sm">
                                <span class="font-bold ${color}">${order.status}</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <button class="h-8 w-8 text-gray-400 hover:text-blue-600 rounded-lg transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                            </td>
                        </tr>
                        `;
                        tbody.innerHTML += tr;
                    });
                })
                .catch(err => console.error(err));
        });
    </script></html>
