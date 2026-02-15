<?php
// Includes/user_footer.php
?>
<footer class="bg-[#0f172a] text-white pt-20 pb-10">
    <div class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-16">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-3 mb-6">
                    <img src="../Images/Logo.png" alt="Halima Seafood" class="h-8 w-auto brightness-0 invert opacity-80">
                    <span class="text-xl font-black tracking-tighter">Halima Seafood</span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">
                    Premium seafood marketplace dedicated to bringing the freshest ocean treasures directly to your kitchen. Sourced responsibly, delivered fresh.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-all"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-all"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="h-8 w-8 bg-white/10 rounded-full flex items-center justify-center text-xs hover:bg-blue-600 transition-all"><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 class="font-bold mb-6 text-sm uppercase tracking-widest">Quick Links</h4>
                <ul class="space-y-4 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Our Story</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Fresh Market Shop</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Recipes & Tips</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Shipping & Delivery</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors">Sustainability Commitment</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-bold mb-6 text-sm uppercase tracking-widest">Contact Info</h4>
                <ul class="space-y-6 text-sm text-gray-400">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-1 text-blue-500"></i>
                        <span>15B Fisherman's Wharf,<br>Maritime District, Cape Town</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-blue-500"></i>
                        <span>+27 (21) 555-0123</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-blue-500"></i>
                        <span>fresh@halimaseafood.com</span>
                    </li>
                </ul>
            </div>

            <!-- Map Placeholder -->
            <div>
                <h4 class="font-bold mb-6 text-sm uppercase tracking-widest">Market Location</h4>
                <div class="h-40 bg-gray-800 rounded-2xl flex items-center justify-center overflow-hidden relative group cursor-pointer">
                    <img src="../Images/map_placeholder.png" alt="Map" class="w-full h-full object-cover opacity-50 group-hover:opacity-70 transition-all" onerror="this.src='https://via.placeholder.com/400x300/374151/9ca3af?text=Map+Location'">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fa-solid fa-location-dot text-3xl text-blue-500 drop-shadow-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-10 flex flex-col md:flex-row justify-between items-center gap-6 text-xs text-gray-500 font-medium">
            <p>&copy; 2026 Halima Seafood Market. All rights reserved.</p>
            <div class="flex gap-8">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>
