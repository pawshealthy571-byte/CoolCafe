<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CoolCafe - Pembayaran QRIS</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-coffee-50 flex items-center justify-center p-4 antialiased">
    <main class="w-full max-w-md bg-white rounded-[2.5rem] shadow-xl border border-coffee-950/5 overflow-hidden flex flex-col relative">
        <!-- Top Banner -->
        <div class="bg-coffee-900 text-white p-6 text-center relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
            <div class="absolute -left-6 -bottom-6 w-20 h-20 bg-white/5 rounded-full blur-xl"></div>
            
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner">
                <i class="fas fa-qrcode text-2xl text-coffee-100"></i>
            </div>
            <h1 class="font-serif text-xl font-bold">Pembayaran QRIS</h1>
            <p class="text-[9px] uppercase tracking-widest text-coffee-200/80 font-bold mt-0.5">Metode Scan Digital</p>
        </div>

        <div class="p-6 space-y-6">
            <!-- Receipt Info Card -->
            <div class="bg-coffee-50/50 border border-coffee-100 rounded-2xl p-4 flex justify-between items-center relative">
                <!-- Receipt details -->
                <div>
                    <span class="text-[9px] uppercase tracking-wider text-coffee-600 font-bold block">Nomor Meja</span>
                    <span class="text-base font-bold text-coffee-950">Meja {{ $table }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[9px] uppercase tracking-wider text-coffee-600 font-bold block">Total Bayar</span>
                    <span class="text-base font-serif font-bold text-coffee-800">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- QR Code container with laser animation -->
            <div class="relative mx-auto w-64 max-w-full aspect-square bg-white border border-coffee-100/70 rounded-3xl p-5 shadow-sm flex items-center justify-center overflow-hidden">
                <!-- Glowing red scanner laser line -->
                <div class="absolute left-0 right-0 h-0.5 bg-red-500 shadow-[0_0_10px_#ef4444] animate-scan z-10"></div>
                
                <!-- Mock QR Cells Grid -->
                <div class="grid grid-cols-9 gap-1 w-full h-full opacity-90">
                    @php
                        $qrCells = [
                            1,1,1,1,1,1,1,0,1,
                            1,0,0,0,0,0,1,1,0,
                            1,0,1,1,1,0,1,0,1,
                            1,0,1,1,1,0,1,1,0,
                            1,0,1,1,1,0,1,0,1,
                            1,0,0,0,0,0,1,1,1,
                            1,1,1,1,1,1,1,0,0,
                            0,1,0,1,0,1,0,1,1,
                            1,0,1,0,1,1,0,1,0,
                            0,1,1,1,0,0,1,0,1,
                            1,0,0,1,1,0,1,1,0,
                            0,1,0,0,1,1,0,0,1,
                            1,1,1,0,1,0,1,1,1,
                            1,0,1,1,0,1,0,0,1,
                            1,1,0,0,1,1,1,0,0,
                            0,1,1,0,0,1,0,1,1,
                            1,0,1,1,1,0,1,0,1,
                            0,1,0,1,0,1,1,1,0,
                        ];
                    @endphp

                    @foreach ($qrCells as $cell)
                        <span class="{{ $cell ? 'bg-coffee-950' : 'bg-white' }} rounded-[3px] transition-colors duration-300"></span>
                    @endforeach
                </div>
            </div>

            <!-- Hint text -->
            <div class="text-center space-y-1">
                <p class="text-xs font-bold text-coffee-800">Scan kode QR di atas untuk menyelesaikan pesanan Anda.</p>
                <p class="text-[10px] text-coffee-500">Struk digital ini akan otomatis diproses setelah pembayaran kasir diverifikasi.</p>
            </div>

            <!-- Action Buttons -->
            <div class="space-y-3 pt-2">
                <button onclick="window.location.href='/estimation?table={{ $table }}'" class="w-full bg-coffee text-white hover:bg-coffee-dark py-3.5 rounded-2xl font-bold text-xs shadow-md shadow-coffee-700/10 active:scale-98 transition-all cursor-pointer flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesai Pembayaran</span>
                </button>
                <a href="/menu" class="block text-center text-[10px] font-bold text-coffee-650 hover:text-coffee-950 uppercase tracking-widest transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Pesan Menu Lainnya
                </a>
            </div>
        </div>
    </main>
</body>
</html>
