<?php
// Admin/messages.php
include '../Includes/db.php';
session_start();

// Basic security check (admin only)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../Auth/index.php");
    exit();
}

$current_page = 'messages';
$admin_id = $_SESSION['user_id'];
$admin_name = $_SESSION['full_name'] ?? "Admin Account";
session_write_close(); // Release session lock early for this heavy page


// Fetch Customer Chat List (Customers Only)
$sql = "SELECT DISTINCT u.id, u.full_name, u.role,
        (SELECT message FROM messages WHERE (sender_id = u.id AND receiver_id IN (SELECT id FROM users WHERE role='admin')) OR (sender_id IN (SELECT id FROM users WHERE role='admin') AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_msg,
        (SELECT created_at FROM messages WHERE (sender_id = u.id AND receiver_id IN (SELECT id FROM users WHERE role='admin')) OR (sender_id IN (SELECT id FROM users WHERE role='admin') AND receiver_id = u.id) ORDER BY created_at DESC LIMIT 1) as last_time,
        (SELECT is_read FROM messages WHERE sender_id = u.id AND receiver_id IN (SELECT id FROM users WHERE role='admin') ORDER BY created_at DESC LIMIT 1) as is_read
        FROM users u 
        WHERE u.role = 'user'
        HAVING last_msg IS NOT NULL
        ORDER BY last_time DESC";

$users_query = mysqli_query($conn, $sql);

// Fetch Products for "Special Offer" Modal
$prod_query = mysqli_query($conn, "SELECT id, name, price_per_kg FROM products WHERE status != 'Out of Stock'");
$products = [];
while($p = mysqli_fetch_assoc($prod_query)) {
    $products[] = $p;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Halima Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; overflow: hidden; }
        .sidebar-link.active {
            background-color: #eff6ff;
            color: #2563eb;
            border-radius: 12px;
        }
        .chat-list-item.active {
            background-color: #f8fafc;
            border-left: 4px solid #2563eb;
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body class="bg-white flex h-screen">

    <?php include 'Includes/sidebar.php'; ?>

    <!-- MESSAGE LIST (SUB-SIDEBAR) -->
    <div class="w-80 ml-64 border-r border-gray-100 flex flex-col h-full bg-[#fcfcfc]">
        <div class="p-8">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-6">Messages</h1>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="messageSearchInput" placeholder="Search conversations..." class="w-full bg-white border border-gray-100 py-3 pl-11 pr-4 rounded-2xl text-xs focus:ring-2 focus:ring-blue-100 transition-all outline-none shadow-sm">
            </div>
        </div>

        <div id="chatListContainer" class="flex-1 overflow-y-auto space-y-px">
            <?php while($user = mysqli_fetch_assoc($users_query)): ?>
            <button onclick="loadChat(<?php echo $user['id']; ?>, '<?php echo addslashes($user['full_name']); ?>')" 
                    class="w-full flex items-center gap-4 px-8 py-6 hover:bg-gray-50 transition-all text-left chat-list-item" 
                    data-user-id="<?php echo $user['id']; ?>">
                <div class="relative flex-shrink-0">
                    <div class="h-12 w-12 <?php echo $user['role'] == 'admin' ? 'bg-blue-100 text-blue-600' : ($user['role'] == 'staff' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600'); ?> rounded-full flex items-center justify-center font-black text-sm">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <span class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 rounded-full border-2 border-white"></span>
                </div>
                <div class="flex-1 overflow-hidden">
                    <div class="flex justify-between items-baseline mb-1">
                        <div class="flex items-center gap-2 truncate">
                            <h4 class="text-sm font-bold text-gray-900 truncate"><?php echo $user['full_name']; ?></h4>
                        </div>
                        <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap ml-2">
                            <?php echo $user['last_time'] ? date('G:i', strtotime($user['last_time'])) : ''; ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 truncate <?php echo $user['is_read'] === '0' ? 'font-black text-gray-900' : 'font-medium'; ?>">
                        <?php echo $user['last_msg'] ?: 'No messages yet'; ?>
                    </p>
                    <?php if($user['is_read'] === '0'): ?>
                        <span class="mt-2 inline-block bg-red-50 text-red-500 px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider">Urgent</span>
                    <?php endif; ?>
                </div>
            </button>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- ACTIVE CHAT AREA -->
    <div id="chatArea" class="flex-1 flex flex-col h-full bg-white relative">
        <!-- Default State -->
        <div id="noChatSelected" class="absolute inset-0 flex items-center justify-center bg-gray-50/30 flex-col text-center p-10">
            <div class="h-20 w-20 bg-blue-50 text-blue-400 rounded-full flex items-center justify-center text-4xl mb-6 shadow-sm">
                <i class="fa-solid fa-comment-dots"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 mb-2">Select a Conversation</h3>
            <p class="text-gray-400 text-sm max-w-xs font-medium">Choose a customer from the left to start responding to their seafood inquiries.</p>
        </div>

        <!-- Chat Header -->
        <div id="activeChatHeader" class="hidden px-8 py-6 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center overflow-hidden">
                    <i class="fa-solid fa-user text-gray-300 text-xl"></i>
                </div>
                <div>
                    <h3 id="chatUserName" class="text-lg font-black text-gray-900">Amina Yusuf</h3>
                    <div class="flex items-center gap-2">
                        <span class="h-2 w-2 bg-green-500 rounded-full"></span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Online</span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button class="bg-white border border-gray-100 px-5 py-2.5 rounded-xl text-xs font-bold text-gray-600 flex items-center gap-3 hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fa-solid fa-receipt text-blue-500"></i> View Orders
                </button>
                <button class="h-10 w-10 text-gray-300 hover:text-gray-900 transition-all">
                    <i class="fa-solid fa-ellipsis-vertical text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Messages Pane -->
        <div id="messagesHistory" class="hidden flex-1 overflow-y-auto p-8 space-y-10 bg-gray-50/20">
            <!-- Messages will be injected here -->
        </div>

        <!-- Message Input -->
        <div id="activeChatInput" class="hidden p-8 border-t border-gray-100 bg-white">
            <form id="sendMessageForm" class="relative">
                <div class="flex items-center gap-4">
                    <button type="button" class="h-10 w-10 text-gray-300 hover:text-blue-600 transition-all">
                        <i class="fa-solid fa-plus-circle text-2xl"></i>
                    </button>
                    <div class="flex-1 relative">
                        <input type="text" id="messageInput" name="message" placeholder="Type your message here..." class="w-full bg-gray-50 border border-gray-100 py-4 px-6 rounded-2xl text-sm focus:ring-2 focus:ring-blue-100 transition-all outline-none" required>
                    </div>
                    <button type="submit" class="h-14 w-14 bg-blue-600 text-white rounded-2xl flex items-center justify-center shadow-xl shadow-blue-100 hover:bg-blue-700 transition-all">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="flex gap-6 mt-4 ml-14">
                    <button type="button" class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 hover:text-blue-500"><i class="fa-solid fa-image"></i> Photos</button>
                    <button type="button" class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-2 hover:text-blue-500"><i class="fa-solid fa-file"></i> Documents</button>
                    <button type="button" onclick="openOfferModal()" class="text-[10px] font-black text-blue-600 uppercase tracking-widest flex items-center gap-2 hover:text-blue-700 bg-blue-50 px-3 py-1 rounded-lg"><i class="fa-solid fa-bolt"></i> Custom Offer</button>
                    <span class="text-[10px] font-bold text-gray-300 ml-auto uppercase tracking-widest italic">Press Enter to send</span>
                </div>
            </form>
        </div>
    </div>

    <!-- SPECIAL OFFER MODAL -->
    <div id="offerModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-[2rem] w-full max-w-sm p-8 shadow-2xl transform scale-95 transition-transform duration-300" id="offerModalContent">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-gray-900 flex items-center gap-2"><i class="fa-solid fa-bolt text-yellow-500"></i> Special Offer</h3>
                <button onclick="closeOfferModal()" class="h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-500 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <form id="offerForm" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Select Product</label>
                    <select id="offerProduct" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 font-bold text-gray-900 outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer" onchange="updateBasePrice()">
                        <option value="">-- Choose Product --</option>
                        <?php foreach($products as $p): ?>
                            <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['price_per_kg']; ?>"><?php echo $p['name']; ?> ($<?php echo $p['price_per_kg']; ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Special Price ($/kg)</label>
                    <input type="number" id="offerPrice" class="w-full bg-white border-2 border-blue-100 rounded-xl px-4 py-3 font-bold text-blue-600 outline-none focus:border-blue-500 transition-all" step="0.01" placeholder="0.00" required>
                </div>

                <p class="text-[10px] text-gray-400 font-medium bg-gray-50 p-3 rounded-lg">
                    <i class="fa-solid fa-clock"></i> This price will be valid for <strong>24 hours</strong> for this user only.
                </p>

                <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-xl font-bold text-lg shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                    Send Offer
                </button>
            </form>
        </div>
    </div>

    <script>
        let currentReceiverId = null;

        async function loadChat(userId, userName) {
            currentReceiverId = userId;
            
            // UI Updates
            document.getElementById('noChatSelected').classList.add('hidden');
            document.getElementById('activeChatHeader').classList.remove('hidden');
            document.getElementById('messagesHistory').classList.remove('hidden');
            document.getElementById('activeChatInput').classList.remove('hidden');
            document.getElementById('chatUserName').innerText = userName;

            // Highlight selected
            document.querySelectorAll('.chat-list-item').forEach(item => {
                item.classList.remove('active');
                if(item.getAttribute('data-user-id') == userId) item.classList.add('active');
            });

            await refreshMessages();
        }

        async function refreshMessages() {
            if (!currentReceiverId) return;
            const res = await fetch(`handlers/message_handler.php?action=fetch&receiver_id=${currentReceiverId}`);
            const data = await res.json();

            const history = document.getElementById('messagesHistory');
            history.innerHTML = '';

            let lastDate = '';

            data.messages.forEach(msg => {
                const date = new Date(msg.created_at).toLocaleDateString();
                if (date !== lastDate) {
                    history.innerHTML += `
                        <div class="flex justify-center my-8">
                            <span class="bg-gray-100 text-[10px] font-black uppercase text-gray-400 px-4 py-1.5 rounded-full tracking-widest shadow-sm">${date === new Date().toLocaleDateString() ? 'Today' : date}</span>
                        </div>
                    `;
                    lastDate = date;
                }

                const isMe = msg.sender_id == <?php echo $admin_id; ?>;
                const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                if (isMe) {
                    history.innerHTML += `
                        <div class="flex justify-end group">
                            <div class="max-w-[70%] space-y-2">
                                <div class="bg-blue-600 text-white p-5 rounded-[2rem] rounded-tr-none text-sm font-medium shadow-lg shadow-blue-50">
                                    ${msg.message}
                                </div>
                                <p class="text-[10px] font-bold text-gray-300 text-right uppercase tracking-widest">${time} • Delivered</p>
                            </div>
                        </div>
                    `;
                } else {
                    history.innerHTML += `
                        <div class="flex justify-start group">
                            <div class="max-w-[70%] space-y-2">
                                <div class="bg-white border border-gray-50 p-6 rounded-[2.5rem] rounded-tl-none text-sm font-bold text-gray-800 shadow-sm leading-relaxed">
                                    ${msg.message}
                                </div>
                                <p class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">${time}</p>
                            </div>
                        </div>
                    `;
                }
            });

            history.scrollTop = history.scrollHeight;
        }
        
        // Event delegation for dynamic negotiation cards
        document.getElementById('messagesHistory').addEventListener('click', function(e) {
            const card = e.target.closest('.negotiation-card');
            if(card) {
                const productId = card.getAttribute('data-product-id');
                const offerPrice = card.getAttribute('data-offer');
                if(productId && offerPrice) {
                    openOfferModal(productId, offerPrice);
                }
            }
        });

        document.getElementById('sendMessageForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const input = document.getElementById('messageInput');
            const message = input.value;
            if (!message || !currentReceiverId) return;

            const formData = new FormData();
            formData.append('action', 'send');
            formData.append('receiver_id', currentReceiverId);
            formData.append('message', message);

            input.value = '';
            
            try {
                const res = await fetch('handlers/message_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                if (data.success) {
                    await refreshMessages();
                }
            } catch (err) { console.error(err); }
        });

        // Polling for new messages
        setInterval(() => {
            if (currentReceiverId) refreshMessages();
        }, 5000);

        // LIVE SEARCH FOR USERS
        document.getElementById('messageSearchInput').addEventListener('input', function() {
            const query = this.value;
            const container = document.getElementById('chatListContainer');

            fetch(`handlers/message_handler.php?action=search_users&query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    container.innerHTML = '';
                    if (data.length === 0) {
                        container.innerHTML = '<div class="p-8 text-center text-gray-400 text-xs font-bold">No users found.</div>';
                        return;
                    }

                    data.forEach(user => {
                        const initial = user.full_name.charAt(0).toUpperCase();
                        const roleColor = user.role === 'admin' ? 'bg-blue-100 text-blue-600' : 
                                         (user.role === 'staff' ? 'bg-green-100 text-green-600' : 'bg-orange-100 text-orange-600');
                        
                        const lastTime = user.last_time ? new Date(user.last_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) : '';
                        const lastMsg = user.last_msg || 'No messages yet';
                        const msgClass = user.is_read === '0' ? 'font-black text-gray-900' : 'font-medium';
                        const urgentBadge = user.is_read === '0' ? 
                            `<span class="mt-2 inline-block bg-red-50 text-red-500 px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-wider">Urgent</span>` : '';

                        const button = `
                        <button onclick="loadChat(${user.id}, '${user.full_name.replace(/'/g, "\\'")}')" 
                                class="w-full flex items-center gap-4 px-8 py-6 hover:bg-gray-50 transition-all text-left chat-list-item" 
                                data-user-id="${user.id}">
                            <div class="relative flex-shrink-0">
                                <div class="h-12 w-12 ${roleColor} rounded-full flex items-center justify-center font-black text-sm">
                                    ${initial}
                                </div>
                                <span class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 rounded-full border-2 border-white"></span>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <div class="flex justify-between items-baseline mb-1">
                                    <div class="flex items-center gap-2 truncate">
                                        <h4 class="text-sm font-bold text-gray-900 truncate">${user.full_name}</h4>
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-medium whitespace-nowrap ml-2">
                                        ${lastTime}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 truncate ${msgClass}">
                                    ${lastMsg}
                                </p>
                                ${urgentBadge}
                            </div>
                        </button>
                        `;
                        container.innerHTML += button;
                    });
                    
                    if (currentReceiverId) {
                        const activeItem = document.querySelector(`.chat-list-item[data-user-id='${currentReceiverId}']`);
                        if (activeItem) activeItem.classList.add('active');
                    }
                })
                .catch(err => console.error(err));
        });

        // OFFER MODAL LOGIC
        const offerModal = document.getElementById('offerModal');
        const offerContent = document.getElementById('offerModalContent');

        function openOfferModal(productId = null, price = null) {
            if(!currentReceiverId) {
                alert("Please select a user first.");
                return;
            }
            
            // Pre-fill if data provided
            if(productId) {
                const select = document.getElementById('offerProduct');
                select.value = productId;
                if(price) {
                     document.getElementById('offerPrice').value = price;
                }
                updateBasePrice(); 
            } else {
                 // Reset if opening manually
                 document.getElementById('offerProduct').value = "";
                 document.getElementById('offerPrice').value = "";
                 document.getElementById('offerPrice').placeholder = "0.00";
            }

            offerModal.classList.remove('hidden');
            setTimeout(() => {
                offerModal.classList.remove('opacity-0');
                offerContent.classList.remove('scale-95');
                offerContent.classList.add('scale-100');
            }, 10);
        }

        function closeOfferModal() {
            offerModal.classList.add('opacity-0');
            offerContent.classList.remove('scale-100');
            offerContent.classList.add('scale-95');
            setTimeout(() => {
                offerModal.classList.add('hidden');
            }, 300);
        }

        function updateBasePrice() {
            const select = document.getElementById('offerProduct');
            const price = select.options[select.selectedIndex].getAttribute('data-price');
            if(price) {
                document.getElementById('offerPrice').placeholder = price;
            }
        }

        document.getElementById('offerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const productId = document.getElementById('offerProduct').value;
            const price = document.getElementById('offerPrice').value;
            const productName = document.getElementById('offerProduct').options[document.getElementById('offerProduct').selectedIndex].text.split(' ($')[0];

            if(!productId || !price) return;

            const formData = new FormData();
            formData.append('user_id', currentReceiverId); // The active chat user
            formData.append('product_id', productId);
            formData.append('price', price);
            formData.append('product_name', productName);

            try {
                const res = await fetch('handlers/price_handler.php', { method: 'POST', body: formData });
                const data = await res.json();
                
                if(data.success) {
                    closeOfferModal();
                    // Send notification message to chat
                    const msgForm = new FormData();
                    msgForm.append('action', 'send');
                    msgForm.append('receiver_id', currentReceiverId);
                    msgForm.append('message', `⚡ <strong>Special Offer!</strong><br>I've updated the price of <strong>${productName}</strong> to <strong>$${price}/kg</strong> for you. This offer is valid for 24 hours. Happy shopping!`);
                    
                    await fetch('handlers/message_handler.php', { method: 'POST', body: msgForm });
                    await refreshMessages();
                    alert("Special offer sent!");
                } else {
                    alert('Error: ' + data.error);
                }
            } catch(err) {
                console.error(err);
                alert("Failed to send offer");
            }
        });
    </script>
</body>
</html>
