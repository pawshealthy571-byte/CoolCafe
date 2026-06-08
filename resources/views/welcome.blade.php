<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CoolCafe - Tempat Santai & Roti Segar</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen flex flex-col bg-coffee-50">
    <!-- Navbar -->
    <header class="w-full py-5 px-6 md:px-12 bg-white/40 backdrop-blur-md sticky top-0 z-50 border-b border-coffee-950/5">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <!-- Brand -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-10 h-10 bg-coffee-700 text-white rounded-2xl flex items-center justify-center shadow-md shadow-coffee-700/10 transition-transform group-hover:rotate-12">
                    <i class="fas fa-coffee text-lg"></i>
                </div>
                <div>
                    <span class="font-serif text-2xl font-bold tracking-tight text-coffee-950">CoolCafe</span>
                    <p class="text-[9px] uppercase tracking-widest text-coffee-600 font-bold -mt-1">Café & Bakery</p>
                </div>
            </a>
            
            <!-- Nav Links -->
            <nav class="flex items-center gap-4">
                <a href="/menu" class="hidden sm:inline-flex items-center gap-2 font-semibold text-sm text-coffee-800 hover:text-coffee-950 transition-colors">
                    Menu Kami
                </a>
                <a href="/menu" class="inline-flex items-center gap-2 px-5 py-2.5 bg-coffee-700 hover:bg-coffee-800 text-white rounded-xl text-xs font-bold transition-all shadow-md shadow-coffee-700/10 active:scale-95">
                    <i class="fas fa-mobile-screen-button"></i>
                    <span>Pesan Digital</span>
                </a>
                @auth
                    <a href="/dashboard" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/80 hover:bg-white text-coffee-850 rounded-xl text-xs font-bold border border-coffee-200 transition-all">
                        Dashboard
                    </a>
                @else
                    <a href="/login" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white/80 hover:bg-white text-coffee-850 rounded-xl text-xs font-bold border border-coffee-250 transition-all">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Staff Login</span>
                    </a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="w-full py-16 md:py-24 px-6 md:px-12 max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <div class="lg:col-span-6 space-y-6">
            <span class="inline-block px-3 py-1 bg-coffee-100 text-coffee-850 rounded-full text-xs font-bold tracking-wide uppercase">
                ☕ Pembuat Kopi & Roti Tradisional
            </span>
            <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-coffee-950 leading-tight">
                Kehangatan Kopi & <span class="italic text-coffee-600">Kelembutan Roti</span> yang Fresh Setiap Hari
            </h1>
            <p class="text-coffee-800 text-base sm:text-lg leading-relaxed max-w-xl">
                Nikmati perpaduan biji kopi pilihan dari petani lokal dan roti artisan panggang segar dari dapur kami. Dibuat dengan tangan penuh dedikasi untuk setiap cangkir Anda.
            </p>
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <a href="/menu" class="px-8 py-4 bg-coffee-700 hover:bg-coffee-800 text-white font-bold rounded-2xl transition-all shadow-lg shadow-coffee-700/15 text-sm active:scale-95 flex items-center gap-3">
                    <span>Mulai Pesan Sekarang</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
                <a href="#about" class="px-6 py-4 bg-white/60 hover:bg-white/90 text-coffee-900 border border-coffee-200 font-bold rounded-2xl transition-all text-sm">
                    Kenali Kami
                </a>
            </div>
        </div>
        
        <div class="lg:col-span-6 relative">
            <div class="absolute -inset-4 bg-coffee-300/20 rounded-[2.5rem] blur-2xl transform rotate-2"></div>
            <div class="relative rounded-[2rem] overflow-hidden shadow-2xl border-4 border-white aspect-[4/3] sm:aspect-video lg:aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?auto=format&fit=crop&w=800&q=80" alt="Cozy Cafe Interior" class="w-full h-full object-cover">
            </div>
        </div>
    </section>

    <!-- Highlights / Features -->
    <section id="about" class="w-full bg-white/50 border-y border-coffee-950/5 py-16 px-6 md:px-12">
        <div class="max-w-7xl mx-auto">
            <div class="text-center max-w-xl mx-auto mb-16 space-y-4">
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-coffee-950">Kenapa Memilih CoolCafe?</h2>
                <p class="text-sm text-coffee-750">Kami percaya setiap suap roti dan teguk kopi memiliki cerita kehangatannya sendiri.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="bg-white rounded-3xl p-8 border border-coffee-100 shadow-sm space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-coffee-50 text-coffee-700 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-mug-hot"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Biji Kopi Pilihan</h3>
                    <p class="text-sm text-coffee-700 leading-relaxed">Biji kopi arabika pilihan dari petani nusantara yang disangrai dengan presisi tinggi oleh barista kami.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white rounded-3xl p-8 border border-coffee-100 shadow-sm space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-coffee-50 text-coffee-700 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-wheat-awn"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Artisan Bakery</h3>
                    <p class="text-sm text-coffee-700 leading-relaxed">Roti dan pastry buatan tangan, dipanggang dari dapur kami setiap pagi tanpa bahan pengawet buatan.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white rounded-3xl p-8 border border-coffee-100 shadow-sm space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-coffee-50 text-coffee-700 flex items-center justify-center text-xl shadow-inner">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Suasana Cozy</h3>
                    <p class="text-sm text-coffee-700 leading-relaxed">Didesain dengan pencahayaan hangat, musik jazz lembut, serta wangi kopi segar untuk produktivitas Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="w-full py-20 px-6 md:px-12 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-16 gap-4">
            <div class="space-y-3">
                <span class="text-xs uppercase tracking-widest text-coffee-600 font-bold">Rekomendasi Chef</span>
                <h2 class="font-serif text-3xl md:text-4xl font-bold text-coffee-950">Menu Favorit Minggu Ini</h2>
            </div>
            <a href="/menu" class="font-bold text-sm text-coffee-700 hover:text-coffee-900 flex items-center gap-2 group">
                <span>Lihat Seluruh Menu</span>
                <i class="fas fa-arrow-right text-xs transition-transform group-hover:translate-x-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Product 1 -->
            <div class="bg-white rounded-[2rem] border border-coffee-100 overflow-hidden shadow-sm group">
                <div class="aspect-square overflow-hidden bg-gray-100 relative">
                    <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=600&q=80" alt="Butter Croissant" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-[10px] font-bold text-coffee-900 border border-coffee-100">Bakery</span>
                </div>
                <div class="p-6 space-y-3">
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Butter Croissant</h3>
                    <p class="text-xs text-coffee-700 leading-relaxed">Roti mentega klasik khas Prancis dengan tekstur renyah di luar, namun berongga dan sangat lembut di dalam.</p>
                    <div class="flex justify-between items-center pt-2">
                        <span class="font-bold text-coffee-800">Rp 22.000</span>
                        <a href="/menu" class="w-9 h-9 bg-coffee-50 text-coffee-700 rounded-xl flex items-center justify-center text-sm hover:bg-coffee-700 hover:text-white transition-all"><i class="fas fa-plus"></i></a>
                    </div>
                </div>
            </div>
            <!-- Product 2 -->
            <div class="bg-white rounded-[2rem] border border-coffee-100 overflow-hidden shadow-sm group">
                <div class="aspect-square overflow-hidden bg-gray-100 relative">
                    <img src="https://images.unsplash.com/photo-1507133750040-4a8f57021571?auto=format&fit=crop&w=600&q=80" alt="Signature Cafe Latte" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-[10px] font-bold text-coffee-900 border border-coffee-100">Kopi</span>
                </div>
                <div class="p-6 space-y-3">
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Signature Cafe Latte</h3>
                    <p class="text-xs text-coffee-700 leading-relaxed">Satu shot espresso premium dipadukan dengan susu steam yang lembut dan gurih serta sentuhan latte art barista.</p>
                    <div class="flex justify-between items-center pt-2">
                        <span class="font-bold text-coffee-800">Rp 32.000</span>
                        <a href="/menu" class="w-9 h-9 bg-coffee-50 text-coffee-700 rounded-xl flex items-center justify-center text-sm hover:bg-coffee-700 hover:text-white transition-all"><i class="fas fa-plus"></i></a>
                    </div>
                </div>
            </div>
            <!-- Product 3 -->
            <div class="bg-white rounded-[2rem] border border-coffee-100 overflow-hidden shadow-sm group">
                <div class="aspect-square overflow-hidden bg-gray-100 relative">
                    <img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=600&q=80" alt="Healthy Chicken Bowl" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    <span class="absolute top-4 right-4 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-[10px] font-bold text-coffee-900 border border-coffee-100">Main Course</span>
                </div>
                <div class="p-6 space-y-3">
                    <h3 class="font-serif text-xl font-bold text-coffee-950">Healthy Chicken Bowl</h3>
                    <p class="text-xs text-coffee-700 leading-relaxed">Nasi coklat sehat dilengkapi dada ayam panggang empuk, alpukat segar, brokoli, dan saus racikan spesial CoolCafe.</p>
                    <div class="flex justify-between items-center pt-2">
                        <span class="font-bold text-coffee-800">Rp 45.000</span>
                        <a href="/menu" class="w-9 h-9 bg-coffee-50 text-coffee-700 rounded-xl flex items-center justify-center text-sm hover:bg-coffee-700 hover:text-white transition-all"><i class="fas fa-plus"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Info Banner (Hours & Map) -->
    <section class="w-full bg-[#f4ebd9] py-16 px-6 md:px-12 border-t border-coffee-200">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="space-y-6">
                <h3 class="font-serif text-3xl font-bold text-coffee-950">Kunjungi Outlet Kami</h3>
                <p class="text-coffee-900 leading-relaxed text-sm">
                    Kami berlokasi di pusat kota yang tenang, sangat cocok untuk bekerja jarak jauh (WFC), berkumpul dengan keluarga, atau sekadar menikmati me-time di sore hari.
                </p>
                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl text-coffee-700 flex items-center justify-center flex-shrink-0"><i class="fas fa-location-dot"></i></div>
                        <div>
                            <p class="font-bold text-sm text-coffee-950">Alamat</p>
                            <p class="text-xs text-coffee-800">Jl. Senopati Raya No. 42, Kebayoran Baru, Jakarta Selatan</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-white rounded-xl text-coffee-700 flex items-center justify-center flex-shrink-0"><i class="fas fa-clock"></i></div>
                        <div>
                            <p class="font-bold text-sm text-coffee-950">Jam Operasional</p>
                            <p class="text-xs text-coffee-800">Senin - Minggu | 07:00 WIB - 22:00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="rounded-3xl overflow-hidden border-4 border-white shadow-xl aspect-video relative">
                <!-- Nice decorative mockup map using a styled div instead of full map API -->
                <div class="absolute inset-0 bg-[#ebe3d5] flex flex-col justify-center items-center p-8 text-center space-y-4">
                    <div class="w-14 h-14 bg-coffee-700 text-white rounded-full flex items-center justify-center text-xl shadow-lg animate-bounce">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div>
                        <h4 class="font-serif text-lg font-bold text-coffee-950">CoolCafe Senopati</h4>
                        <p class="text-xs text-coffee-800 mt-1">Dapatkan rute navigasi Google Maps</p>
                    </div>
                    <a href="https://maps.google.com" target="_blank" class="px-5 py-2.5 bg-white text-coffee-900 hover:bg-coffee-50 font-bold rounded-xl text-xs transition-all shadow-sm">
                        Buka di Maps
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="w-full py-8 px-6 bg-coffee-950 text-white/60 text-center border-t border-white/5 mt-auto">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 text-xs">
            <p>&copy; 2026 CoolCafe. All Rights Reserved. Kebersamaan dalam setiap seduhan.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-white"><i class="fab fa-instagram text-base"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-facebook text-base"></i></a>
                <a href="#" class="hover:text-white"><i class="fab fa-whatsapp text-base"></i></a>
            </div>
        </div>
    </footer>
</body>
</html>
