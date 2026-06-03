@extends('layouts.app')

@section('title', 'Cashier Dashboard')

@push('styles')
<style>
    .order-card {
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .toast {
        position: fixed;
        left: 50%;
        top: 20px;
        z-index: 100;
        width: calc(100% - 32px);
        max-width: 420px;
        padding: 14px 16px;
        border-radius: 18px;
        background: #ffffff;
        color: #1f2937;
        box-shadow: 0 20px 45px rgba(31, 41, 55, 0.18);
        border-left: 6px solid #22c55e;
        transform: translate(-50%, -18px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease, transform 0.25s ease;
    }
    .toast.show {
        opacity: 1;
        transform: translate(-50%, 0);
    }
    .confirm-backdrop {
        position: fixed;
        inset: 0;
        z-index: 110;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(17, 24, 39, 0.55);
        backdrop-filter: blur(4px);
    }
    .confirm-backdrop.show {
        display: flex;
    }
    .confirm-box {
        width: 100%;
        max-width: 420px;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 25px 55px rgba(17, 24, 39, 0.28);
        transform: translateY(10px) scale(0.98);
        animation: popIn 0.2s ease forwards;
    }
    @keyframes popIn {
        to { transform: translateY(0) scale(1); }
    }
</style>
@endpush

@section('content')
<div id="toast" class="toast">
    <div class="flex items-start gap-3">
        <i class="fas fa-check-circle text-green-500 mt-1"></i>
        <p id="toast-message" class="text-sm font-semibold"></p>
    </div>
</div>

<div id="confirm-modal" class="confirm-backdrop">
    <div class="confirm-box p-6">
        <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center mb-4">
            <i class="fas fa-triangle-exclamation text-xl"></i>
        </div>
        <h3 id="confirm-title" class="text-lg font-bold text-gray-800 mb-2">Konfirmasi</h3>
        <p id="confirm-message" class="text-sm text-gray-500 mb-5"></p>
        <div class="grid grid-cols-2 gap-3">
            <button id="confirm-cancel" type="button" class="bg-gray-100 text-gray-700 py-3 rounded-2xl font-bold text-sm">
                Batal
            </button>
            <button id="confirm-ok" type="button" class="bg-red-500 text-white py-3 rounded-2xl font-bold text-sm">
                Hapus
            </button>
        </div>
    </div>
</div>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 id="section-title" class="text-2xl font-bold text-gray-800">Pesanan Masuk</h2>
        <p id="section-desc" class="text-gray-500 text-sm">Monitor pesanan pelanggan secara real-time.</p>
    </div>
    <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-2xl">
        <button onclick="showSection('orders')" id="orders-tab" class="px-6 py-2 rounded-xl text-xs font-bold transition-all bg-white shadow-sm text-coffee">
            Pesanan
        </button>
        <button onclick="showSection('report')" id="report-tab" class="px-6 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:text-gray-700">
            Laporan
        </button>
    </div>
</div>

<section id="orders-section">
    <div class="flex justify-end mb-4">
        <button onclick="clearAllOrders()" class="text-xs text-red-500 font-bold hover:underline">
            <i class="fas fa-trash-can mr-1"></i> Hapus Semua Pesanan
        </button>
    </div>
    <div id="orders-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Order Cards will appear here -->
    </div>

    <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 text-gray-300">
        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-receipt text-4xl"></i>
        </div>
        <p class="text-lg font-medium text-gray-400">Belum ada pesanan masuk</p>
    </div>
</section>

<section id="report-section" class="hidden">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2">
            <input type="date" id="report-date" class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-coffee">
            <button onclick="loadReport()" class="btn-primary !text-xs">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
        @if (in_array(Auth::user()->role, ['admin', 'superadmin'], true))
            <button onclick="clearSalesReport()" class="text-red-500 bg-red-50 px-4 py-2 rounded-xl font-bold text-xs hover:bg-red-100 transition-colors">
                <i class="fas fa-rotate-left mr-2"></i>Reset Laporan
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="card border-l-4 border-l-coffee">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Omzet</p>
            <p id="report-revenue" class="text-2xl font-bold text-coffee mt-2">Rp 0</p>
        </div>
        <div class="card">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transaksi</p>
            <p id="report-transactions" class="text-2xl font-bold text-gray-800 mt-2">0</p>
        </div>
        <div class="card">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Rata-rata</p>
            <p id="report-average" class="text-2xl font-bold text-gray-800 mt-2">Rp 0</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="card !p-0 overflow-hidden">
            <div class="p-5 border-b border-gray-50">
                <h3 class="font-bold text-gray-800">Metode Pembayaran</h3>
            </div>
            <div id="payment-summary" class="p-5 space-y-3"></div>
        </div>
        <div class="card !p-0 overflow-hidden">
            <div class="p-5 border-b border-gray-50">
                <h3 class="font-bold text-gray-800">Item Terlaris</h3>
            </div>
            <div id="top-items" class="p-5 space-y-3"></div>
        </div>
    </div>

    <div class="card !p-0 overflow-hidden">
        <div class="p-5 border-b border-gray-50">
            <h3 class="font-bold text-gray-800">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
                    <tr>
                        <th class="text-left px-6 py-4">Waktu</th>
                        <th class="text-left px-6 py-4">Meja</th>
                        <th class="text-left px-6 py-4">Pembayaran</th>
                        <th class="text-left px-6 py-4">Item</th>
                        <th class="text-right px-6 py-4">Total</th>
                    </tr>
                </thead>
                <tbody id="sales-table" class="divide-y divide-gray-50"></tbody>
            </table>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    let activeSection = 'orders';
    let toastTimer = null;

    function showConfirm({ title = 'Konfirmasi', message = '', okText = 'Hapus', cancelText = 'Batal' }) {
        const modal = document.getElementById('confirm-modal');
        document.getElementById('confirm-title').innerText = title;
        document.getElementById('confirm-message').innerText = message;
        document.getElementById('confirm-ok').innerText = okText;
        document.getElementById('confirm-cancel').innerText = cancelText;
        modal.classList.add('show');

        return new Promise((resolve) => {
            const okButton = document.getElementById('confirm-ok');
            const cancelButton = document.getElementById('confirm-cancel');

            const close = (result) => {
                modal.classList.remove('show');
                okButton.removeEventListener('click', onOk);
                cancelButton.removeEventListener('click', onCancel);
                modal.removeEventListener('click', onBackdrop);
                resolve(result);
            };
            const onOk = () => close(true);
            const onCancel = () => close(false);
            const onBackdrop = (event) => {
                if (event.target === modal) close(false);
            };

            okButton.addEventListener('click', onOk);
            cancelButton.addEventListener('click', onCancel);
            modal.addEventListener('click', onBackdrop);
        });
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').innerText = message;

        clearTimeout(toastTimer);
        toast.classList.add('show');
        toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function showSection(section) {
        activeSection = section;
        document.getElementById('orders-section').classList.toggle('hidden', section !== 'orders');
        document.getElementById('report-section').classList.toggle('hidden', section !== 'report');
        
        const title = section === 'orders' ? 'Pesanan Masuk' : 'Laporan Keuangan';
        const desc = section === 'orders' ? 'Monitor pesanan pelanggan secara real-time.' : 'Ringkasan transaksi yang sudah diselesaikan kasir.';
        
        document.getElementById('section-title').innerText = title;
        document.getElementById('section-desc').innerText = desc;

        document.getElementById('orders-tab').className = section === 'orders'
            ? 'px-6 py-2 rounded-xl text-xs font-bold transition-all bg-white shadow-sm text-coffee'
            : 'px-6 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
        document.getElementById('report-tab').className = section === 'report'
            ? 'px-6 py-2 rounded-xl text-xs font-bold transition-all bg-white shadow-sm text-coffee'
            : 'px-6 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:text-gray-700';

        if (section === 'report') {
            loadReport();
        }
    }

    const orderCards = new Map();
    let lastReportJson = '';

    async function loadOrders() {
        try {
            const response = await fetch('/orders', { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Gagal mengambil data pesanan');
            
            const orders = await response.json();
            const grid = document.getElementById('orders-grid');
            const emptyState = document.getElementById('empty-state');

            if (!orders || orders.length === 0) {
                grid.innerHTML = '';
                orderCards.clear();
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            const currentIds = new Set(orders.map(o => String(o.id)));

            // Remove stale cards
            for (const [id, element] of orderCards.entries()) {
                if (!currentIds.has(id)) {
                    element.remove();
                    orderCards.delete(id);
                }
            }

            // Update or Add
            [...orders].reverse().forEach((order) => {
                const id = String(order.id);
                const isReady = order.status === 'ready';
                const stateJson = JSON.stringify({ status: order.status, items: order.items.length });
                
                let card = orderCards.get(id);
                if (card) {
                    if (card.dataset.state === stateJson) return;
                } else {
                    card = document.createElement('div');
                    card.id = `order-${id}`;
                    grid.appendChild(card);
                    orderCards.set(id, card);
                }

                card.dataset.state = stateJson;
                card.className = `order-card card !p-0 overflow-hidden transition-all duration-300 ${isReady ? 'ring-2 ring-green-500 shadow-lg shadow-green-100' : ''}`;
                
                let itemsHtml = order.items.map(item => `
                    <div class="flex justify-between items-start py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-xs font-bold text-gray-800">${item.name} <span class="text-coffee">x${item.quantity}</span></p>
                            ${item.options ? `<p class="text-[9px] text-gray-400">(${formatItemOptions(item.options)})</p>` : ''}
                        </div>
                        <p class="text-xs font-semibold text-gray-500">${(item.price * item.quantity).toLocaleString('id-ID')}</p>
                    </div>
                `).join('');

                card.innerHTML = `
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-4">
                            <div class="flex items-center gap-2">
                                <span class="bg-coffee text-white px-4 py-1.5 rounded-xl font-bold text-[10px] shadow-sm">Meja ${order.table}</span>
                                ${isReady ? `<span class="bg-green-600 text-white px-3 py-1.5 rounded-xl font-bold text-[9px]">SIAP DIAMBIL</span>` : ''}
                            </div>
                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">${order.time}</span>
                        </div>
                        <div class="mb-4">
                            <p class="text-[9px] font-bold text-gray-300 uppercase tracking-[0.2em] mb-3">Item Pesanan</p>
                            <div class="space-y-1">
                                ${itemsHtml}
                            </div>
                        </div>
                        ${order.orderNote ? `
                            <div class="bg-amber-50 p-3 rounded-xl mb-4 border border-amber-100">
                                <p class="text-[9px] font-bold text-amber-700 uppercase mb-1">Catatan:</p>
                                <p class="text-[10px] text-amber-800 italic">"${order.orderNote}"</p>
                            </div>
                        ` : ''}
                        <div class="pt-4 border-t border-gray-50">
                            <div class="space-y-1 mb-4">
                                <div class="flex justify-between items-center text-[10px] text-gray-400">
                                    <span>Subtotal</span>
                                    <span>${formatRupiah(order.subtotal || (order.total / 1.12))}</span>
                                </div>
                                <div class="flex justify-between items-center text-[10px] text-gray-400">
                                    <span>Pajak (12%)</span>
                                    <span>${formatRupiah(order.tax || (order.total - (order.total / 1.12)))}</span>
                                </div>
                                <div class="flex justify-between items-end pt-1 border-t border-dashed">
                                    <div>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase">Total Tagihan</p>
                                        <p class="text-lg font-bold text-coffee leading-none">${formatRupiah(order.total)}</p>
                                    </div>
                                    <p class="text-[9px] text-gray-400">Via: <span class="text-gray-600 font-bold">${order.payment}</span></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="deleteOrder('${order.id}')" class="bg-red-50 text-red-500 py-2.5 rounded-xl font-bold text-xs hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fas fa-trash-can mr-1"></i> Batal
                                </button>
                                <button onclick="completeOrder('${order.id}')" class="bg-coffee text-white py-2.5 rounded-xl font-bold text-xs hover:bg-opacity-90 transition-all shadow-md shadow-coffee/10">
                                    <i class="fas fa-check mr-2"></i> Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Error loading orders:', error);
        }
    }

    async function loadReport() {
        const dateInput = document.getElementById('report-date');
        if (!dateInput.value) {
            dateInput.value = new Date().toISOString().slice(0, 10);
        }

        try {
            const response = await fetch(`/sales-report?date=${dateInput.value}`, { headers: { 'Accept': 'application/json' } });
            const report = await response.json();
            const currentReportJson = JSON.stringify({ revenue: report.revenue, tx: report.transactions, sales: (report.sales || []).length });

            if (currentReportJson === lastReportJson) return;
            lastReportJson = currentReportJson;

            document.getElementById('report-revenue').innerText = formatRupiah(report.revenue);
            document.getElementById('report-transactions').innerText = report.transactions;
            document.getElementById('report-average').innerText = formatRupiah(report.averageTransaction);

            const paymentSummary = document.getElementById('payment-summary');
            const paymentEntries = Object.entries(report.paymentSummary || {});
            paymentSummary.innerHTML = paymentEntries.length ? '' : '<p class="text-xs text-gray-400 py-4 text-center">Belum ada data.</p>';
            paymentEntries.forEach(([method, data]) => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center bg-gray-50 rounded-2xl px-5 py-4';
                row.innerHTML = `
                    <div>
                        <p class="font-bold text-gray-800 text-xs">${method}</p>
                        <p class="text-[10px] text-gray-400">${data.count} transaksi</p>
                    </div>
                    <p class="font-bold text-coffee text-sm">${formatRupiah(data.total)}</p>
                `;
                paymentSummary.appendChild(row);
            });

            const topItems = document.getElementById('top-items');
            topItems.innerHTML = report.topItems.length ? '' : '<p class="text-xs text-gray-400 py-4 text-center">Belum ada data.</p>';
            report.topItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center bg-gray-50 rounded-2xl px-5 py-4';
                row.innerHTML = `
                    <div class="flex items-center gap-4">
                        <span class="w-8 h-8 rounded-xl bg-coffee/10 text-coffee text-xs font-bold flex items-center justify-center">${index + 1}</span>
                        <div>
                            <p class="font-bold text-gray-800 text-xs">${item.name}</p>
                            <p class="text-[10px] text-gray-400">${item.quantity} terjual</p>
                        </div>
                    </div>
                    <p class="font-bold text-coffee text-sm">${formatRupiah(item.total)}</p>
                `;
                topItems.appendChild(row);
            });

            const salesTable = document.getElementById('sales-table');
            salesTable.innerHTML = (report.sales || []).length ? '' : `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-xs">Belum ada transaksi pada tanggal ini.</td>
                </tr>
            `;
            (report.sales || []).forEach((sale) => {
                const row = document.createElement('tr');
                const items = (sale.items || []).map(item => `${item.name} x${item.quantity}`).join(', ');
                row.className = 'hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 text-gray-500 text-xs">${sale.completedTime || sale.time || '-'}</td>
                    <td class="px-6 py-4 font-bold text-gray-800 text-xs">Meja ${sale.table}</td>
                    <td class="px-6 py-4 text-gray-600 text-xs">${sale.payment}</td>
                    <td class="px-6 py-4 text-gray-500 text-xs min-w-[200px]">${items}</td>
                    <td class="px-6 py-4 text-right font-bold text-coffee text-sm">${formatRupiah(sale.total)}</td>
                `;
                salesTable.appendChild(row);
            });
        } catch (error) {
            console.error('Error loading report:', error);
        }
    }

    function formatItemOptions(options) {
        if (options.type === 'Paket') {
            const addOns = (options.addOns || []).map(addOn => addOn.name).join(', ');
            return `${addOns ? 'Add-on: ' + addOns : 'Tanpa add-on'}${options.note ? ', ' + options.note : ''}`;
        }

        return `${options.sweetness || ''}${options.note ? ', ' + options.note : ''}`;
    }

    async function completeOrder(id) {
        await fetch(`/orders/${id}/complete`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        showToast('Pesanan diselesaikan!');
        loadOrders();
        if (activeSection === 'report') {
            loadReport();
        }
    }

    async function deleteOrder(id) {
        if (await showConfirm({
            title: 'Batalkan pesanan?',
            message: 'Pesanan akan dihapus permanen.',
            okText: 'Ya, Batalkan'
        })) {
            await deleteOrderById(id);
        }
    }

    async function deleteOrderById(id) {
        await fetch(`/orders/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        loadOrders();
    }

    async function clearAllOrders() {
        if (await showConfirm({
            title: 'Hapus semua?',
            message: 'Semua pesanan aktif akan dihapus.',
            okText: 'Hapus Semua'
        })) {
            await fetch('/orders', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            loadOrders();
        }
    }

    document.getElementById('report-date').value = new Date().toISOString().slice(0, 10);
    loadOrders();
    
    setInterval(() => {
        loadOrders();
        if (activeSection === 'report') {
            loadReport();
        }
    }, 5000);
</script>
@endpush
