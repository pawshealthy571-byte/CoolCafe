@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Ringkasan Operasional</h2>
        <p class="text-gray-500 text-sm">Pantau performa CoolCafe hari ini secara real-time.</p>
    </div>
</div>

<div id="stats-section">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="card">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pesanan Aktif</p>
                <p class="text-2xl font-bold text-gray-800">{{ $activeOrders }}</p>
            </div>
        </div>
    </div>

    <div class="card border-l-4 border-l-green-500">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Omzet Hari Ini</p>
                <p class="text-2xl font-bold text-coffee">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center">
                <i class="fas fa-exchange-alt text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transaksi</p>
                <p class="text-2xl font-bold text-gray-800">{{ $todayTransactions }}</p>
            </div>
        </div>
    </div>

    <div class="card border-l-4 border-l-coffee">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[#6348321a] text-coffee rounded-2xl flex items-center justify-center">
                <i class="fas fa-coins text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Omzet</p>
                <p class="text-2xl font-bold text-coffee">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <section class="lg:col-span-2">
        <div class="card !p-0 overflow-hidden">
            <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">Akses Cepat Dashboard</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Menu kerja sesuai role akun Anda.</p>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @if (in_array($role, ['manager', 'admin', 'superadmin'], true))
                    <div class="group border border-gray-100 rounded-2xl p-5 hover:border-coffee/30 hover:bg-coffee/[0.02] transition-all cursor-pointer">
                        <div class="w-12 h-12 bg-coffee/10 text-coffee rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Laporan Manager</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">Pantau omzet, transaksi, dan item terlaris dengan detail mendalam.</p>
                    </div>
                @endif

                @if (in_array($role, ['admin', 'superadmin'], true))
                    <div class="group border border-gray-100 rounded-2xl p-5 hover:border-coffee/30 hover:bg-coffee/[0.02] transition-all cursor-pointer">
                        <div class="w-12 h-12 bg-coffee/10 text-coffee rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-sliders text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Manajemen Toko</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">Area operasional untuk pengaturan data toko, menu, dan staff.</p>
                    </div>
                @endif

                @if ($role === 'superadmin')
                    <div class="group border border-gray-100 rounded-2xl p-5 hover:border-coffee/30 hover:bg-coffee/[0.02] transition-all cursor-pointer md:col-span-2">
                        <div class="w-12 h-12 bg-coffee/10 text-coffee rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-shield-halved text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Sistem Superadmin</h3>
                        <p class="text-xs text-gray-500 mt-2 leading-relaxed">Akses penuh ke semua kontrol sistem, role, dan backup data keamanan.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section>
        <div class="card !p-0 overflow-hidden">
            <div class="p-5 border-b border-gray-50">
                <h3 class="font-bold text-gray-800">Daftar Staff</h3>
                <p class="text-xs text-gray-500 mt-0.5">Akun demo staff yang tersedia.</p>
            </div>
            <div class="divide-y divide-gray-50 max-h-[400px] overflow-y-auto">
                @foreach ($users as $staff)
                    <div class="p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors">
                        <div class="w-10 h-10 rounded-full bg-coffee/10 flex items-center justify-center text-coffee font-bold text-sm">
                            {{ strtoupper(substr($staff->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-xs text-gray-800">{{ $staff->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $staff->email }}</p>
                        </div>
                        <span class="text-[8px] uppercase tracking-widest px-2 py-1 rounded-lg font-bold
                            @if($staff->role === 'superadmin') bg-purple-100 text-purple-600
                            @elseif($staff->role === 'admin') bg-blue-100 text-blue-600
                            @elseif($staff->role === 'manager') bg-green-100 text-green-600
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ $staff->role }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
</div>
@endsection
