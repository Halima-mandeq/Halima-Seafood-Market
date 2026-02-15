<?php
// Admin/reports.php
include '../Includes/db.php';
session_start();

// Basic security check (admin only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/index.php");
    exit();
}

// Ensure error reporting is visible if something goes wrong
error_reporting(E_ALL);
ini_set('display_errors', 1);

$current_page = 'reports';
$admin_name = $_SESSION['full_name'] ?? "Admin Account";

// 1. DATA FOR SUMMARY CARDS
$total_revenue = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_price) FROM orders WHERE status = 'Delivered'"))[0] ?? 0;
$this_week_revenue = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_price) FROM orders WHERE status = 'Delivered' AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))[0] ?? 0;
$this_month_revenue = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_price) FROM orders WHERE status = 'Delivered' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))[0] ?? 0;
$total_orders = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0] ?? 0;

// 2. LINE CHART DATA: Revenue Trends (Last 7 Days)
$sales_trend_labels = [];
$sales_trend_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $disp_date = date('D', strtotime($date));
    $rev = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_price) FROM orders WHERE DATE(created_at) = '$date' AND status != 'Cancelled'"))[0] ?? 0;
    
    $sales_trend_labels[] = $disp_date;
    $sales_trend_data[] = floatval($rev);
}

// 2.5 BAR CHART DATA: Hourly Sales Peaks
$hourly_labels = ["6AM", "8AM", "10AM", "12PM", "2PM", "4PM", "6PM", "8PM", "10PM"];
$hourly_data = [];
foreach ([6, 8, 10, 12, 14, 16, 18, 20, 22] as $hour) {
    $h_rev = mysqli_fetch_row(mysqli_query($conn, "SELECT SUM(total_price) FROM orders WHERE HOUR(created_at) IN ($hour, ".($hour+1).") AND status != 'Cancelled'"))[0] ?? 0;
    $hourly_data[] = floatval($h_rev);
}

// 3. DOUGHNUT CHART DATA: Category Distribution
$cat_labels = [];
$cat_data = [];
$cat_sql = "SELECT p.category, COUNT(o.id) as order_count 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            GROUP BY p.category, p.name"; // Fixed grouping for accuracy
$cat_res = mysqli_query($conn, $cat_sql);
while($row = mysqli_fetch_assoc($cat_res)) {
    if (!in_array($row['category'], $cat_labels)) {
        $cat_labels[] = $row['category'];
        $cat_data[array_search($row['category'], $cat_labels)] = intval($row['order_count']);
    } else {
        $cat_data[array_search($row['category'], $cat_labels)] += intval($row['order_count']);
    }
}
// Clean up category data for chart
$cat_data = array_values($cat_data);

// 4. TOP PRODUCTS RANKING
$top_prod_sql = "SELECT p.name, p.category, SUM(o.weight_kg) as total_kg, SUM(o.total_price) as total_sales, p.image_path
                 FROM orders o 
                 JOIN products p ON o.product_id = p.id 
                 GROUP BY p.id, p.name, p.category, p.image_path 
                 ORDER BY total_sales DESC LIMIT 5";
$top_prod_query = mysqli_query($conn, $top_prod_sql);

// 5. TOP CUSTOMERS RANKING
$top_cust_sql = "SELECT u.full_name, COUNT(o.id) as order_count, SUM(o.total_price) as total_spent, u.email
                 FROM orders o 
                 JOIN users u ON o.user_id = u.id 
                 GROUP BY u.id, u.full_name, u.email 
                 ORDER BY total_spent DESC LIMIT 5";
$top_cust_query = mysqli_query($conn, $top_cust_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics | Halima Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .sidebar-link.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-radius: 12px;
        }
        .custom-shadow { box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <?php include 'Includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 ml-64 p-8">
        
        <!-- Header -->
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Sales Analytics</h1>
                <p class="text-sm text-gray-400 font-bold uppercase tracking-widest mt-1">Full Inventory & Revenue Performance</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="handlers/export_handler.php" class="bg-gray-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs uppercase tracking-widest flex items-center gap-3 hover:bg-blue-600 transition-all shadow-xl shadow-gray-100">
                    <i class="fa-solid fa-file-excel"></i> Download Excel Report
                </a>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-8 rounded-[3rem] border border-gray-100 custom-shadow group hover:border-blue-100 transition-all">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Today's Total</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-4xl font-black text-gray-900 tracking-tighter">$<?php echo number_format($total_revenue, 2); ?></h3>
                    <span class="text-green-500 font-bold text-xs mb-1">Live Bank</span>
                </div>
            </div>
            <div class="bg-white p-8 rounded-[3rem] border border-gray-100 custom-shadow group hover:border-blue-100 transition-all">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Weekly Money</p>
                <h3 class="text-4xl font-black text-blue-600 tracking-tighter">$<?php echo number_format($this_week_revenue, 2); ?></h3>
            </div>
            <div class="bg-white p-8 rounded-[3rem] border border-gray-100 custom-shadow group hover:border-blue-100 transition-all">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">This Month</p>
                <h3 class="text-4xl font-black text-orange-500 tracking-tighter">$<?php echo number_format($this_month_revenue, 2); ?></h3>
            </div>
            <div class="bg-white p-8 rounded-[3rem] border border-gray-100 custom-shadow group hover:border-blue-100 transition-all">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Total Orders</p>
                <h3 class="text-4xl font-black text-gray-900 tracking-tighter"><?php echo number_format($total_orders); ?></h3>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-3 gap-8 mb-12">
            <!-- Revenue Trend Chart -->
            <div class="col-span-2 bg-white p-10 rounded-[3rem] border border-gray-100 custom-shadow relative">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-xl font-black text-gray-900">Weekly Revenue Trend</h3>
                    <div class="flex gap-2">
                        <span class="h-2 w-2 bg-blue-600 rounded-full"></span>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Revenue (USD)</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Hourly Traffic -->
            <div class="bg-white p-10 rounded-[3rem] border border-gray-100 custom-shadow flex flex-col">
                <h3 class="text-xl font-black text-gray-900 mb-2">Hourly Peaks</h3>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-8">When the money comes in</p>
                <div class="flex-1">
                    <canvas id="hourlyChart"></canvas>
                </div>
            </div>

            <!-- Category Distribution -->
            <div class="col-span-1 bg-white p-10 rounded-[3rem] border border-gray-100 custom-shadow flex flex-col">
                <h3 class="text-xl font-black text-gray-900 mb-8">Sales by Category</h3>
                <div class="flex-1 flex items-center justify-center">
                    <div class="w-full">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Additional Space for new metrics or layout balance -->
            <div class="col-span-2 bg-white p-10 rounded-[3rem] border border-gray-100 custom-shadow">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-gray-900">Strategic Performance Tip</h3>
                </div>
                <div class="bg-blue-50 p-6 rounded-2xl flex items-center gap-6">
                    <div class="h-16 w-16 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600 text-2xl">
                        <i class="fa-solid fa-lightbulb"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-blue-900 text-sm mb-1">Peak Sales Hour Detected</h4>
                        <p class="text-blue-700/70 text-xs leading-relaxed font-medium">Your customers are most active between 10 AM and 2 PM. Consider launching "Ocean Flash Deals" during these hours to maximize weekly revenue.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Top Products & Best Customers -->
        <div class="grid grid-cols-2 gap-8 mb-12">
            <!-- Most Productive Fish -->
            <div class="bg-white p-10 rounded-[3.5rem] border border-gray-100 custom-shadow">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-gray-900">Most Profitable Seafood</h3>
                    <span class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">Top Selling</span>
                </div>
                <div class="space-y-6">
                    <?php while($prod = mysqli_fetch_assoc($top_prod_query)): ?>
                    <div class="flex items-center justify-between group p-3 hover:bg-gray-50 rounded-2xl transition-all">
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 bg-gray-100 rounded-2xl overflow-hidden relative shadow-sm">
                                <img src="../Images/products/<?php echo $prod['image_path'] ?: 'default_fish.png'; ?>" class="h-full w-full object-cover">
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900"><?php echo $prod['name']; ?></h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase"><?php echo $prod['category']; ?> • <?php echo number_format($prod['total_kg'], 1); ?> KG Sold</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-gray-900">$<?php echo number_format($prod['total_sales'], 2); ?></p>
                            <p class="text-[10px] font-black text-green-500 uppercase">Sales Volume</p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <!-- Best Customers -->
            <div class="bg-white p-10 rounded-[3.5rem] border border-gray-100 custom-shadow">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-xl font-black text-gray-900">Elite Customer Ranking</h3>
                    <span class="bg-orange-50 text-orange-600 px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest">VIP Clients</span>
                </div>
                <div class="space-y-6">
                    <?php while($cust = mysqli_fetch_assoc($top_cust_query)): ?>
                    <div class="flex items-center justify-between group p-3 hover:bg-gray-50 rounded-2xl transition-all">
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-black text-lg">
                                <?php echo strtoupper(substr($cust['full_name'], 0, 1)); ?>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900"><?php echo $cust['full_name']; ?></h4>
                                <p class="text-[10px] font-bold text-gray-400 uppercase"><?php echo $cust['email']; ?> • <?php echo $cust['order_count']; ?> Orders</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-gray-900">$<?php echo number_format($cust['total_spent'], 2); ?></p>
                            <p class="text-[10px] font-black text-blue-500 uppercase">Total Lifetime</p>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

    </main>

    <script>
        // 1. REVENUE LINE CHART
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($sales_trend_labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($sales_trend_data); ?>,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.05)',
                    borderWidth: 5,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { display: false },
                        ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold', size: 10 }, color: '#94a3b8' }
                    }
                }
            }
        });

        // 2. CATEGORY DOUGHNUT CHART
        const catCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($cat_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($cat_data); ?>,
                    backgroundColor: ['#2563eb', '#f97316', '#10b981', '#6366f1', '#f43f5e'],
                    borderWidth: 0,
                    spacing: 10
                }]
            },
            options: {
                responsive: true,
                cutout: '80%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: { weight: 'bold', size: 10 },
                            padding: 20
                        }
                    }
                }
            }
        });

        // 3. HOURLY SALES BAR CHART
        const hourCtx = document.getElementById('hourlyChart').getContext('2d');
        new Chart(hourCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($hourly_labels); ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?php echo json_encode($hourly_data); ?>,
                    backgroundColor: '#f97316',
                    borderRadius: 8,
                    barThickness: 12
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        display: false 
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { weight: 'bold', size: 8 }, color: '#94a3b8' }
                    }
                }
            }
        });
    </script>

</body>
</html>
