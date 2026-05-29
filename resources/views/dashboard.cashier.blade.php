<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - Cashier Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f3f4f6;
        }
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
</head>
<body class="min-h-screen">
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

    <nav class="bg-[#634832] text-white p-4 shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex justify-between items-center">
            <div class="flex items-center gap-2">
                <i class="fas fa-cash-register text-2xl"></i>
                <h1 class="text-xl font-bold">CoolCafe Cashier</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="hidden md:inline text-xs text-white/70">{{ Auth::user()->name }} ({{ Auth::user()->role }})</span>
                <a href="/dashboard" class="text-xs hover:bg-white/10 px-3 py-2 rounded-xl font-semibold">Dashboard</a>
                <span id="order-status" class="bg-green-500/20 text-green-100 text-xs px-3 py-1 rounded-full border border-green-500/50">
                    Sistem Aktif
                </span>
                <button onclick="showSection('orders')" id="orders-tab" class="text-xs bg-white/15 px-3 py-2 rounded-xl font-semibold">Pesanan</button>
                <button onclick="showSection('report')" id="report-tab" class="text-xs hover:bg-white/10 px-3 py-2 rounded-xl font-semibold">Laporan</button>
                <button onclick="clearAllOrders()" class="text-xs text-red-200 hover:text-white underline">Hapus Semua</button>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="text-xs bg-red-500/80 hover:bg-red-500 px-3 py-2 rounded-xl font-semibold">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container mx-auto p-4 md:p-8">
        <section id="orders-section">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Pesanan Masuk</h2>
            <p class="text-gray-500 text-sm">Monitor pesanan pelanggan secara real-time.</p>
        </div>

        <div id="orders-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Order Cards will appear here -->
        </div>

        <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 text-gray-400">
            <i class="fas fa-receipt text-6xl mb-4"></i>
            <p class="text-lg">Belum ada pesanan masuk</p>
        </div>
        </section>

        <section id="report-section" class="hidden">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan</h2>
                    <p class="text-gray-500 text-sm">Ringkasan transaksi yang sudah diselesaikan kasir.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <input type="date" id="report-date" class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-[#634832]">
                    <button onclick="loadReport()" class="bg-[#634832] text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-[#4f3928]">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    @if (in_array(Auth::user()->role, ['admin', 'superadmin'], true))
                        <button onclick="clearSalesReport()" class="text-red-500 bg-red-50 px-4 py-2 rounded-xl font-bold text-sm hover:bg-red-100">
                            <i class="fas fa-trash mr-2"></i>Reset Laporan
                        </button>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Omzet</p>
                    <p id="report-revenue" class="text-2xl font-bold text-[#634832] mt-2">Rp 0</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah Transaksi</p>
                    <p id="report-transactions" class="text-2xl font-bold text-gray-800 mt-2">0</p>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rata-rata Transaksi</p>
                    <p id="report-average" class="text-2xl font-bold text-gray-800 mt-2">Rp 0</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Metode Pembayaran</h3>
                    <div id="payment-summary" class="space-y-3"></div>
                </div>
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <h3 class="font-bold text-gray-800 mb-4">Item Terlaris</h3>
                    <div id="top-items" class="space-y-3"></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-bold text-gray-800">Riwayat Transaksi</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500">
                            <tr>
                                <th class="text-left px-5 py-3 font-semibold">Waktu</th>
                                <th class="text-left px-5 py-3 font-semibold">Meja</th>
                                <th class="text-left px-5 py-3 font-semibold">Pembayaran</th>
                                <th class="text-left px-5 py-3 font-semibold">Item</th>
                                <th class="text-right px-5 py-3 font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody id="sales-table" class="divide-y divide-gray-100"></tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>

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
            document.getElementById('orders-tab').className = section === 'orders'
                ? 'text-xs bg-white/15 px-3 py-2 rounded-xl font-semibold'
                : 'text-xs hover:bg-white/10 px-3 py-2 rounded-xl font-semibold';
            document.getElementById('report-tab').className = section === 'report'
                ? 'text-xs bg-white/15 px-3 py-2 rounded-xl font-semibold'
                : 'text-xs hover:bg-white/10 px-3 py-2 rounded-xl font-semibold';

            if (section === 'report') {
                loadReport();
            }
        }

        async function loadOrders() {
            const response = await fetch('/orders', { headers: { 'Accept': 'application/json' } });
            const orders = await response.json();
            const grid = document.getElementById('orders-grid');
            const emptyState = document.getElementById('empty-state');

            if (orders.length === 0) {
                grid.innerHTML = '';
                emptyState.classList.remove('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            grid.innerHTML = '';

            // Gunakan salinan array agar tidak merusak urutan asli saat di-reverse
            [...orders].reverse().forEach((order) => {
                const card = document.createElement('div');
                card.className = 'order-card bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden';
                
                let itemsHtml = order.items.map(item => `
                    <div class="flex justify-between items-start py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="font-medium text-gray-800">${item.name} <span class="text-[#634832]">x${item.quantity}</span></p>
                            ${item.options ? `<p class="text-[10px] text-gray-400">(${item.options.sweetness}${item.options.note ? ', ' + item.options.note : ''})</p>` : ''}
                        </div>
                        <p class="text-sm font-semibold text-gray-600">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</p>
                    </div>
                `).join('');

                card.innerHTML = `
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-[#634832] text-white px-4 py-1 rounded-full font-bold text-sm">Meja ${order.table}</span>
                            <span class="text-[10px] text-gray-400">${order.time}</span>
                        </div>
                        <div class="mb-4">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Item Pesanan</p>
                            ${itemsHtml}
                        </div>
                        ${order.orderNote ? `
                            <div class="bg-yellow-50 p-3 rounded-xl mb-4">
                                <p class="text-[10px] font-bold text-yellow-700 uppercase mb-1">Catatan Pesanan:</p>
                                <p class="text-xs text-yellow-800 italic">"${order.orderNote}"</p>
                            </div>
                        ` : ''}
                        <div class="flex justify-between items-center pt-4 border-t">
                            <div>
                                <p class="text-[10px] text-gray-400">Pembayaran: <span class="text-gray-800 font-bold">${order.payment}</span></p>
                        <p class="text-lg font-bold text-[#634832]">${formatRupiah(order.total)}</p>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="deleteOrder(${order.id})" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button onclick="completeOrder(${order.id})" class="bg-green-500 text-white px-4 py-2 rounded-xl font-bold text-sm hover:bg-green-600 transition-colors shadow-md shadow-green-200">
                                    Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        async function completeOrder(id) {
            await fetch(`/orders/${id}/complete`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            showToast('Pesanan selesai dan masuk laporan keuangan!');
            loadOrders();
            if (activeSection === 'report') {
                loadReport();
            }
        }

        async function deleteOrder(id) {
            if (await showConfirm({
                title: 'Batalkan pesanan?',
                message: 'Pesanan akan dihapus dari daftar masuk dan tidak masuk laporan.',
                okText: 'Batalkan'
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
                title: 'Hapus semua pesanan?',
                message: 'Semua pesanan aktif akan dihapus dari kasir.',
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

        async function loadReport() {
            const dateInput = document.getElementById('report-date');
            if (!dateInput.value) {
                dateInput.value = new Date().toISOString().slice(0, 10);
            }

            const response = await fetch(`/sales-report?date=${dateInput.value}`, { headers: { 'Accept': 'application/json' } });
            const report = await response.json();

            document.getElementById('report-revenue').innerText = formatRupiah(report.revenue);
            document.getElementById('report-transactions').innerText = report.transactions;
            document.getElementById('report-average').innerText = formatRupiah(report.averageTransaction);

            const paymentSummary = document.getElementById('payment-summary');
            const paymentEntries = Object.entries(report.paymentSummary || {});
            paymentSummary.innerHTML = paymentEntries.length ? '' : '<p class="text-sm text-gray-400">Belum ada pembayaran.</p>';
            paymentEntries.forEach(([method, data]) => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center bg-gray-50 rounded-xl px-4 py-3';
                row.innerHTML = `
                    <div>
                        <p class="font-semibold text-gray-800">${method}</p>
                        <p class="text-xs text-gray-400">${data.count} transaksi</p>
                    </div>
                    <p class="font-bold text-[#634832]">${formatRupiah(data.total)}</p>
                `;
                paymentSummary.appendChild(row);
            });

            const topItems = document.getElementById('top-items');
            topItems.innerHTML = report.topItems.length ? '' : '<p class="text-sm text-gray-400">Belum ada item terjual.</p>';
            report.topItems.forEach((item, index) => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center bg-gray-50 rounded-xl px-4 py-3';
                row.innerHTML = `
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-full bg-[#634832] text-white text-xs font-bold flex items-center justify-center">${index + 1}</span>
                        <div>
                            <p class="font-semibold text-gray-800">${item.name}</p>
                            <p class="text-xs text-gray-400">${item.quantity} item terjual</p>
                        </div>
                    </div>
                    <p class="font-bold text-[#634832]">${formatRupiah(item.total)}</p>
                `;
                topItems.appendChild(row);
            });

            const salesTable = document.getElementById('sales-table');
            salesTable.innerHTML = report.sales.length ? '' : `
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-gray-400">Belum ada transaksi selesai pada tanggal ini.</td>
                </tr>
            `;
            report.sales.forEach((sale) => {
                const row = document.createElement('tr');
                const items = (sale.items || []).map(item => `${item.name} x${item.quantity}`).join(', ');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-5 py-4 text-gray-500">${sale.completedTime || sale.time || '-'}</td>
                    <td class="px-5 py-4 font-semibold text-gray-800">Meja ${sale.table}</td>
                    <td class="px-5 py-4 text-gray-600">${sale.payment}</td>
                    <td class="px-5 py-4 text-gray-600 min-w-[220px]">${items}</td>
                    <td class="px-5 py-4 text-right font-bold text-[#634832]">${formatRupiah(sale.total)}</td>
                `;
                salesTable.appendChild(row);
            });
        }

        async function clearSalesReport() {
            if (await showConfirm({
                title: 'Reset laporan keuangan?',
                message: 'Semua data laporan akan dikosongkan. Pesanan aktif tidak ikut terhapus.',
                okText: 'Reset'
            })) {
                await fetch('/sales-report', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                loadReport();
            }
        }

        // Initial load
        document.getElementById('report-date').value = new Date().toISOString().slice(0, 10);
        loadOrders();
        
        // Auto refresh every 5 seconds as a fallback
        setInterval(() => {
            loadOrders();
            if (activeSection === 'report') {
                loadReport();
            }
        }, 5000);
    </script>
</body>
</html>
