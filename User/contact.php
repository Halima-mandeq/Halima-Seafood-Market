<?php
// User/contact.php
include '../Includes/db.php';
session_start();
$current_page = 'contact';

// Authentication check removed to allow public access
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body>

    <?php include '../Includes/user_header.php'; ?>

    <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col lg:flex-row gap-8 items-start h-[calc(100vh-80px)]">
        
        <!-- LEFT SIDEBAR -->
        <aside class="w-full lg:w-1/3 space-y-6">
            
            <!-- Branding Card -->
            <div class="bg-white p-10 rounded-[2rem] shadow-sm border border-gray-100 text-center">
                <div class="h-24 w-24 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <img src="../Images/Logo.png" alt="Halima Seafood" class="h-12 w-auto">
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-1">Halima Seafood</h2>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Real-time Support</p>
            </div>

            <!-- Contact Info -->
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Contact Info</h3>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 mb-1">Email</p>
                            <p class="text-sm font-bold text-gray-900">amindir2022@gmail.com</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 mb-1">Phone</p>
                            <p class="text-sm font-bold text-gray-900">+252 61 9896704</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-400 mb-1">Location</p>
                            <p class="text-sm font-bold text-gray-900">Mogadishu, Somalia</p>
                        </div>
                    </div>
                </div>
            </div>

        </aside>

        <!-- CHAT INTERFACE / LOGIN PROMPT -->
        <main class="w-full lg:w-2/3 bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px] lg:h-full relative">
            <?php if ($is_logged_in): ?>
                <!-- Clean Chat Interface for Logged In Users -->
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white sticky top-0 z-10">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg">
                            A
                        </div>
                        <div>
                            <h3 class="font-black text-gray-900 text-lg">Admin</h3>
                            <div class="flex items-center gap-2">
                                <span class="h-2 w-2 bg-green-500 rounded-full animate-pulse"></span>
                                <span class="text-xs font-bold text-green-500 uppercase tracking-wider">Online Now</span>
                            </div>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                </div>

                <div id="chatMessages" class="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50/50 scrollbar-hide">
                    <div class="flex justify-center">
                        <div class="bg-gray-100 text-gray-400 px-4 py-2 rounded-full text-xs font-bold">
                            Connecting to support...
                        </div>
                    </div>
                </div>

                <div class="p-6 bg-white border-t border-gray-50">
                    <!-- Negotiation Preview Container -->
                    <div id="negotiationPreview" class="hidden mb-4 p-4 bg-blue-50 border border-blue-100 rounded-2xl relative">
                        <button onclick="clearNegotiation()" class="absolute -top-2 -right-2 bg-red-500 text-white h-6 w-6 rounded-full flex items-center justify-center text-xs shadow-md hover:bg-red-600"><i class="fa-solid fa-xmark"></i></button>
                        <div id="previewContent"></div>
                    </div>

                    <form id="chatForm" class="flex items-center gap-4 bg-gray-50 p-2 pr-2 rounded-full border border-gray-100 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                        <button type="button" class="h-10 w-10 text-gray-400 hover:text-blue-600 transition-colors flex items-center justify-center rounded-full">
                            <i class="fa-solid fa-paperclip"></i>
                        </button>
                        <input type="text" id="messageInput" class="flex-1 bg-transparent border-none outline-none text-sm font-medium text-gray-700 placeholder-gray-400" placeholder="Type your message..." autocomplete="off">
                        <button type="submit" class="h-10 w-10 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg shadow-blue-200 hover:bg-blue-700 hover:scale-105 transition-all">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <!-- Login Prompt for Guests -->
                <div class="h-full flex flex-col items-center justify-center text-center p-12 bg-gray-50/50">
                    <div class="h-20 w-20 bg-white rounded-full flex items-center justify-center text-3xl shadow-sm mb-6 animate-bounce">
                        👋
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 mb-3">Login to Chat</h3>
                    <p class="text-gray-500 mb-8 max-w-sm mx-auto">Connect directly with our support team for specialized assistance with your orders and inquiries.</p>
                    <div class="flex flex-col sm:flex-row gap-4 w-full max-w-xs">
                        <a href="../Auth/index.php?form=login" class="flex-1 bg-blue-600 text-white py-3.5 px-6 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                            Log In
                        </a>
                        <a href="../Auth/index.php?form=register" class="flex-1 bg-white text-gray-900 border border-gray-200 py-3.5 px-6 rounded-xl font-bold hover:bg-gray-50 transition-all">
                            Register
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Hidden Footer on Desktop for full height chat, distinct from mobile -->
    <div class="lg:hidden">
        <?php include '../Includes/user_footer.php'; ?>
    </div>

    <?php if ($is_logged_in): ?>
    <script>
        const chatMessages = document.getElementById('chatMessages');
        const chatForm = document.getElementById('chatForm');
        const messageInput = document.getElementById('messageInput');
        const negotiationPreview = document.getElementById('negotiationPreview');
        const previewContent = document.getElementById('previewContent');
        let lastMessageCount = 0;
        let pendingCardHTML = '';

        // Check for Negotiation Draft
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('negotiate') === 'true') {
            const product = urlParams.get('product');
            const image = urlParams.get('image');
            const qty = urlParams.get('qty');
            const offer = parseFloat(urlParams.get('offer')).toFixed(2);
            const base = parseFloat(urlParams.get('base')).toFixed(2);
            const totalOffer = (offer * qty).toFixed(2);

            // Construct Card HTML
            pendingCardHTML = `
                <div class='p-4 bg-white rounded-2xl border border-blue-100 shadow-sm mb-2'>
                    <div class='flex gap-4 items-center mb-3'>
                        <img src='../Images/products/${image}' class='w-16 h-16 object-cover rounded-xl bg-gray-50'>
                        <div>
                            <div class='text-[10px] font-bold text-blue-600 uppercase tracking-widest'>Negotiation Offer</div>
                            <h4 class='font-black text-gray-900 leading-tight'>${product}</h4>
                        </div>
                    </div>
                    <div class='grid grid-cols-2 gap-2 text-sm'>
                        <div class='bg-gray-50 p-2 rounded-lg'>
                            <span class='block text-[10px] text-gray-400 font-bold uppercase'>My Offer</span>
                            <span class='block font-black text-blue-600'>$${offer}/kg</span>
                        </div>
                        <div class='bg-gray-50 p-2 rounded-lg'>
                            <span class='block text-[10px] text-gray-400 font-bold uppercase'>Quantity</span>
                            <span class='block font-black text-gray-900'>${qty} KG</span>
                        </div>
                    </div>
                    <div class='mt-3 pt-3 border-t border-gray-100 flex justify-between items-end'>
                        <span class='text-xs font-bold text-gray-400'>Total Offer</span>
                        <span class='font-black text-gray-900 text-lg'>$${totalOffer}</span>
                    </div>
                </div>`;

            // Show Preview
            previewContent.innerHTML = pendingCardHTML;
            negotiationPreview.classList.remove('hidden');
            
            // Pre-fill text
            messageInput.value = `Hi, I would like to pay $${offer}/kg instead of $${base}/kg. Is this acceptable?`;
            messageInput.focus();
            
            // Clean URL
            window.history.replaceState({}, document.title, window.location.pathname);
        } else if (urlParams.get('message')) {
            // Legacy simple message support
             messageInput.value = urlParams.get('message');
             messageInput.focus();
        }

        function clearNegotiation() {
            negotiationPreview.classList.add('hidden');
            pendingCardHTML = '';
            messageInput.value = '';
        }

        // Function to load messages
        function loadMessages() {
            fetch('handlers/chat_handler.php?action=fetch')
                .then(response => response.json())
                .then(data => {
                    if (data.length !== lastMessageCount) {
                        chatMessages.innerHTML = ''; // Clear current
                        
                        data.forEach(msg => {
                            const isMe = msg.is_me;
                            const div = document.createElement('div');
                            div.className = `flex ${isMe ? 'justify-end' : 'justify-start'}`;
                            
                            div.innerHTML = `
                                <div class="max-w-[75%]">
                                    <div class="${isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white text-gray-700 border border-gray-100 rounded-tl-none'} p-4 rounded-2xl shadow-sm text-sm font-medium leading-relaxed">
                                        ${msg.message}
                                    </div>
                                    <div class="text-[10px] font-bold text-gray-400 mt-2 ${isMe ? 'text-right' : 'text-left'}">
                                        ${msg.created_at}
                                    </div>
                                </div>
                            `;
                            chatMessages.appendChild(div);
                        });

                        // Scroll to bottom
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                        lastMessageCount = data.length;
                    }
                })
                .catch(err => console.error('Error loading messages:', err));
        }

        // Send Message
        chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            let message = messageInput.value.trim();
            if (!message && !pendingCardHTML) return;

            // Combine Card + Message
            if (pendingCardHTML) {
                message = pendingCardHTML + `<div class='text-sm font-medium text-gray-600'>${message}</div>`;
            }

            const formData = new FormData();
            formData.append('action', 'send');
            formData.append('message', message);

            fetch('handlers/chat_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    messageInput.value = '';
                    loadMessages(); // Refresh immediately
                }
            });
        });

        // Poll every 3 seconds
        loadMessages();
        setInterval(loadMessages, 3000);
    </script>
    <?php endif; ?>
</body>
</html>
