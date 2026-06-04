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
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
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

    <div class="card border-l-4 border-l-red-500">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center">
                <i class="fas fa-arrow-down text-xl"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Pengeluaran</p>
                <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <section class="card">
        <h3 class="font-bold text-gray-800 mb-4">Grafik Omzet Mingguan</h3>
        <canvas id="revenueChart"></canvas>
    </section>
    <section class="card">
        <h3 class="font-bold text-gray-800 mb-4">Stok Bahan Baku Rendah</h3>
        <div id="low-stock-list" class="space-y-2">
            <!-- Low stock items -->
        </div>
    </section>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize charts and load data
    async function initDashboard() {
        // Mock chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                datasets: [{
                    label: 'Omzet',
                    data: [120000, 190000, 300000, 500000, 200000, 300000, 400000],
                    borderColor: '#634832',
                    tension: 0.4
                }]
            }
        });

        // Load low stock
        const resp = await fetch('/admin/ingredients');
        const ingredients = await resp.json();
        const lowStock = ingredients.filter(i => i.stock < 10);
        const list = document.getElementById('low-stock-list');
        list.innerHTML = lowStock.length ? lowStock.map(i => `<div class="p-2 bg-red-50 text-red-600 rounded text-xs font-bold">${i.name}: ${i.stock} ${i.unit}</div>`).join('') : '<p class="text-xs text-gray-400">Semua stok aman.</p>';
    }
    initDashboard();
</script>
@endpush
</div>
@endsection
