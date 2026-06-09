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
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
    <div class="card hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-receipt text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Pesanan Aktif</p>
                <p class="text-xl 2xl:text-2xl font-bold text-gray-800 truncate">{{ $activeOrders }}</p>
            </div>
        </div>
    </div>

    <div class="card hover:shadow-md transition-shadow border-l-4 border-l-green-500">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Omzet Hari Ini</p>
                <p class="text-xl 2xl:text-2xl font-bold text-coffee truncate" title="Rp {{ number_format($todayRevenue, 0, ',', '.') }}">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card hover:shadow-md transition-shadow">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-exchange-alt text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Transaksi</p>
                <p class="text-xl 2xl:text-2xl font-bold text-gray-800 truncate">{{ $todayTransactions }}</p>
            </div>
        </div>
    </div>

    <div class="card hover:shadow-md transition-shadow border-l-4 border-l-coffee">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-[#6348321a] text-coffee rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-coins text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Omzet</p>
                <p class="text-xl 2xl:text-2xl font-bold text-coffee truncate" title="Rp {{ number_format($totalRevenue, 0, ',', '.') }}">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    <div class="card hover:shadow-md transition-shadow border-l-4 border-l-red-500">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center shrink-0">
                <i class="fas fa-arrow-down text-xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest truncate">Total Pengeluaran</p>
                <p class="text-xl 2xl:text-2xl font-bold text-red-600 truncate" title="Rp {{ number_format($totalExpenses, 0, ',', '.') }}">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
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

<section class="card mt-8">
    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-history text-coffee"></i> Riwayat Aktivitas Sistem</h3>
    <div id="activity-logs-list" class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
        <p class="text-xs text-gray-400 text-center py-4">Memuat riwayat aktivitas...</p>
    </div>
</section>

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

        // Load Activity Logs
        try {
            const logsResp = await fetch('/admin/activity-logs');
            const logs = await logsResp.json();
            const logContainer = document.getElementById('activity-logs-list');
            if (logs.length > 0) {
                logContainer.innerHTML = logs.map(log => `
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm shrink-0 mt-1">
                            <i class="fas fa-user-circle text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-800"><b>${log.user ? log.user.name : 'Sistem'}</b> <span class="text-gray-500">${log.description}</span></p>
                            <p class="text-[9px] text-gray-400 mt-0.5">${new Date(log.created_at).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                `).join('');
            } else {
                logContainer.innerHTML = '<p class="text-xs text-gray-400 text-center py-4">Belum ada aktivitas tercatat.</p>';
            }
        } catch (error) {
            console.error('Failed to load activity logs:', error);
        }
    }
    initDashboard();
</script>
@endpush
</div>
@endsection