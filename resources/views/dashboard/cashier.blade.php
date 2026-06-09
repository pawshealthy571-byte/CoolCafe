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
</style>
@endpush

@section('content')

<!-- Register New Product Modal -->
<div id="product-register-modal" class="confirm-backdrop">
    <div class="confirm-box p-6 !max-w-md">
        <div class="w-12 h-12 rounded-2xl bg-coffee/10 text-coffee flex items-center justify-center mb-4">
            <i class="fas fa-plus text-xl"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-800 mb-2">Produk Baru Terdeteksi</h3>
        <p class="text-xs text-gray-400 mb-4">Produk ini ditemukan di database online. Masukkan harga dan kategori untuk mendaftarkannya.</p>
        
        <form id="product-register-form" onsubmit="saveNewScannedProduct(event)">
            <input type="hidden" id="reg-barcode">
            <div class="space-y-4 mb-5">
                <div>
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Produk</label>
                    <input type="text" id="reg-name" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Kategori</label>
                        <select id="reg-category" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30">
                            <option value="Minuman">Minuman</option>
                            <option value="Bakery">Bakery</option>
                            <option value="Main Course">Main Course</option>
                            <option value="Cemilan">Cemilan</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Harga Jual (Rp)</label>
                        <input type="number" id="reg-price" required min="0" placeholder="Contoh: 10000" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30">
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="closeProductRegisterModal()" type="button" class="bg-gray-100 text-gray-700 py-3 rounded-2xl font-bold text-sm">
                    Batal
                </button>
                <button type="submit" class="bg-coffee text-white py-3 rounded-2xl font-bold text-sm hover:bg-coffee-dark transition-all">
                    Simpan & Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 id="section-title" class="text-2xl font-bold text-gray-800">Pesanan Masuk</h2>
        <p id="section-desc" class="text-gray-500 text-sm">Monitor pesanan pelanggan secara real-time.</p>
    </div>
    <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-2xl">
        <button id="sound-toggle" onclick="toggleSound()" class="px-4 py-2 rounded-xl text-xs font-bold transition-all bg-white text-gray-500 hover:text-coffee flex items-center gap-2">
            <i class="fas fa-volume-mute"></i> <span>Suara Off</span>
        </button>
        <button onclick="showSection('orders')" id="orders-tab" class="px-6 py-2 rounded-xl text-xs font-bold transition-all bg-white shadow-sm text-coffee">
            Pesanan
        </button>
        <button onclick="showSection('scan')" id="scan-tab" class="px-6 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:text-gray-700">
            Scan Barcode Belanja
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

<section id="scan-section" class="hidden">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scanner Input Panel -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card !p-6">
                <h3 class="font-bold text-gray-800 text-lg mb-2 flex items-center gap-2">
                    <i class="fas fa-barcode text-coffee"></i> Scan Item
                </h3>
                <p class="text-xs text-gray-400 mb-4">Arahkan alat barcode scanner Anda dan scan barcode item menu, atau ketik manual kodenya di bawah ini.</p>
                
                <!-- Camera Scan Feature (html5-qrcode) -->
                <div class="mb-4 bg-gray-50/50 border border-gray-100 rounded-2xl p-4">
                    <button type="button" onclick="toggleCameraScanner()" class="flex items-center justify-between w-full text-xs font-bold text-gray-600 uppercase tracking-widest hover:text-coffee transition-all outline-none">
                        <span class="flex items-center gap-2 text-coffee">
                            <i class="fas fa-camera"></i> Gunakan Kamera (Scan Barcode)
                        </span>
                        <span id="camera-btn-text" class="text-xs text-gray-400 font-semibold">Buka Kamera</span>
                    </button>
                    <div id="camera-scanner-container" class="hidden mt-4 bg-black rounded-xl overflow-hidden relative aspect-video w-full max-w-sm mx-auto shadow-inner">
                        <div id="interactive" class="w-full h-full"></div>
                        <div class="absolute inset-0 border-2 border-dashed border-white/20 pointer-events-none flex items-center justify-center">
                            <div class="w-2/3 h-1/2 border-2 border-coffee/60 rounded-lg shadow-[0_0_0_9999px_rgba(0,0,0,0.4)]"></div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 bg-gray-50 border border-gray-100 rounded-2xl p-2 focus-within:border-coffee/30 focus-within:bg-white transition-all shadow-inner">
                    <input type="text" id="scanner-input" placeholder="Scan barcode disini..." class="flex-1 bg-transparent px-3 py-2 text-sm font-semibold text-gray-800 outline-none placeholder:text-gray-400" autofocus>
                    <button onclick="triggerManualScan()" class="bg-coffee text-white px-5 py-2.5 rounded-xl text-xs font-bold hover:scale-105 active:scale-95 transition-all shadow-md shadow-coffee/15 flex items-center gap-1.5">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>

            <!-- List Menu references for cashier reference (Premium aesthetic) -->
            <div class="card !p-6">
                <div class="flex items-center justify-between mb-4">
                    <h4 class="font-bold text-gray-700 text-sm">Referensi Menu Terdaftar</h4>
                    <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-[8px] text-[9px] font-bold" id="ref-menu-count">0 menu</span>
                </div>
                <div class="overflow-y-auto max-h-[40vh] pr-2 space-y-2 no-scrollbar" id="ref-menu-list">
                    <!-- Loaded dynamically via fetch /admin/menus -->
                </div>
            </div>
        </div>

        <!-- Cashier Cart Panel -->
        <div class="space-y-6">
            <div class="card !p-6 flex flex-col min-h-[50vh] justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2"><i class="fas fa-shopping-basket text-coffee"></i> Keranjang</h3>
                        <button onclick="clearCashierCart()" class="text-[10px] text-red-500 font-bold hover:underline">Reset</button>
                    </div>

                    <!-- Cart Items List -->
                    <div id="cashier-cart-items" class="space-y-3 max-h-[30vh] overflow-y-auto pr-1">
                        <p class="text-center py-8 text-xs text-gray-400">Keranjang kosong. Silakan scan barcode item.</p>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4 mt-4 space-y-4">
                    <!-- Meja & Pembayaran info -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-1">No Meja</label>
                            <input type="number" id="cashier-table" placeholder="Meja" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-3 py-2 text-xs outline-none focus:border-coffee/30 font-semibold" min="1">
                        </div>
                        <div>
                            <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-1">Bayar</label>
                            <select id="cashier-payment" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-3 py-2 text-xs outline-none focus:border-coffee/30 font-semibold">
                                <option value="Cash">Cash</option>
                                <option value="QRIS">QRIS</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-1.5 bg-gray-50 p-4 rounded-2xl border border-gray-100 shadow-inner">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Subtotal</span>
                            <span id="cashier-subtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Pajak (12%)</span>
                            <span id="cashier-tax">Rp 0</span>
                        </div>
                        <div class="flex justify-between font-bold text-sm text-[#634832] pt-1.5 border-t border-dashed">
                            <span>Total</span>
                            <span id="cashier-total">Rp 0</span>
                        </div>
                    </div>

                    <button onclick="submitCashierOrder()" class="w-full bg-coffee text-white py-3.5 rounded-2xl font-bold text-xs shadow-lg shadow-coffee/20 hover:scale-[1.02] active:scale-95 transition-all">
                        BUAT PESANAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="report-section" class="hidden">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
            <input type="date" id="report-date" class="bg-transparent border-none px-4 py-2 text-sm font-bold text-gray-700 outline-none">
            <button onclick="loadReport()" class="bg-coffee text-white w-10 h-10 rounded-xl flex items-center justify-center hover:scale-105 active:scale-95 transition-all">
                <i class="fas fa-filter text-xs"></i>
            </button>
        </div>
        @if (in_array(Auth::user()->role, ['admin', 'superadmin', 'manager'], true))
            <button onclick="clearSalesReport()" class="text-red-500 bg-red-50 px-5 py-3 rounded-2xl font-bold text-xs hover:bg-red-100 transition-all flex items-center gap-2">
                <i class="fas fa-rotate-left"></i> Reset Laporan
            </button>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card !bg-coffee text-white overflow-hidden relative border-none">
            <div class="relative z-10">
                <p class="text-[10px] font-bold text-white/60 uppercase tracking-[0.2em] mb-1">Total Omzet</p>
                <h3 id="report-revenue" class="text-3xl font-bold italic">Rp 0</h3>
            </div>
            <i class="fas fa-wallet absolute -bottom-4 -right-4 text-8xl text-white/5 rotate-12"></i>
        </div>
        <div class="card border-b-4 border-b-blue-500">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Transaksi</p>
            <h3 id="report-transactions" class="text-3xl font-bold text-gray-800">0</h3>
        </div>
        <div class="card border-b-4 border-b-amber-500">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em] mb-1">Rata-rata</p>
            <h3 id="report-average" class="text-3xl font-bold text-gray-800">Rp 0</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="card !p-0 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 text-indigo-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-credit-card"></i>
                </div>
                <h3 class="font-bold text-gray-800">Metode Pembayaran</h3>
            </div>
            <div id="payment-summary" class="p-6 space-y-4"></div>
        </div>
        <div class="card !p-0 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 text-amber-500 rounded-xl flex items-center justify-center">
                    <i class="fas fa-fire"></i>
                </div>
                <h3 class="font-bold text-gray-800">Item Terlaris</h3>
            </div>
            <div id="top-items" class="p-6 space-y-4"></div>
        </div>
    </div>

    <div class="card !p-0 overflow-hidden">
        <div class="p-6 border-b border-gray-50 flex items-center gap-3">
            <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded-xl flex items-center justify-center">
                <i class="fas fa-history"></i>
            </div>
            <h3 class="font-bold text-gray-800">Riwayat Transaksi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
                    <tr>
                        <th class="text-left px-6 py-4">Waktu</th>
                        <th class="text-left px-6 py-4">Meja</th>
                        <th class="text-left px-6 py-4">Metode</th>
                        <th class="text-left px-6 py-4">Item Terjual</th>
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
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let activeSection = 'orders';
    let lastOrderCount = -1;
    let soundEnabled = false;

    function toggleSound() {
        soundEnabled = !soundEnabled;
        const btn = document.getElementById('sound-toggle');
        const icon = btn.querySelector('i');
        const text = btn.querySelector('span');
        
        if (soundEnabled) {
            btn.classList.add('text-coffee');
            btn.classList.remove('text-gray-500');
            icon.className = 'fas fa-volume-up';
            text.innerText = 'Suara On';
            // Play a silent sound to unlock audio
            const utterance = new SpeechSynthesisUtterance('');
            window.speechSynthesis.speak(utterance);
            window.showToast('Notifikasi suara diaktifkan');
        } else {
            btn.classList.remove('text-coffee');
            btn.classList.add('text-gray-500');
            icon.className = 'fas fa-volume-mute';
            text.innerText = 'Suara Off';
            window.showToast('Notifikasi suara dimatikan');
        }
    }

    function playOrderNotification(items = []) {
        if (soundEnabled && 'speechSynthesis' in window) {
            let text = 'Pesanan baru masuk. ';
            if (items.length > 0) {
                const itemNames = items.map(i => i.name).join(', ');
                text += 'Isinya: ' + itemNames;
            }
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 1;
            utterance.pitch = 1;
            window.speechSynthesis.speak(utterance);
        }
    }

    function speakOrder(id) {
        if (window.speechSynthesis.speaking) {
            window.speechSynthesis.cancel();
            return;
        }
        
        const card = orderCards.get(String(id));
        if (!card) return;
        
        // Find item names from the card's content
        const names = Array.from(card.querySelectorAll('.text-gray-800 b, .text-gray-800'))
            .filter(el => el.innerText.includes(' x'))
            .map(el => el.innerText.split(' x')[0].trim());
        
        if (names.length > 0) {
            const text = 'Pesanan meja ' + card.querySelector('.bg-coffee').innerText.replace('Meja ', '') + '. Isinya: ' + names.join(', ');
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            window.speechSynthesis.speak(utterance);
        }
    }

    function formatRupiah(value) {
        return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
    }

    function showSection(section) {
        activeSection = section;
        document.getElementById('orders-section').classList.toggle('hidden', section !== 'orders');
        document.getElementById('scan-section').classList.toggle('hidden', section !== 'scan');
        document.getElementById('report-section').classList.toggle('hidden', section !== 'report');
        
        let title = 'Pesanan Masuk';
        let desc = 'Monitor pesanan pelanggan secara real-time.';
        if (section === 'scan') {
            title = 'Scan Barcode Belanja';
            desc = 'Scan barcode item untuk membuat pesanan pelanggan secara langsung.';
            loadRefMenus();
            setTimeout(() => {
                const scannerInput = document.getElementById('scanner-input');
                if (scannerInput) scannerInput.focus();
            }, 100);
        } else if (section === 'report') {
            title = 'Laporan Keuangan';
            desc = 'Ringkasan transaksi yang sudah diselesaikan hari ini.';
        }
        
        document.getElementById('section-title').innerText = title;
        document.getElementById('section-desc').innerText = desc;

        document.getElementById('orders-tab').className = section === 'orders'
            ? 'px-6 py-2 rounded-xl text-xs font-bold transition-all bg-white shadow-sm text-coffee'
            : 'px-6 py-2 rounded-xl text-xs font-bold transition-all text-gray-500 hover:text-gray-700';
        document.getElementById('scan-tab').className = section === 'scan'
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

            // Sound Notification logic
            if (lastOrderCount !== -1 && orders.length > lastOrderCount) {
                // Get the newest order(s)
                const newOrders = orders.slice(lastOrderCount);
                newOrders.forEach(order => playOrderNotification(order.items));
            }
            lastOrderCount = orders.length;

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
                                <button onclick="speakOrder('${order.id}')" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-coffee/10 hover:text-coffee flex items-center justify-center transition-all">
                                    <i class="fas fa-volume-up text-xs"></i>
                                </button>
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
                                    <p class="text-[9px] text-gray-400">Via: <span class="text-gray-600 font-bold uppercase">${order.payment}</span></p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <button onclick="deleteOrder('${order.id}')" class="bg-red-50 text-red-500 py-3 rounded-xl font-bold text-xs hover:bg-red-500 hover:text-white transition-all duration-300">
                                    <i class="fas fa-trash-can mr-1"></i> Batal
                                </button>
                                <button onclick="completeOrder('${order.id}')" class="bg-coffee text-white py-3 rounded-xl font-bold text-xs hover:bg-opacity-95 hover:scale-[1.02] transition-all duration-300 shadow-md shadow-coffee/10">
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
            
            document.getElementById('report-revenue').innerText = formatRupiah(report.revenue);
            document.getElementById('report-transactions').innerText = report.transactions;
            document.getElementById('report-average').innerText = formatRupiah(report.averageTransaction || (report.revenue / (report.transactions || 1)));

            const paymentSummary = document.getElementById('payment-summary');
            const paymentEntries = Object.entries(report.paymentSummary || {});
            paymentSummary.innerHTML = paymentEntries.length ? '' : '<p class="text-xs text-gray-400 py-4 text-center">Belum ada data.</p>';
            paymentEntries.forEach(([method, data]) => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center bg-gray-50 rounded-2xl px-5 py-4';
                row.innerHTML = `
                    <div>
                        <p class="font-bold text-gray-800 text-xs uppercase">${method}</p>
                        <p class="text-[10px] text-gray-400">${data.count} transaksi</p>
                    </div>
                    <p class="font-bold text-coffee text-sm">${formatRupiah(data.total)}</p>
                `;
                paymentSummary.appendChild(row);
            });

            const topItems = document.getElementById('top-items');
            const items = report.topItems || [];
            topItems.innerHTML = items.length ? '' : '<p class="text-xs text-gray-400 py-4 text-center">Belum ada data.</p>';
            items.forEach((item, index) => {
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
                const itemsStr = (sale.items || []).map(item => `${item.name} x${item.quantity}`).join(', ');
                row.className = 'hover:bg-gray-50 transition-colors';
                row.innerHTML = `
                    <td class="px-6 py-4 text-gray-500 text-xs font-medium">${sale.completedTime || sale.time || '-'}</td>
                    <td class="px-6 py-4 font-bold text-gray-800 text-xs">Meja ${sale.table}</td>
                    <td class="px-6 py-4"><span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-[9px] font-bold uppercase">${sale.payment}</span></td>
                    <td class="px-6 py-4 text-gray-400 text-[10px] min-w-[200px] italic">${itemsStr}</td>
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
        window.showToast('Pesanan berhasil diselesaikan!');
        loadOrders();
        if (activeSection === 'report') {
            loadReport();
        }
    }

    async function deleteOrder(id) {
        const confirmed = await window.showConfirm({
            title: 'Batalkan Pesanan?',
            message: 'Pesanan meja ini akan dihapus permanen. Lanjutkan?',
            okText: 'YA, BATALKAN'
        });

        if (confirmed) {
            await fetch(`/orders/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            window.showToast('Pesanan telah dibatalkan.');
            loadOrders();
        }
    }

    async function clearAllOrders() {
        const confirmed = await window.showConfirm({
            title: 'Hapus Semua?',
            message: 'Semua pesanan yang ada di daftar akan dihapus permanen. Lanjutkan?',
            okText: 'HAPUS SEMUA'
        });

        if (confirmed) {
            await fetch('/orders', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            window.showToast('Seluruh pesanan telah dibersihkan.');
            loadOrders();
        }
    }

    async function clearSalesReport() {
        const confirmed = await window.showConfirm({
            title: 'Reset Laporan?',
            message: 'Seluruh riwayat transaksi akan dihapus permanen. Tindakan ini tidak dapat dibatalkan!',
            okText: 'YA, RESET SEMUA'
        });

        if (confirmed) {
            await fetch('/sales-report', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            window.showToast('Laporan keuangan telah direset.');
            loadReport();
        }
    }

    // --- Cashier Scan & Cart Logic ---
    let cashierCart = [];
    let registeredMenus = [];

    async function loadRefMenus() {
        try {
            const response = await fetch('/admin/menus', { headers: { 'Accept': 'application/json' } });
            registeredMenus = await response.json();
            
            // Render count
            document.getElementById('ref-menu-count').innerText = `${registeredMenus.length} menu`;

            // Render list
            const container = document.getElementById('ref-menu-list');
            container.innerHTML = '';
            
            const STORAGE_URL = "{{ asset('storage') }}";

            registeredMenus.forEach(menu => {
                const item = document.createElement('div');
                // Base style for all items
                item.className = 'group flex items-center gap-3 p-2.5 bg-white hover:bg-coffee/5 border border-gray-100 hover:border-coffee/20 rounded-2xl transition-all duration-200 cursor-pointer shadow-sm hover:shadow-md';
                item.onclick = () => addMenuItemToCashierCart(menu);
                
                let thumbnail = 'https://placehold.co/100x100?text=Menu';
                if (menu.image && !menu.image.startsWith('http')) {
                    thumbnail = `${STORAGE_URL}/${menu.image}`;
                } else if (menu.image && menu.image.startsWith('http')) {
                    thumbnail = menu.image;
                }

                const isSnackDrink = menu.category === 'Snack & Minuman';
                const barcodeImage = menu.barcode_image ? `${STORAGE_URL}/${menu.barcode_image}` : null;

                item.innerHTML = `
                    <div class="relative w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform duration-200">
                        <img src="${thumbnail}" class="w-full h-full object-cover">
                        ${isSnackDrink && barcodeImage ? `
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center">
                                <img src="${barcodeImage}" class="w-full h-full object-contain bg-white/80 p-0.5">
                            </div>
                        ` : ''}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-bold text-xs text-gray-800 truncate">${menu.name}</p>
                            <span class="text-[9px] font-bold text-coffee whitespace-nowrap">Rp ${Number(menu.price).toLocaleString('id-ID')}</span>
                        </div>
                        <div class="flex items-center gap-1.5 mt-0.5">
                            <span class="text-[9px] font-medium text-gray-400 uppercase tracking-wider">${menu.category}</span>
                            ${isSnackDrink ? '<span class="w-1 h-1 rounded-full bg-coffee/40"></span>' : ''}
                            ${menu.barcode ? `<span class="text-[9px] font-mono text-gray-400">${menu.barcode}</span>` : ''}
                        </div>
                    </div>
                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 text-coffee">
                        <i class="fas fa-plus-circle text-sm"></i>
                    </div>
                `;
                container.appendChild(item);
            });
        } catch (error) {
            console.error('Error loading reference menus:', error);
        }
    }

    // Attach Event Listener for Keypress in Scanner Input & Globally for barcode scanner
    let cashierBarcodeBuffer = '';
    let cashierLastKeyTime = 0;

    window.addEventListener('keypress', function(e) {
        // Only run scan detection if active section is 'scan'
        if (activeSection !== 'scan') return;

        const currentTime = Date.now();
        
        // If focusing on some inputs (like table number/notes/scanner input), prevent global capture
        const activeTag = document.activeElement ? document.activeElement.tagName.toLowerCase() : '';
        if (activeTag === 'textarea' || (activeTag === 'input' && (document.activeElement.id === 'cashier-table' || document.activeElement.id === 'scanner-input'))) {
            return;
        }

        // Hardware scanners output characters very fast (typically < 30ms difference)
        if (currentTime - cashierLastKeyTime > 50) {
            cashierBarcodeBuffer = '';
        }
        
        cashierLastKeyTime = currentTime;

        if (e.key === 'Enter') {
            if (cashierBarcodeBuffer.length >= 3) {
                e.preventDefault();
                processCashierBarcode(cashierBarcodeBuffer);
            }
            cashierBarcodeBuffer = '';
            
            // Clear input field if focus is inside scanner-input
            const input = document.getElementById('scanner-input');
            if (input) input.value = '';
        } else if (/^[a-zA-Z0-9]$/.test(e.key)) {
            cashierBarcodeBuffer += e.key;
        }
    });

    // Also support typing / pressing search button manually
    const scannerInput = document.getElementById('scanner-input');
    if (scannerInput) {
        scannerInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation(); // Prevent duplicate processing on window event listener
                const code = this.value.trim();
                if (code) {
                    processCashierBarcode(code);
                    this.value = '';
                }
            }
        });
    }

    function triggerManualScan() {
        const input = document.getElementById('scanner-input');
        const code = input.value.trim();
        if (!code) return;
        
        processCashierBarcode(code);
        input.value = '';
        input.focus();
    }

    let html5QrCode = null;
    let cameraScanActive = false;

    async function toggleCameraScanner() {
        const container = document.getElementById('camera-scanner-container');
        const btnText = document.getElementById('camera-btn-text');
        
        if (cameraScanActive) {
            if (html5QrCode) {
                try {
                    await html5QrCode.stop();
                } catch (e) {
                    console.error(e);
                }
            }
            container.classList.add('hidden');
            btnText.innerText = 'Buka Kamera';
            cameraScanActive = false;
        } else {
            container.classList.remove('hidden');
            btnText.innerText = 'Tutup Kamera';
            cameraScanActive = true;
            
            setTimeout(() => {
                html5QrCode = new Html5Qrcode("interactive");
                const config = { fps: 15, qrbox: { width: 250, height: 150 } };
                
                html5QrCode.start(
                    { facingMode: "environment" }, 
                    config,
                    (decodedText) => {
                        processCashierBarcode(decodedText);
                        toggleCameraScanner(); // Stop scanning after success
                    },
                    (errorMessage) => {
                        // ignore failures
                    }
                ).catch(err => {
                    console.error(err);
                    window.showToast("Gagal mengakses kamera. Pastikan izin kamera aktif & gunakan HTTPS.", "error");
                    toggleCameraScanner();
                });
            }, 100);
        }
    }

    function openProductRegisterModal(barcode, name) {
        document.getElementById('reg-barcode').value = barcode;
        document.getElementById('reg-name').value = name;
        document.getElementById('reg-price').value = '';
        
        const modal = document.getElementById('product-register-modal');
        if (modal) modal.classList.add('show');
    }

    function closeProductRegisterModal() {
        const modal = document.getElementById('product-register-modal');
        if (modal) modal.classList.remove('show');
    }

    async function saveNewScannedProduct(e) {
        e.preventDefault();
        
        const barcode = document.getElementById('reg-barcode').value;
        const name = document.getElementById('reg-name').value;
        const category = document.getElementById('reg-category').value;
        const price = document.getElementById('reg-price').value;
        
        try {
            const response = await fetch('/admin/menus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: name,
                    category: category,
                    price: price,
                    barcode: barcode,
                    is_available: true,
                    description: 'Terdaftar otomatis via barcode scanner.'
                })
            });
            
            const result = await response.json();
            if (response.ok) {
                window.showToast(`Berhasil mendaftarkan ${name}!`);
                closeProductRegisterModal();
                
                // Refresh registered menus list
                await loadRefMenus();
                
                // Add the newly created item to cart
                addMenuItemToCashierCart(result);
            } else {
                window.showToast(result.message || 'Gagal menyimpan produk baru.', 'error');
            }
        } catch (error) {
            console.error('Error saving scanned product:', error);
            window.showToast('Gagal terhubung ke server untuk menyimpan produk.', 'error');
        }
    }

    async function processCashierBarcode(code) {
        // Find menu by barcode
        const menu = registeredMenus.find(m => String(m.barcode) === String(code));
        if (menu) {
            addMenuItemToCashierCart(menu);
            return;
        }

        // Look up product from Open Food Facts API
        window.showToast(`Mencari produk barcode "${code}" di database online...`, 'info');
        
        try {
            const response = await fetch(`https://world.openfoodfacts.org/api/v2/product/${code}.json?fields=product_name,product_name_id,brands`);
            const data = await response.json();
            
            if (data.status === 1 && data.product) {
                const productName = data.product.product_name_id || data.product.product_name || "Produk Baru";
                const brand = data.product.brands ? ` (${data.product.brands})` : '';
                const fullName = productName + brand;
                
                openProductRegisterModal(code, fullName);
            } else {
                window.showToast(`Barcode "${code}" tidak terdaftar di database lokal maupun online!`, 'error');
            }
        } catch (error) {
            console.error('Error looking up barcode:', error);
            window.showToast(`Gagal menghubungi database online. Tambahkan manual di menu.`, 'error');
        }
    }

    function addMenuItemToCashierCart(menu) {
        const existing = cashierCart.find(item => item.id === menu.id);
        if (existing) {
            existing.quantity++;
        } else {
            cashierCart.push({
                id: menu.id,
                name: menu.name,
                price: menu.price,
                quantity: 1,
                options: null // Direct additions from cashier
            });
        }
        updateCashierCartUI();
        window.showToast(`Berhasil menambahkan ${menu.name} ke keranjang!`);
    }

    function removeCashierCartQty(id) {
        const item = cashierCart.find(i => i.id === id);
        if (item) {
            if (item.quantity > 1) {
                item.quantity--;
            } else {
                cashierCart = cashierCart.filter(i => i.id !== id);
            }
            updateCashierCartUI();
        }
    }

    function addCashierCartQty(id) {
        const item = cashierCart.find(i => i.id === id);
        if (item) {
            item.quantity++;
            updateCashierCartUI();
        }
    }

    function clearCashierCart() {
        cashierCart = [];
        updateCashierCartUI();
    }

    function updateCashierCartUI() {
        const container = document.getElementById('cashier-cart-items');
        if (!container) return;

        container.innerHTML = '';
        if (cashierCart.length === 0) {
            container.innerHTML = '<p class="text-center py-8 text-xs text-gray-400">Keranjang kosong. Silakan scan barcode item.</p>';
            document.getElementById('cashier-subtotal').innerText = 'Rp 0';
            document.getElementById('cashier-tax').innerText = 'Rp 0';
            document.getElementById('cashier-total').innerText = 'Rp 0';
            return;
        }

        let subtotal = 0;
        cashierCart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'flex justify-between items-center bg-gray-50 p-3 rounded-xl border border-gray-100 shadow-sm';
            row.innerHTML = `
                <div class="flex-grow">
                    <p class="font-bold text-xs text-gray-800">${item.name}</p>
                    <p class="text-[10px] text-gray-400">Rp ${Number(item.price).toLocaleString('id-ID')}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="removeCashierCartQty(${item.id})" class="w-6 h-6 rounded-full border border-gray-200 bg-white flex items-center justify-center text-xs hover:bg-gray-100">-</button>
                    <span class="text-xs font-semibold w-5 text-center">${item.quantity}</span>
                    <button onclick="addCashierCartQty(${item.id})" class="w-6 h-6 rounded-full bg-coffee text-white flex items-center justify-center text-xs">+</button>
                </div>
            `;
            container.appendChild(row);
            subtotal += item.price * item.quantity;
        });

        const tax = Math.round(subtotal * 0.12);
        const total = subtotal + tax;

        document.getElementById('cashier-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
        document.getElementById('cashier-tax').innerText = 'Rp ' + tax.toLocaleString('id-ID');
        document.getElementById('cashier-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
    }

    async function submitCashierOrder() {
        if (cashierCart.length === 0) {
            window.showToast('Keranjang masih kosong!', 'error');
            return;
        }

        const table = document.getElementById('cashier-table').value.trim();
        if (!table) {
            window.showToast('Harap isi nomor meja!', 'error');
            return;
        }

        const payment = document.getElementById('cashier-payment').value;
        const subtotal = cashierCart.reduce((acc, i) => acc + (i.price * i.quantity), 0);
        const tax = Math.round(subtotal * 0.12);
        const total = subtotal + tax;

        try {
            const response = await fetch('/orders', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    table: table,
                    payment: payment,
                    orderNote: 'Dibuat langsung oleh Kasir',
                    items: cashierCart,
                    subtotal: subtotal,
                    tax: tax,
                    total: total
                })
            });

            if (response.ok) {
                window.showToast('Pesanan berhasil dibuat!');
                clearCashierCart();
                document.getElementById('cashier-table').value = '';
                showSection('orders');
            } else {
                window.showToast('Gagal membuat pesanan.', 'error');
            }
        } catch (error) {
            console.error('Error submitting cashier order:', error);
            window.showToast('Terjadi kesalahan koneksi.', 'error');
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
