@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div id="reports-section">
    <!-- Financial Reports Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h2>
            <p class="text-gray-500 text-sm">Pantau arus kas dan performa penjualan secara mendalam.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="exportData('print')" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">
                <i class="fas fa-print mr-2"></i>Cetak
            </button>
            <button onclick="exportData('pdf')" class="bg-white border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-bold hover:bg-gray-50 transition-all">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </button>
            <button onclick="exportData('excel')" class="bg-green-50 text-green-600 border border-green-100 px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-100 transition-all">
                <i class="fas fa-file-excel mr-2"></i>Excel
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card flex flex-col justify-between border-none shadow-md hover:shadow-lg transition-all border-b-4 !border-b-blue-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <span class="text-[10px] font-bold text-blue-500 uppercase tracking-widest bg-blue-50 px-2 py-1 rounded-lg">Harian</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Omzet Hari Ini</p>
                <h3 id="report-daily-revenue" class="text-2xl font-bold text-gray-800">Rp 0</h3>
                <p id="report-daily-count" class="text-xs text-gray-500 mt-1">0 Transaksi</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between border-none shadow-md hover:shadow-lg transition-all border-b-4 !border-b-indigo-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <span class="text-[10px] font-bold text-indigo-500 uppercase tracking-widest bg-indigo-50 px-2 py-1 rounded-lg">Mingguan</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Omzet Minggu Ini</p>
                <h3 id="report-weekly-revenue" class="text-2xl font-bold text-gray-800">Rp 0</h3>
                <p id="report-weekly-count" class="text-xs text-gray-500 mt-1">0 Transaksi</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between border-none shadow-md hover:shadow-lg transition-all border-b-4 !border-b-purple-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-purple-50 text-purple-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <span class="text-[10px] font-bold text-purple-500 uppercase tracking-widest bg-purple-50 px-2 py-1 rounded-lg">Tahunan</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Omzet Tahun Ini</p>
                <h3 id="report-yearly-revenue" class="text-2xl font-bold text-gray-800">Rp 0</h3>
                <p id="report-yearly-count" class="text-xs text-gray-500 mt-1">0 Transaksi</p>
            </div>
        </div>

        <div class="card flex flex-col justify-between border-none shadow-md hover:shadow-lg transition-all border-b-4 !border-b-red-500">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest bg-red-50 px-2 py-1 rounded-lg">Total</span>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pengeluaran</p>
                <h3 id="report-total-expenses" class="text-2xl font-bold text-red-600">Rp 0</h3>
            </div>
        </div>
    </div>

    <!-- Detailed Transactions Table -->
    <div class="card !p-0 overflow-hidden shadow-md border-none">
        <div class="p-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white sticky top-0 z-10">
            <h3 class="font-bold text-gray-800">Rincian Transaksi</h3>
            <div class="flex gap-2">
                <input type="date" id="filter-date" class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-xs outline-none focus:border-coffee/30">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
                    <tr>
                        <th class="text-left px-6 py-5">Waktu</th>
                        <th class="text-left px-6 py-5">Meja</th>
                        <th class="text-left px-6 py-5">Metode</th>
                        <th class="text-left px-6 py-5">Total</th>
                        <th class="text-right px-6 py-5">Aksi</th>
                    </tr>
                </thead>
                <tbody id="transactions-table-body" class="divide-y divide-gray-50">
                    <!-- Transactions will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="tx-modal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Rincian Transaksi</h3>
            <button onclick="closeTxModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <div id="tx-details-content" class="p-6 max-h-[70vh] overflow-y-auto">
            <!-- Details injected here -->
        </div>
        <div class="p-6 bg-gray-50 flex gap-3">
            <button onclick="closeTxModal()" class="flex-1 bg-white text-gray-700 py-3 rounded-xl font-bold text-sm border border-gray-200">TUTUP</button>
            <button id="print-tx-btn" class="flex-1 bg-coffee text-white py-3 rounded-xl font-bold text-sm shadow-lg shadow-coffee/20">CETAK STRUK</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    let allSales = [];

    async function loadFinancialData() {
        try {
            const [salesResponse, expensesResponse] = await Promise.all([
                fetch('/sales-report?all', { headers: { 'Accept': 'application/json' } }),
                fetch('/admin/expenses', { headers: { 'Accept': 'application/json' } })
            ]);
            
            if (!salesResponse.ok || !expensesResponse.ok) throw new Error('Gagal memuat data');
            
            allSales = await salesResponse.json();
            const expenses = await expensesResponse.json();
            
            const totalExpenses = Array.isArray(expenses) ? expenses.reduce((sum, exp) => sum + (parseFloat(exp.amount) || 0), 0) : 0;
            document.getElementById('report-total-expenses').textContent = `Rp ${totalExpenses.toLocaleString('id-ID')}`;
            
            updateSummaryCards();
            renderTransactions();
        } catch (error) {
            console.error('Error loading financial data:', error);
            // Don't show toast if it's just no data
            document.getElementById('transactions-table-body').innerHTML = '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">Tidak ada data untuk ditampilkan.</td></tr>';
        }
    }

    function updateSummaryCards() {
        const now = new Date();
        const todayStr = now.toISOString().split('T')[0];
        
        const last7Days = new Date();
        last7Days.setDate(now.getDate() - 7);
        
        const currentYear = now.getFullYear();

        const daily = allSales.filter(s => (s.completedDate || s.date) === todayStr);
        const weekly = allSales.filter(s => new Date(s.completedDate || s.date) >= last7Days);
        const yearly = allSales.filter(s => new Date(s.completedDate || s.date).getFullYear() === currentYear);

        const sum = arr => arr.reduce((a, b) => a + (parseFloat(b.total) || 0), 0);

        document.getElementById('report-daily-revenue').textContent = `Rp ${sum(daily).toLocaleString('id-ID')}`;
        document.getElementById('report-daily-count').textContent = `${daily.length} Transaksi`;

        document.getElementById('report-weekly-revenue').textContent = `Rp ${sum(weekly).toLocaleString('id-ID')}`;
        document.getElementById('report-weekly-count').textContent = `${weekly.length} Transaksi`;

        document.getElementById('report-yearly-revenue').textContent = `Rp ${sum(yearly).toLocaleString('id-ID')}`;
        document.getElementById('report-yearly-count').textContent = `${yearly.length} Transaksi`;
    }

    let filteredSales = [];

    function renderTransactions() {
        const tbody = document.getElementById('transactions-table-body');
        const filterDate = document.getElementById('filter-date').value;
        
        filteredSales = Array.isArray(allSales) ? [...allSales] : [];
        if (filterDate) {
            filteredSales = filteredSales.filter(s => (s.completedDate || s.date) === filterDate);
        }

        // Sort by time desc
        filteredSales.sort((a, b) => {
            const dtA = (a.completedDate || a.date) + ' ' + (a.completedTime || a.time);
            const dtB = (b.completedDate || b.date) + ' ' + (b.completedTime || b.time);
            return dtB.localeCompare(dtA);
        });

        tbody.innerHTML = filteredSales.length ? '' : '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">Tidak ada transaksi ditemukan.</td></tr>';

        filteredSales.forEach((sale, index) => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors';
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <p class="font-bold text-gray-800 text-xs">${sale.completedTime || sale.time || '-'}</p>
                    <p class="text-[9px] text-gray-400 uppercase font-bold">${sale.completedDate || sale.date}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[10px] font-bold">Meja ${sale.table}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-[10px] font-bold text-gray-500 uppercase">${sale.payment}</span>
                </td>
                <td class="px-6 py-4">
                    <p class="font-bold text-coffee text-xs">Rp ${Number(sale.total).toLocaleString('id-ID')}</p>
                </td>
                <td class="px-6 py-4 text-right">
                    <button onclick="viewDetailsByIndex(${index})" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-coffee hover:text-white transition-all flex items-center justify-center ml-auto">
                        <i class="fas fa-eye text-[10px]"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function viewDetailsByIndex(index) {
        const sale = filteredSales[index];
        if (sale) viewDetails(sale);
    }

    function viewDetails(sale) {
        const modal = document.getElementById('tx-modal');
        const content = document.getElementById('tx-details-content');
        
        let itemsHtml = (sale.items || []).map(item => `
            <div class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
                <div>
                    <p class="text-sm font-bold text-gray-800">${item.name} <span class="text-coffee">x${item.quantity}</span></p>
                    ${item.options ? `<p class="text-[10px] text-gray-400">${Object.values(item.options).join(', ')}</p>` : ''}
                </div>
                <p class="text-sm font-bold text-gray-700">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</p>
            </div>
        `).join('');

        content.innerHTML = `
            <div class="text-center mb-6">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold mb-1">ID Transaksi</p>
                <h4 class="font-bold text-gray-800">${sale.id}</h4>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6 bg-gray-50 p-4 rounded-2xl">
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-bold">Waktu</p>
                    <p class="text-xs font-bold text-gray-700">${sale.completedDate || sale.date} ${sale.completedTime || sale.time}</p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-bold">Meja</p>
                    <p class="text-xs font-bold text-gray-700">Meja ${sale.table}</p>
                </div>
                <div>
                    <p class="text-[9px] text-gray-400 uppercase font-bold">Metode Bayar</p>
                    <p class="text-xs font-bold text-gray-700 uppercase">${sale.payment}</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-[9px] text-gray-400 uppercase tracking-widest font-bold mb-3">Item Pesanan</p>
                <div class="space-y-1">
                    ${itemsHtml}
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 space-y-2">
                <div class="flex justify-between text-gray-500">
                    <span class="text-xs font-medium">Subtotal</span>
                    <span class="text-xs font-bold">Rp ${Number(sale.subtotal).toLocaleString('id-ID')}</span>
                </div>
                <div class="flex justify-between text-gray-500">
                    <span class="text-xs font-medium">Pajak (10%)</span>
                    <span class="text-xs font-bold">Rp ${Number(sale.tax).toLocaleString('id-ID')}</span>
                </div>
                <div class="flex justify-between text-coffee pt-2">
                    <span class="text-sm font-black uppercase">Total Akhir</span>
                    <span class="text-lg font-black italic">Rp ${Number(sale.total).toLocaleString('id-ID')}</span>
                </div>
            </div>
        `;

        document.getElementById('print-tx-btn').onclick = () => window.print();
        modal.classList.remove('hidden');
    }

    function closeTxModal() {
        document.getElementById('tx-modal').classList.add('hidden');
    }

    function exportData(format) {
        if (!allSales.length) {
            window.showToast('Tidak ada data untuk diekspor', 'info');
            return;
        }

        if (format === 'excel') {
            const data = allSales.map(s => ({
                'ID': s.id,
                'Tanggal': s.completedDate || s.date,
                'Waktu': s.completedTime || s.time,
                'Meja': s.table,
                'Metode': s.payment,
                'Subtotal': s.subtotal,
                'Pajak': s.tax,
                'Total': s.total,
                'Catatan': s.orderNote || '-'
            }));
            
            const ws = XLSX.utils.json_to_sheet(data);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Laporan Keuangan");
            XLSX.writeFile(wb, `Laporan_CoolCafe_${new Date().toISOString().split('T')[0]}.xlsx`);
            window.showToast('File Excel berhasil diunduh!');
        } else {
            window.print();
        }
    }

    document.getElementById('filter-date').addEventListener('change', renderTransactions);
    document.addEventListener('DOMContentLoaded', loadFinancialData);
</script>
@endpush
