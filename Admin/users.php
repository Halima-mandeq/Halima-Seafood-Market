<?php
// Admin/users.php
include '../Includes/db.php';
session_start();

// Basic security check (admin only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/index.php");
    exit();
}

$current_page = 'users';
$admin_name = $_SESSION['full_name'] ?? "Admin User";
$admin_email = "admin@halimaseafood.com";

// Fetch Stats
$total_admins = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'admin'"))[0] ?? 0;
$total_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'user' AND status = 'active'"))[0] ?? 0;
$new_users = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'user' AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))[0] ?? 0;

// Fetch Users for Table
$users_query = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Halima Admin</title>
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
            <div class="text-gray-400 text-xs font-bold uppercase tracking-widest">
                Admin <i class="fa-solid fa-chevron-right text-[8px] mx-2"></i> Users
            </div>
            <div class="flex items-center gap-4">
                <!-- Notifications and Help removed -->
            </div>
        </header>

        <!-- Page Title & Actions -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tight mb-2">Users</h1>
                <p class="text-gray-500 font-medium">Manage and monitor platform users across all categories.</p>
            </div>
            <button onclick="toggleModal(true)" class="bg-[#2563eb] text-white px-6 py-3.5 rounded-xl font-bold text-sm flex items-center gap-3 hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                <i class="fa-solid fa-user-plus"></i> Add New User
            </button>
        </div>

        <!-- Filters & Tools -->
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4 mb-8">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search users by name or email..." class="w-full bg-gray-50 border-none py-3 pl-12 pr-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
            </div>

        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mb-8">
            <table class="w-full text-left" id="usersTable">
                <thead>
                    <tr class="text-gray-400 uppercase text-[10px] font-black tracking-widest border-b border-gray-50">
                        <th class="px-8 py-6">Name</th>
                        <th class="px-8 py-6">Email</th>
                        <th class="px-8 py-6">Role</th>
                        <th class="px-8 py-6">Status</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="userTableBody">
                    <?php while($user = mysqli_fetch_assoc($users_query)): ?>
                    <tr class="hover:bg-gray-50/50 transition-all group" data-id="<?php echo $user['id']; ?>">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 
                                    <?php 
                                        $bg_colors = ['bg-blue-50 text-blue-600', 'bg-orange-50 text-orange-600', 'bg-green-50 text-green-600', 'bg-purple-50 text-purple-600'];
                                        echo $bg_colors[array_rand($bg_colors)]; 
                                    ?> 
                                    rounded-full flex items-center justify-center text-xs font-black">
                                    <?php 
                                        $parts = explode(' ', $user['full_name']);
                                        echo strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ""));
                                    ?>
                                </div>
                                <span class="text-sm font-bold text-gray-900"><?php echo $user['full_name']; ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-sm text-gray-500 font-medium"><?php echo $user['email']; ?></td>
                        <td class="px-8 py-5">
                            <?php 
                                $role_classes = [
                                    'admin' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'user' => 'bg-gray-50 text-gray-600 border-gray-200'
                                ];
                                $class = $role_classes[$user['role']] ?? $role_classes['user'];
                            ?>
                            <span class="<?php echo $class; ?> px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-wider role-badge">
                                <?php echo $user['role']; ?>
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full <?php echo $user['status'] == 'active' ? 'bg-green-500' : 'bg-gray-300'; ?>"></span>
                                <span class="text-xs font-bold status-text <?php echo $user['status'] == 'active' ? 'text-gray-700' : 'text-gray-400'; ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all">
                                <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)" class="h-8 w-8 text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button onclick="confirmDelete(<?php echo $user['id']; ?>, '<?php echo addslashes($user['full_name']); ?>')" class="h-8 w-8 text-red-500 hover:bg-red-50 rounded-lg transition-all flex items-center justify-center">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </main>

    <!-- ADD USER MODAL -->
    <div id="addUserModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10 transform scale-90 transition-all duration-300 overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Add New User</h2>
                    <p class="text-gray-500 font-medium">Create a new account member.</p>
                </div>
                <button onclick="toggleModal(false)" class="h-10 w-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div id="modalAlert" class="hidden p-4 rounded-xl mb-6 text-sm font-semibold border italic"></div>

            <form id="addUserForm" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Full Name</label>
                        <input type="text" name="full_name" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Email Address</label>
                        <input type="email" name="email" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" placeholder="john@example.com" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Username</label>
                        <input type="text" name="username" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" placeholder="@username" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Role</label>
                        <select name="role" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase text-gray-400">Password</label>
                    <input type="password" name="password" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" placeholder="............" required>
                </div>

                <div class="flex justify-end gap-3 mt-10">
                    <button type="button" onclick="toggleModal(false)" class="px-6 py-3 rounded-xl border border-gray-100 font-bold text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" id="submitBtn" class="bg-[#2563eb] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Create User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div id="editUserModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white w-full max-w-xl rounded-[2.5rem] shadow-2xl p-10 transform scale-90 transition-all duration-300 overflow-hidden">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Edit User</h2>
                    <p class="text-gray-500 font-medium">Update account details for this member.</p>
                </div>
                <button onclick="toggleEditModal(false)" class="h-10 w-10 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div id="editModalAlert" class="hidden p-4 rounded-xl mb-6 text-sm font-semibold border italic"></div>

            <form id="editUserForm" class="space-y-6">
                <input type="hidden" name="id" id="editUserId">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Full Name</label>
                        <input type="text" name="full_name" id="editFullName" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Email Address</label>
                        <input type="email" name="email" id="editEmail" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Username</label>
                        <input type="text" name="username" id="editUsername" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" required>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Role</label>
                        <select name="role" id="editRole" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="space-y-2 col-span-2">
                        <label class="block text-xs font-black uppercase text-gray-400">Status</label>
                        <select name="status" id="editStatus" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 rounded-xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-10">
                    <button type="button" onclick="toggleEditModal(false)" class="px-6 py-3 rounded-xl border border-gray-100 font-bold text-gray-500 hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" id="editSubmitBtn" class="bg-[#2563eb] text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div id="deleteModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center opacity-0 pointer-events-none transition-all duration-300">
        <div class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl p-10 transform scale-90 transition-all duration-300 text-center">
            <div class="h-20 w-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-6">
                <i class="fa-solid fa-trash-can"></i>
            </div>
            <h3 class="text-2xl font-black text-gray-900 mb-2">Delete User?</h3>
            <p class="text-gray-500 font-medium mb-8">Are you sure you want to delete <span id="deleteUserName" class="text-gray-900 font-bold"></span>? This action cannot be undone.</p>
            
            <div class="flex flex-col gap-3">
                <button id="confirmDeleteBtn" class="w-full bg-red-500 text-white py-4 rounded-2xl font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-100">Yes, Delete User</button>
                <button onclick="toggleDeleteModal(false)" class="w-full bg-gray-50 text-gray-500 py-4 rounded-2xl font-bold hover:bg-gray-100 transition-all">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        // MODAL TOGGLES
        function toggleModal(show) {
            const modal = document.getElementById('addUserModal');
            const card = modal.querySelector('div');
            const alert = document.getElementById('modalAlert');
            
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-90');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.add('scale-90');
                document.getElementById('addUserForm').reset();
                alert.classList.add('hidden');
            }
        }

        function toggleEditModal(show) {
            const modal = document.getElementById('editUserModal');
            const card = modal.querySelector('div');
            const alert = document.getElementById('editModalAlert');
            
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-90');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.add('scale-90');
                alert.classList.add('hidden');
            }
        }

        function toggleDeleteModal(show) {
            const modal = document.getElementById('deleteModal');
            const card = modal.querySelector('div');
            if (show) {
                modal.classList.remove('opacity-0', 'pointer-events-none');
                card.classList.remove('scale-90');
            } else {
                modal.classList.add('opacity-0', 'pointer-events-none');
                card.classList.add('scale-90');
            }
        }

        // OPEN EDIT MODAL
        function openEditModal(user) {
            document.getElementById('editUserId').value = user.id;
            document.getElementById('editFullName').value = user.full_name;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editUsername').value = user.username;
            document.getElementById('editRole').value = user.role;
            document.getElementById('editStatus').value = user.status;
            toggleEditModal(true);
        }

        // CONFIRM DELETE
        let userIdToDelete = null;
        function confirmDelete(id, name) {
            userIdToDelete = id;
            document.getElementById('deleteUserName').innerText = name;
            toggleDeleteModal(true);
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', async function() {
            if (!userIdToDelete) return;
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Deleting...';

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', userIdToDelete);

            try {
                const response = await fetch('handlers/user_handler.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    const row = document.querySelector(`tr[data-id="${userIdToDelete}"]`);
                    row.classList.add('opacity-0', '-translate-x-10');
                    setTimeout(() => row.remove(), 300);
                    toggleDeleteModal(false);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                btn.disabled = false;
                btn.innerText = 'Yes, Delete User';
            }
        });

        // ADD USER HANDLER
        document.getElementById('addUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const alert = document.getElementById('modalAlert');
            const formData = new FormData(this);
            formData.append('action', 'create');

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Creating...';

            try {
                const response = await fetch('handlers/user_handler.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    alert.className = "p-4 rounded-xl mb-6 text-sm font-semibold border italic bg-green-50 text-green-600 border-green-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-check mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                    
                    // Reload table or append (append is cleaner)
                    setTimeout(() => window.location.reload(), 1000); // Simple reload for now to get all badges etc right
                } else {
                    alert.className = "p-4 rounded-xl mb-6 text-sm font-semibold border italic bg-red-50 text-red-600 border-red-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                btn.disabled = false;
                btn.innerText = 'Create User';
            }
        });

        // EDIT USER HANDLER
        document.getElementById('editUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = document.getElementById('editSubmitBtn');
            const alert = document.getElementById('editModalAlert');
            const formData = new FormData(this);
            formData.append('action', 'edit');

            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Saving...';

            try {
                const response = await fetch('handlers/user_handler.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    alert.className = "p-4 rounded-xl mb-6 text-sm font-semibold border italic bg-green-50 text-green-600 border-green-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-check mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                    
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert.className = "p-4 rounded-xl mb-6 text-sm font-semibold border italic bg-red-50 text-red-600 border-red-100";
                    alert.innerHTML = '<i class="fa-solid fa-circle-exclamation mr-2"></i> ' + data.message;
                    alert.classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
            } finally {
                btn.disabled = false;
                btn.innerText = 'Save Changes';
            }
        });

        // LIVE SEARCH
        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value;
            const tbody = document.getElementById('userTableBody');

            fetch(`handlers/user_handler.php?action=search&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="px-8 py-5 text-center text-gray-400">No users found.</td></tr>';
                        return;
                    }

                    data.forEach(user => {
                        const parts = user.full_name.split(' ');
                        const initials = (parts[0][0] + (parts[1] ? parts[1][0] : '')).toUpperCase();
                        const bgColors = ['bg-blue-50 text-blue-600', 'bg-orange-50 text-orange-600', 'bg-green-50 text-green-600', 'bg-purple-50 text-purple-600'];
                        const randomColor = bgColors[Math.floor(Math.random() * bgColors.length)];
                        
                        const roleClass = user.role === 'admin' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-gray-50 text-gray-600 border-gray-200';
                        const statusDot = user.status === 'active' ? 'bg-green-500' : 'bg-gray-300';
                        const statusText = user.status === 'active' ? 'text-gray-700' : 'text-gray-400';

                        const tr = `
                        <tr class="hover:bg-gray-50/50 transition-all group" data-id="${user.id}">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 ${randomColor} rounded-full flex items-center justify-center text-xs font-black">
                                        ${initials}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900">${user.full_name}</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-500 font-medium">${user.email}</td>
                            <td class="px-8 py-5">
                                <span class="${roleClass} px-3 py-1 rounded-lg border text-[10px] font-black uppercase tracking-wider">
                                    ${user.role}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="h-1.5 w-1.5 rounded-full ${statusDot}"></span>
                                    <span class="text-xs font-bold ${statusText}">
                                        ${user.status.charAt(0).toUpperCase() + user.status.slice(1)}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-all">
                                    <button onclick='openEditModal(${JSON.stringify(user)})' class="h-8 w-8 text-blue-600 hover:bg-blue-50 rounded-lg transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button onclick="confirmDelete(${user.id}, '${user.full_name.replace(/'/g, "\\'")}')" class="h-8 w-8 text-red-500 hover:bg-red-50 rounded-lg transition-all flex items-center justify-center">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        `;
                        tbody.innerHTML += tr;
                    });
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
