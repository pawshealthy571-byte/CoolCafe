<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - Dashboard {{ ucfirst($role) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>
<body class="min-h-screen">
    <nav class="bg-[#634832] text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <p class="text-xs text-white/70">Login sebagai {{ $user->name }}</p>
                <h1 class="text-xl font-bold">Dashboard {{ ucfirst($role) }}</h1>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="/menu" class="bg-white/15 hover:bg-white/25 px-4 py-2 rounded-xl text-sm font-bold">
                    <i class="fas fa-utensils mr-2"></i>Menu
                </a>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-xl text-sm font-bold">
                        <i class="fas fa-right-from-bracket mr-2"></i>Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4 md:p-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pesanan Aktif</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">{{ $activeOrders }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Omzet Hari Ini</p>
                <p class="text-2xl font-bold text-[#634832] mt-2">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Transaksi Hari Ini</p>
                <p class="text-2xl font-bold text-gray-800 mt-2">{{ $todayTransactions }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omzet</p>
                <p class="text-2xl font-bold text-[#634832] mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <section class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b">
                    <h2 class="font-bold text-gray-800">Akses Dashboard</h2>
                    <p class="text-sm text-gray-500 mt-1">Menu kerja sesuai role akun.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-5">
                    @if (in_array($role, ['manager', 'admin', 'superadmin'], true))
                        <div class="border border-gray-100 rounded-2xl p-5">
                            <i class="fas fa-chart-line text-[#634832] text-2xl mb-3"></i>
                            <h3 class="font-bold text-gray-800">Laporan Manager</h3>
                            <p class="text-sm text-gray-500 mt-1">Pantau omzet, transaksi, dan item terlaris.</p>
                        </div>
                    @endif

                    @if (in_array($role, ['admin', 'superadmin'], true))
                        <div class="border border-gray-100 rounded-2xl p-5">
                            <i class="fas fa-gear text-[#634832] text-2xl mb-3"></i>
                            <h3 class="font-bold text-gray-800">Dashboard Admin</h3>
                            <p class="text-sm text-gray-500 mt-1">Area operasional admin untuk data toko dan staff.</p>
                        </div>
                    @endif

                    @if ($role === 'superadmin')
                        <div class="border border-gray-100 rounded-2xl p-5">
                            <i class="fas fa-user-shield text-[#634832] text-2xl mb-3"></i>
                            <h3 class="font-bold text-gray-800">Dashboard Superadmin</h3>
                            <p class="text-sm text-gray-500 mt-1">Akses penuh ke semua role dan data sistem.</p>
                        </div>
                    @endif
                </div>
            </section>

            <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b">
                    <h2 class="font-bold text-gray-800">Staff</h2>
                    <p class="text-sm text-gray-500 mt-1">Akun demo yang tersedia.</p>
                </div>
                <div class="divide-y divide-gray-100">
                    @foreach ($users as $staff)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-sm text-gray-800">{{ $staff->name }}</p>
                                <p class="text-xs text-gray-400">{{ $staff->email }}</p>
                            </div>
                            <span class="text-[10px] uppercase tracking-wider bg-gray-100 text-gray-600 px-3 py-1 rounded-full font-bold">
                                {{ $staff->role }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </main>
</body>
</html>
