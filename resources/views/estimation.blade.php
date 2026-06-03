<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CoolCafe - Estimasi Pesanan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f5f2;
        }
        .loader-ring {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 80px;
        }
        .loader-ring div {
            box-sizing: border-box;
            display: block;
            position: absolute;
            width: 64px;
            height: 64px;
            margin: 8px;
            border: 8px solid #634832;
            border-radius: 50%;
            animation: loader-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
            border-color: #634832 transparent transparent transparent;
        }
        .loader-ring div:nth-child(1) { animation-delay: -0.45s; }
        .loader-ring div:nth-child(2) { animation-delay: -0.3s; }
        .loader-ring div:nth-child(3) { animation-delay: -0.15s; }
        @keyframes loader-ring {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-center">
    <div class="max-w-md w-full">
        <div class="mb-8">
            <div class="loader-ring"><div></div><div></div><div></div><div></div></div>
        </div>
        
        <h1 class="text-2xl font-bold text-[#634832] mb-2">Pesanan Diterima!</h1>
        <p class="text-gray-500 mb-8">Mohon tunggu sebentar, dapur kami sedang menyiapkan pesanan terbaik untuk Anda.</p>
        
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div class="text-left">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Meja</p>
                    <p class="text-xl font-bold text-gray-800">{{ $table }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Waktu Estimasi</p>
                    <p class="text-xl font-bold text-[#634832]">10 - 15 Menit</p>
                </div>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center gap-4 text-left">
                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Pesanan Masuk</p>
                        <p class="text-xs text-gray-400">Sudah terkirim ke sistem kasir</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-left">
                    <div class="w-10 h-10 rounded-full bg-[#634832]/10 text-[#634832] flex items-center justify-center flex-shrink-0 animate-pulse">
                        <i class="fas fa-fire-burner"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Sedang Disiapkan</p>
                        <p class="text-xs text-gray-400">Chef sedang memproses pesanan Anda</p>
                    </div>
                </div>
                <div class="flex items-center gap-4 text-left opacity-40">
                    <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">Siap Dihidangkan</p>
                        <p class="text-xs text-gray-400">Pesanan akan segera diantar ke meja</p>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="/menu" class="inline-block text-sm font-bold text-[#634832] hover:underline">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Menu
        </a>
        
        <div class="mt-12">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest">Terima kasih telah berkunjung ke</p>
            <p class="text-sm font-bold text-[#634832] mt-1">CoolCafe</p>
        </div>
    </div>
</body>
</html>
