<?php
// User/about.php
include '../Includes/db.php';
session_start();
$current_page = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Halima Seafood Market</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">

    <?php include '../Includes/user_header.php'; ?>

    <!-- HERO SECTION -->
    <header class="relative h-[500px] flex items-center overflow-hidden">
        <div class="absolute inset-0">
            <img src="../Images/3.jpg" alt="About Hero" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">
            <span class="bg-blue-600 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest mb-6 inline-block">Established 2018</span>
            <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-6 max-w-4xl">
                Anchored in Excellence, <br>
                Driven by Freshness.
            </h1>
            <p class="text-gray-200 text-lg font-medium max-w-2xl leading-relaxed">
                From the heart of Mogadishu to your table, our journey is defined by our commitment to the ocean and the community we serve.
            </p>
        </div>
    </header>

    <!-- OUR STORY -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div>
                <span class="text-blue-600 font-black text-xs uppercase tracking-widest mb-4 block">Our Journey</span>
                <h2 class="text-4xl font-black text-gray-900 mb-8">Our Story Since 2018</h2>
                <div class="space-y-6 text-gray-500 leading-relaxed text-sm">
                    <p>
                        Founded in 2018, Halima Seafood Market began with a vision to bring the rich bounty of Somalia's coast to the people of Mogadishu. What started as a small local initiative has grown into a trusted name for premium quality seafood.
                    </p>
                    <p>
                        Over the years, we have built strong relationships with local fishermen and expanded our reach, but our core values remain unchanged. We celebrate the rich maritime heritage of Benadir while embracing modern standards of safety and freshness to serve our community.
                    </p>
                </div>
            </div>
            <div class="relative">
                <div class="absolute -bottom-6 -right-6 h-full w-full bg-gray-100 rounded-[2rem] -z-10"></div>
                <img src="../Images/4.jpg" alt="Our Story" class="rounded-[2rem] shadow-xl w-full h-[400px] object-cover" onerror="this.src='../Images/7.jpg'">
            </div>
        </div>
    </section>

    <!-- CORE VALUES / MISSION -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <span class="text-blue-600 font-black text-xs uppercase tracking-widest mb-4 block">Core Values</span>
            <h2 class="text-4xl font-black text-gray-900 mb-4">Our Mission</h2>
            <p class="text-gray-500 max-w-2xl mx-auto mb-16">
                We are dedicated to revolutionizing the local seafood industry through transparency, quality, and a deep respect for our marine resources.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Value 1 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-lg transition-all group">
                    <div class="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-8 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-medal"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-4">Uncompromising Quality</h3>
                    <p class="text-xs text-gray-500 leading-relaxed px-4">
                        Every piece of seafood is hand-inspected by our experts to meet the highest culinary standards.
                    </p>
                </div>

                <!-- Value 2 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-lg transition-all group">
                    <div class="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-8 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-truck-fast"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-4">Guaranteed Freshness</h3>
                    <p class="text-xs text-gray-500 leading-relaxed px-4">
                        Our logistics network ensures your seafood goes from the ocean to your plate in record time.
                    </p>
                </div>

                <!-- Value 3 -->
                <div class="bg-white p-10 rounded-[2.5rem] shadow-sm hover:shadow-lg transition-all group">
                    <div class="h-16 w-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-2xl mx-auto mb-8 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-4">Sustainable Sourcing</h3>
                    <p class="text-xs text-gray-500 leading-relaxed px-4">
                        We partner exclusively with fishermen who prioritize the long-term health of our Somali waters.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- HERITAGE / OCEAN TO TABLE -->
    <section class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-16 items-center">
            <div class="order-2 md:order-1 relative">
                 <img src="../Images/7.jpg" alt="Fishing Boat" class="rounded-[2rem] shadow-xl w-full h-[350px] object-cover" onerror="this.src='../Images/2.jpg'">
            </div>
            <div class="order-1 md:order-2">
                <span class="text-blue-600 font-black text-xs uppercase tracking-widest mb-4 block">The Halima Way</span>
                <h2 class="text-4xl font-black text-gray-900 mb-8">Our Heritage: Ocean to Table</h2>
                <div class="space-y-6 text-gray-500 leading-relaxed text-sm mb-8">
                    <p>
                        The 'Ocean to Table' philosophy is more than just a motto; it's our heritage. It represents the shortest distance between the catch and your kitchen. By eliminating unnecessary middlemen, we preserve the natural flavors and nutritional value of the seafood.
                    </p>
                </div>
                
                <ul class="space-y-4">
                    <li class="flex items-start gap-4">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-1"></i>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Direct Fleet Access</h4>
                            <p class="text-xs text-gray-400 mt-1">We work directly with local fleets for complete oversight.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-1"></i>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Community Roots</h4>
                            <p class="text-xs text-gray-400 mt-1">Supporting Benadir fishing families for over 6 years.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- SUSTAINABILITY -->
    <section class="py-24 px-6 bg-white">
        <div class="max-w-7xl mx-auto bg-blue-600 rounded-[3rem] p-12 md:p-20 relative overflow-hidden shadow-2xl shadow-blue-200 text-white">
            <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            
            <div class="relative z-10 max-w-3xl">
                <span class="font-black text-xs uppercase tracking-widest mb-6 block text-blue-200">Environment First</span>
                <h2 class="text-4xl md:text-5xl font-black mb-6">Sustainability Commitment</h2>
                <p class="text-blue-100 text-lg font-medium leading-relaxed mb-10">
                    We recognize that our livelihood depends on the ocean's health. Halima Seafood Market is committed to sustainable sourcing. This means zero-waste processing, supporting seasonal fishing to allow species regeneration, and using eco-friendly packaging for all our retail and wholesale deliveries.
                </p>
                <div class="flex flex-wrap gap-4">
                    <span class="bg-white/20 backdrop-blur-md px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider border border-white/10">Sustainable Sourcing</span>
                    <span class="bg-white/20 backdrop-blur-md px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider border border-white/10">Eco-Friendly</span>
                    <span class="bg-white/20 backdrop-blur-md px-6 py-3 rounded-xl text-xs font-bold uppercase tracking-wider border border-white/10">Traceable Catch</span>
                </div>
            </div>
        </div>
    </section>

    <!-- VISIT US -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white rounded-[3rem] p-10 shadow-sm border border-gray-100 flex flex-col md:flex-row gap-10">
                <div class="flex-1 flex flex-col justify-center p-6">
                    <h2 class="text-3xl font-black text-gray-900 mb-6">Visit Our Market</h2>
                    <p class="text-gray-500 text-sm mb-10 leading-relaxed">
                        Experience the freshness firsthand at our location in Benadir. Our experts are ready to help you select the perfect catch.
                    </p>
                    
                    <ul class="space-y-6">
                        <li class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center"><i class="fa-solid fa-location-dot"></i></div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Location</p>
                                <p class="text-sm font-bold text-gray-900">Mogadishu, Benadir, Somalia</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center"><i class="fa-solid fa-phone"></i></div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Phone</p>
                                <p class="text-sm font-bold text-gray-900">+252 61 9896704</p>
                            </div>
                        </li>
                        <li class="flex items-center gap-4">
                            <div class="h-10 w-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center"><i class="fa-solid fa-envelope"></i></div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Email</p>
                                <p class="text-sm font-bold text-gray-900">info@halimaseafood.com</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="flex-1 h-[400px] bg-gray-200 rounded-[2rem] overflow-hidden relative group">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15955.337286367373!2d45.3341!3d2.0398!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1837c7e53f1f7d53%3A0x6b8d7b3b3b3b3b3b!2sMogadishu%2C%20Banadir%2C%20Somalia!5e0!3m2!1sen!2sus!4v1625585000000!5m2!1sen!2sus" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <?php include '../Includes/user_footer.php'; ?>
</body>
</html>
