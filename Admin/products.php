<?php
// Admin/products.php
include '../Includes/db.php';
session_start();

// Basic security check (admin only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/index.php");
    exit();
}

$current_page = 'products';
$admin_name = $_SESSION['full_name'] ?? "Admin Account";

// Fetch Stats
$total_products = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0] ?? 0;
$in_stock = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE status = 'In Stock'"))[0] ?? 0;
$low_stock = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE status = 'Low Stock'"))[0] ?? 0;
$out_of_stock = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products WHERE status = 'Out of Stock'"))[0] ?? 0;

// Fetch Products for Table
$products_query = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Halima Admin</title>
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
        .form-input {
            width: 100%;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 0.875rem 1rem;
            font-size: 0.875rem;
            transition: all 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #3b82f6;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.05);
        }
    </style>
</head>
<body class="bg-[#f8fafc] flex min-h-screen">

    <?php include 'Includes/sidebar.php'; ?>

    <!-- MAIN CONTENT -->
    <main class="flex-1 ml-64 p-8">
        
        <!-- Header -->
        <header class="flex justify-between items-center mb-10">
            <div class="relative w-96">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="productSearchInput" placeholder="Search products..." class="w-full bg-[#f1f5f9] border-none py-3 pl-12 pr-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
            </div>
            <div class="flex items-center gap-6">
                <!-- Icons removed -->
            </div>
        </header>

        <!-- Page Title & Actions -->
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Products</h1>
                <p class="text-gray-500 font-medium">Manage your seafood inventory, pricing, and stock status.</p>
            </div>
            <button onclick="toggleAddModal(true)" class="bg-[#2563eb] text-white px-8 py-4 rounded-xl font-bold text-sm flex items-center gap-3 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                <i class="fa-solid fa-plus"></i> Add New Product
            </button>
        </div>

        <!-- Stat Cards -->
        <div class="grid grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md group">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Total Products</p>
                <h3 class="text-3xl font-black text-gray-900 leading-none"><?php echo number_format($total_products); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md group">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">In Stock</p>
                <h3 class="text-3xl font-black text-green-500 leading-none"><?php echo number_format($in_stock); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md group">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Low Stock</p>
                <h3 class="text-3xl font-black text-orange-500 leading-none"><?php echo number_format($low_stock); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm transition-all hover:shadow-md group">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Out of Stock</p>
                <h3 class="text-3xl font-black text-red-500 leading-none"><?php echo number_format($out_of_stock); ?></h3>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-widest border-b border-gray-50">
                        <th class="px-8 py-6">Image</th>
                        <th class="px-8 py-6">Product Name</th>
                        <th class="px-8 py-6">Category</th>
                        <th class="px-8 py-6">Price per KG</th>
                        <th class="px-8 py-6">Stock Level</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="productTableBody">
                    <?php while($product = mysqli_fetch_assoc($products_query)): ?>
                    <tr class="hover:bg-gray-50/50 transition-all group" data-id="<?php echo $product['id']; ?>">
                        <td class="px-8 py-5">
                            <div class="h-14 w-14 bg-gray-50 rounded-2xl flex items-center justify-center overflow-hidden border border-gray-100">
                                <img src="../Images/products/<?php echo $product['image_path']; ?>" 
                                     alt="Product" 
                                     class="h-full w-full object-cover transition-transform group-hover:scale-110"
                                     onerror="this.src='../Images/products/default_fish.png'">
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 leading-none mb-1"><?php echo $product['name']; ?></h4>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">SKU: <?php echo $product['sku']; ?></p>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-sm font-medium text-gray-500"><?php echo $product['category']; ?></td>
                        <td class="px-8 py-5 text-sm font-black text-gray-900">$<?php echo number_format($product['price_per_kg'], 2); ?>/KG</td>
                        <td class="px-8 py-5 text-sm font-medium text-gray-500"><?php echo number_format($product['stock_level_kg'], 0); ?> KG</td>
                        <td class="px-8 py-5">
                            <?php 
                                $status_classes = [
                                    'In Stock' => 'bg-green-50 text-green-600 border-green-100',
                                    'Low Stock' => 'bg-orange-50 text-orange-600 border-orange-100',
                                    'Out of Stock' => 'bg-red-50 text-red-600 border-red-100'
                                ];
                                $class = $status_classes[$product['status']] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                            ?>
                            <span class="<?php echo $class; ?> px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-wider">
                                <?php echo $product['status']; ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($product)); ?>)" class="h-9 w-9 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-pencil text-sm"></i>
                                </button>
                                <button onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" class="h-9 w-9 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>

    <!-- ADD PRODUCT MODAL -->
    <div id="addProductModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 p-6">
        <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-10 transform scale-95 transition-all duration-300 overflow-hidden relative">
            <button onclick="toggleAddModal(false)" class="absolute top-8 right-10 h-10 w-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="mb-10">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">New Product</h2>
                <p class="text-gray-500 font-medium">Add a new seafood item to your inventory.</p>
            </div>

            <div id="addAlert" class="hidden p-4 rounded-2xl mb-8 text-sm font-bold border italic"></div>

            <form id="addProductForm" class="grid grid-cols-2 gap-8">
                <div class="col-span-2 flex gap-8 items-start bg-gray-50 p-6 rounded-3xl border border-dashed border-gray-200">
                    <div class="h-32 w-32 bg-white rounded-2xl border border-gray-100 flex items-center justify-center overflow-hidden shrink-0 relative group">
                        <img id="imagePreview" src="../Images/products/default_fish.png" class="h-full w-full object-cover">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center pointer-events-none">
                            <i class="fa-solid fa-camera text-white text-xl"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Product Image</h4>
                        <p class="text-xs text-gray-500 mb-4 leading-relaxed">Recommended size: 800x800px. PNG or JPG accepted.</p>
                        <input type="file" id="imageInput" name="image" class="hidden" accept="image/*">
                        <button type="button" onclick="document.getElementById('imageInput').click()" class="bg-white border border-gray-200 text-gray-600 px-5 py-2.5 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all shadow-sm">
                            Upload Photo
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Product Name</label>
                    <input type="text" name="name" class="form-input" placeholder="e.g. Atlantic Salmon" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">SKU Number</label>
                    <input type="text" name="sku" class="form-input" placeholder="SALM-001" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Category</label>
                    <select name="category" class="form-input">
                        <option value="Fresh">Fresh Seafood</option>
                        <option value="Frozen">Frozen Products</option>
                        <option value="Shellfish">Shellfish</option>
                        <option value="Canned">Canned Seafood</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Price Per KG ($)</label>
                    <input type="number" step="0.01" name="price_per_kg" class="form-input" placeholder="24.99" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Initial Stock (KG)</label>
                    <input type="number" step="1" name="stock_level_kg" class="form-input" placeholder="100" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Overall Status</label>
                    <select name="status" class="form-input">
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>

                <div class="col-span-2 flex justify-end gap-4 mt-6">
                    <button type="button" onclick="toggleAddModal(false)" class="px-8 py-3.5 rounded-2xl font-bold text-gray-400 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" id="addBtn" class="bg-[#2563eb] text-white px-10 py-3.5 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-xl shadow-blue-100">Create Product</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT PRODUCT MODAL -->
    <div id="editProductModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 p-6">
        <div class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl p-10 transform scale-95 transition-all duration-300 overflow-hidden relative">
            <button onclick="toggleEditModal(false)" class="absolute top-8 right-10 h-10 w-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fa-solid fa-xmark"></i>
            </button>
            
            <div class="mb-10">
                <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Edit Product</h2>
                <p class="text-gray-500 font-medium">Update the details for this seafood item.</p>
            </div>

            <div id="editAlert" class="hidden p-4 rounded-2xl mb-8 text-sm font-bold border italic"></div>

            <form id="editProductForm" class="grid grid-cols-2 gap-8">
                <input type="hidden" name="id" id="editProdId">
                
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Product Name</label>
                    <input type="text" name="name" id="editName" class="form-input" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">SKU Number</label>
                    <input type="text" name="sku" id="editSku" class="form-input" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Category</label>
                    <select name="category" id="editCategory" class="form-input">
                        <option value="Fresh">Fresh Seafood</option>
                        <option value="Frozen">Frozen Products</option>
                        <option value="Shellfish">Shellfish</option>
                        <option value="Canned">Canned Seafood</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Price Per KG ($)</label>
                    <input type="number" step="0.01" name="price_per_kg" id="editPrice" class="form-input" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Stock Level (KG)</label>
                    <input type="number" step="0.01" name="stock_level_kg" id="editStock" class="form-input" required>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400 tracking-widest">Status</label>
                    <select name="status" id="editStatus" class="form-input">
                        <option value="In Stock">In Stock</option>
                        <option value="Low Stock">Low Stock</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>

                <div class="col-span-2 flex justify-end gap-4 mt-6">
                    <button type="button" onclick="toggleEditModal(false)" class="px-8 py-3.5 rounded-2xl font-bold text-gray-400 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" id="editBtn" class="bg-[#2563eb] text-white px-10 py-3.5 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-xl shadow-blue-100">Apply Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE PRODUCT MODAL -->
    <div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300 p-6">
        <div class="bg-white w-full max-w-sm rounded-[3rem] shadow-2xl p-10 transform scale-95 transition-all duration-300 text-center">
            <div class="h-20 w-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
                <i class="fa-solid fa-trash"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Delete Item?</h3>
            <p class="text-gray-500 font-medium mb-8 leading-relaxed">Are you sure you want to remove <span id="delProdName" class="text-gray-900 font-bold"></span> from your inventory?</p>
            
            <div class="flex flex-col gap-3">
                <button id="confirmDelBtn" class="w-full bg-red-500 text-white py-4 rounded-2xl font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-100">Yes, Remove Item</button>
                <button onclick="toggleDeleteModal(false)" class="w-full bg-gray-50 text-gray-400 py-4 rounded-2xl font-bold hover:bg-gray-100 transition-all">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // Modal Toggles
        function toggleAddModal(show) {
            const modal = document.getElementById('addProductModal');
            const card = modal.querySelector('div');
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-95');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.add('scale-95');
                document.getElementById('addProductForm').reset();
                document.getElementById('imagePreview').src = '../Images/products/default_fish.png';
                document.getElementById('addAlert').classList.add('hidden');
            }
        }

        function toggleEditModal(show) {
            const modal = document.getElementById('editProductModal');
            const card = modal.querySelector('div');
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-95');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.add('scale-95');
                document.getElementById('editAlert').classList.add('hidden');
            }
        }

        function toggleDeleteModal(show) {
            const modal = document.getElementById('deleteModal');
            const card = modal.querySelector('div');
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-95');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.add('scale-95');
            }
        }

        // Image Preview
        document.getElementById('imageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Add Product AJAX
        document.getElementById('addProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('addBtn');
            const alert = document.getElementById('addAlert');
            const formData = new FormData(this);
            formData.append('action', 'create');

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Adding...';

            try {
                const response = await fetch('handlers/product_handler.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.success) {
                    alert.className = "p-4 rounded-2xl mb-8 text-sm font-bold border italic bg-green-50 text-green-600 border-green-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-check mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert.className = "p-4 rounded-2xl mb-8 text-sm font-bold border italic bg-red-50 text-red-600 border-red-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                }
            } catch (err) { console.error(err); }
            finally { btn.disabled = false; btn.innerText = 'Create Product'; }
        });

        // Edit Product Handler
        function openEditModal(prod) {
            document.getElementById('editProdId').value = prod.id;
            document.getElementById('editName').value = prod.name;
            document.getElementById('editSku').value = prod.sku;
            document.getElementById('editCategory').value = prod.category;
            document.getElementById('editPrice').value = prod.price_per_kg;
            document.getElementById('editStock').value = prod.stock_level_kg;
            document.getElementById('editStatus').value = prod.status;
            toggleEditModal(true);
        }

        document.getElementById('editProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('editBtn');
            const alert = document.getElementById('editAlert');
            const formData = new FormData(this);
            formData.append('action', 'update');

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Updating...';

            try {
                const response = await fetch('handlers/product_handler.php', { method: 'POST', body: formData });
                const data = await response.json();

                if (data.success) {
                    alert.className = "p-4 rounded-2xl mb-8 text-sm font-bold border italic bg-green-50 text-green-600 border-green-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-check mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert.className = "p-4 rounded-2xl mb-8 text-sm font-bold border italic bg-red-50 text-red-600 border-red-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                }
            } catch (err) { console.error(err); }
            finally { btn.disabled = false; btn.innerText = 'Apply Changes'; }
        });

        // Delete Handler
        let prodToDelete = null;
        function confirmDelete(id, name) {
            prodToDelete = id;
            document.getElementById('delProdName').innerText = name;
            toggleDeleteModal(true);
        }

        document.getElementById('confirmDelBtn').addEventListener('click', async function() {
            if (!prodToDelete) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Removing...';

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', prodToDelete);

            try {
                const response = await fetch('handlers/product_handler.php', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) {
                    const row = document.querySelector(`tr[data-id="${prodToDelete}"]`);
                    row.classList.add('opacity-0', '-translate-x-10');
                    setTimeout(() => row.remove(), 300);
                    toggleDeleteModal(false);
                }
            } catch (err) { console.error(err); }
            finally { btn.disabled = false; btn.innerText = 'Yes, Remove Item'; }
        });

        // LIVE SEARCH
        document.getElementById('productSearchInput').addEventListener('input', function() {
            const query = this.value;
            const tbody = document.getElementById('productTableBody');

            fetch(`handlers/product_handler.php?action=search&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="px-8 py-5 text-center text-gray-400">No products found.</td></tr>';
                        return;
                    }

                    data.forEach(prod => {
                        const statusClass = {
                            'In Stock': 'bg-green-50 text-green-600 border-green-100',
                            'Low Stock': 'bg-orange-50 text-orange-600 border-orange-100',
                            'Out of Stock': 'bg-red-50 text-red-600 border-red-100'
                        }[prod.status] || 'bg-gray-50 text-gray-600 border-gray-100';

                        const tr = `
                        <tr class="hover:bg-gray-50/50 transition-all group" data-id="${prod.id}">
                            <td class="px-8 py-5">
                                <div class="h-14 w-14 bg-gray-50 rounded-2xl flex items-center justify-center overflow-hidden border border-gray-100">
                                    <img src="../Images/products/${prod.image_path}" 
                                         alt="Product" 
                                         class="h-full w-full object-cover transition-transform group-hover:scale-110"
                                         onerror="this.src='../Images/products/default_fish.png'">
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 leading-none mb-1">${prod.name}</h4>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">SKU: ${prod.sku}</p>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">${prod.category}</td>
                            <td class="px-8 py-5 text-sm font-black text-gray-900">$${parseFloat(prod.price_per_kg).toFixed(2)}/KG</td>
                            <td class="px-8 py-5 text-sm font-medium text-gray-500">${parseInt(prod.stock_level_kg)} KG</td>
                            <td class="px-8 py-5">
                                <span class="${statusClass} px-3 py-1 rounded-lg border text-[9px] font-black uppercase tracking-wider">
                                    ${prod.status}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all">
                                    <button onclick='openEditModal(${JSON.stringify(prod)})' class="h-9 w-9 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-pencil text-sm"></i>
                                    </button>
                                    <button onclick="confirmDelete(${prod.id}, '${prod.name.replace(/'/g, "\\'")}')" class="h-9 w-9 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;
                        tbody.innerHTML += tr;
                    });
                })
                .catch(err => console.error(err));
        });
    </script>
</body>
</html>
