<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CoolCafe - QRIS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f5f2;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-[#634832] text-white p-6 text-center">
            <i class="fas fa-qrcode text-4xl mb-3"></i>
            <h1 class="text-2xl font-bold">Pembayaran QRIS</h1>
            <p class="text-sm text-white/75">Demo QR, bukan pembayaran real.</p>
        </div>

        <div class="p-6">
            <div class="flex justify-between items-center bg-gray-50 rounded-2xl p-4 mb-6">
                <div>
                    <p class="text-xs text-gray-400">Meja</p>
                    <p class="font-bold text-gray-800">{{ $table }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400">Total</p>
                    <p class="font-bold text-[#634832]">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="mx-auto w-72 max-w-full aspect-square bg-white border-8 border-gray-100 rounded-3xl p-4 shadow-inner">
                <div class="grid grid-cols-9 gap-1 h-full">
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
                        <span class="{{ $cell ? 'bg-gray-950' : 'bg-white' }} rounded-sm"></span>
                    @endforeach
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 mt-5">
                Tunjukkan layar ini ke kasir setelah scan.
            </p>

            <div class="mt-6">
                <a href="/menu" class="block text-center bg-[#634832] text-white py-3 rounded-2xl font-bold text-sm">
                    Pesan Lagi
                </a>
            </div>
        </div>
    </main>
</body>
</html>
