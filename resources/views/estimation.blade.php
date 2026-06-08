<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CoolCafe - Estimasi Pesanan</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* CSS animated coffee cup steam styling */
        .steam-container {
            display: flex;
            justify-content: center;
            gap: 6px;
            margin-bottom: 2px;
            height: 30px;
        }
        .steam-line {
            width: 3px;
            height: 20px;
            background: rgba(111, 71, 39, 0.4);
            border-radius: 50%;
        }
        .coffee-cup {
            position: relative;
            width: 60px;
            height: 48px;
            background: linear-gradient(135deg, #6f4727 0%, #58351b 100%);
            border-radius: 0 0 24px 24px;
            border-top: 2px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(111, 71, 39, 0.15);
            margin: 0 auto;
        }
        .cup-handle {
            position: absolute;
            right: -10px;
            top: 10px;
            width: 14px;
            height: 24px;
            border: 3.5px solid #6f4727;
            border-left: 0;
            border-radius: 0 10px 10px 0;
        }
        .saucer {
            width: 80px;
            height: 5px;
            background: #ab8158;
            border-radius: 50%;
            margin: 4px auto 0;
            box-shadow: 0 2px 4px rgba(111, 71, 39, 0.1);
        }
    </style>
</head>
<body class="min-h-screen bg-coffee-50 flex items-center justify-center p-6 text-center antialiased">
    <div class="max-w-md w-full space-y-8">
        <!-- Interactive Barista Steam Loader -->
        <div class="flex flex-col items-center justify-center">
            <div class="steam-container">
                <div class="steam-line animate-steam-1"></div>
                <div class="steam-line animate-steam-2"></div>
                <div class="steam-line animate-steam-3"></div>
            </div>
            <div class="coffee-cup">
                <div class="cup-handle"></div>
            </div>
            <div class="saucer"></div>
        </div>
        
        <div class="space-y-2">
            <h1 class="font-serif text-3xl font-bold text-coffee-950">Pesanan Diterima!</h1>
            <p class="text-xs text-coffee-700 max-w-sm mx-auto leading-relaxed">Terima kasih atas pesanan Anda. Tim barista dan chef kami sedang menyiapkan sajian terbaik untuk Anda.</p>
        </div>
        
        <!-- Live Status Stepper Card -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-coffee-950/5 text-left space-y-6">
            <!-- Table & Time Info -->
            <div class="flex justify-between items-center pb-4 border-b border-coffee-100">
                <div>
                    <span class="text-[9px] uppercase tracking-wider text-coffee-600 font-bold block">Nomor Meja</span>
                    <span class="text-lg font-bold text-coffee-950">Meja {{ $table }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] uppercase tracking-wider text-coffee-600 font-bold block">Estimasi Tunggu</span>
                    <span class="text-lg font-serif font-bold text-coffee-800">10 - 15 Menit</span>
                </div>
            </div>
            
            <!-- Process steps -->
            <div class="relative pl-8 space-y-6 before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-[2px] before:bg-coffee-100">
                <!-- Step 1: Active/Done -->
                <div class="relative flex items-start gap-4">
                    <div class="absolute -left-8 w-7 h-7 rounded-full bg-emerald-50 border border-emerald-250 text-emerald-500 flex items-center justify-center text-[10px] z-10">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-coffee-950">Pesanan Masuk</h3>
                        <p class="text-[10px] text-coffee-600">Pesanan telah tercatat di kasir & printer dapur.</p>
                    </div>
                </div>
                
                <!-- Step 2: Preparing -->
                <div class="relative flex items-start gap-4">
                    <div class="absolute -left-8 w-7 h-7 rounded-full bg-coffee-100 border border-coffee-250 text-coffee-700 flex items-center justify-center text-[10px] z-10 animate-pulse">
                        <i class="fas fa-fire-burner"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-coffee-950">Sedang Disiapkan</h3>
                        <p class="text-[10px] text-coffee-600">Bahan segar sedang diproses untuk pesanan Anda.</p>
                    </div>
                </div>
                
                <!-- Step 3: Serve -->
                <div class="relative flex items-start gap-4 opacity-40">
                    <div class="absolute -left-8 w-7 h-7 rounded-full bg-coffee-50/50 border border-coffee-100 text-coffee-400 flex items-center justify-center text-[10px] z-10">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-coffee-950">Siap Dihidangkan</h3>
                        <p class="text-[10px] text-coffee-600">Pelayan kami akan mengantarkan pesanan langsung ke meja.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pt-4 space-y-8">
            <a href="/menu" class="inline-flex items-center gap-2 text-xs font-bold text-coffee-700 hover:text-coffee-900 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Menu Utama</span>
            </a>
            
            <div>
                <p class="text-[9px] text-coffee-400 uppercase tracking-widest font-bold">Terima kasih atas kunjungan Anda</p>
                <p class="font-serif text-sm font-bold text-coffee-700 mt-0.5">CoolCafe</p>
            </div>
        </div>
    </div>
</body>
</html>
