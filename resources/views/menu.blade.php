<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - Digital Menu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f5f2;
        }
        .menu-card {
            transition: transform 0.2s;
        }
        .menu-card:active {
            transform: scale(0.98);
        }
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e53e3e;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 10px;
            font-weight: bold;
        }
        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 50;
            background-color: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
        }
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
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
        .confirm-detail {
            max-height: 220px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        @keyframes popIn {
            to { transform: translateY(0) scale(1); }
        }
    </style>
</head>
<body class="pb-24">
    <div id="toast" class="toast">
        <div class="flex items-start gap-3">
            <i id="toast-icon" class="fas fa-check-circle text-[#634832] mt-1"></i>
            <p id="toast-message" class="text-sm font-semibold"></p>
        </div>
    </div>
    <div id="confirm-modal" class="confirm-backdrop">
        <div class="confirm-box p-6">
            <div class="w-12 h-12 rounded-2xl bg-[#634832]/10 text-[#634832] flex items-center justify-center mb-4">
                <i class="fas fa-circle-question text-xl"></i>
            </div>
            <h3 id="confirm-title" class="text-lg font-bold text-gray-800 mb-2">Konfirmasi</h3>
            <p id="confirm-message" class="text-sm text-gray-500 mb-4"></p>
            <pre id="confirm-detail" class="confirm-detail hidden bg-gray-50 text-gray-700 text-xs rounded-2xl p-4 mb-5 font-sans"></pre>
            <div class="grid grid-cols-2 gap-3">
                <button id="confirm-cancel" type="button" class="bg-gray-100 text-gray-700 py-3 rounded-2xl font-bold text-sm">
                    Batal
                </button>
                <button id="confirm-ok" type="button" class="bg-[#634832] text-white py-3 rounded-2xl font-bold text-sm">
                    Lanjut
                </button>
            </div>
        </div>
    </div>

    <!-- Header & Table Info -->
    <header class="sticky-header shadow-sm p-4 mb-4">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-2xl font-bold text-[#634832]">CoolCafe</h1>
                <p class="text-xs text-gray-500">Café & Bakery</p>
            </div>
            <div class="relative cursor-pointer" onclick="toggleCart()">
                <i class="fas fa-shopping-basket text-2xl text-[#634832]"></i>
                <span id="cart-count" class="cart-badge hidden">0</span>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-3 shadow-inner flex items-center gap-3">
            <i class="fas fa-table text-gray-400"></i>
            <input type="number" id="table-number" placeholder="Nomor Meja" class="w-full bg-transparent focus:outline-none text-sm font-medium" min="1">
        </div>
    </header>

    <!-- Categories Navigation -->
    <nav class="flex overflow-x-auto gap-3 px-4 mb-6 no-scrollbar">
        <button onclick="filterCategory('all')" class="category-pill active whitespace-nowrap px-6 py-2 rounded-full border border-[#634832] text-sm font-medium">Semua</button>
        <button onclick="filterCategory('Bakery')" class="category-pill whitespace-nowrap px-6 py-2 rounded-full border border-[#634832] text-sm font-medium text-[#634832]">Bakery</button>
        <button onclick="filterCategory('Beverages')" class="category-pill whitespace-nowrap px-6 py-2 rounded-full border border-[#634832] text-sm font-medium text-[#634832]">Coffee & Beverages</button>
        <button onclick="filterCategory('Main Course')" class="category-pill whitespace-nowrap px-6 py-2 rounded-full border border-[#634832] text-sm font-medium text-[#634832]">Main Course</button>
        <button onclick="filterCategory('Paket')" class="category-pill whitespace-nowrap px-6 py-2 rounded-full border border-[#634832] text-sm font-medium text-[#634832]">Paket</button>
    </nav>

    <!-- Menu List -->
    <div id="menu-container" class="px-4 space-y-4">
        @foreach($menus as $menu)
        <div class="menu-item" data-category="{{ $menu->category }}">
            <div class="menu-card bg-white rounded-2xl p-3 flex gap-4 shadow-sm">
                <div class="w-24 h-24 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                    @php
                        $imageUrl = $menu->image ?? 'https://placehold.co/200x200?text=No+Image';
                        if ($menu->image && !str_starts_with($menu->image, 'http')) {
                            $imageUrl = asset('storage/' . $menu->image);
                        }
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-col justify-between flex-grow">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $menu->name }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $menu->description }}</p>
                    </div>
                    <div class="flex justify-between items-center mt-2">
                        <span class="font-bold text-[#634832]">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                        @if($menu->category === 'Beverages')
                            <button onclick="openCustomization('{{ $menu->name }}', {{ $menu->price }})" class="bg-[#634832] text-white px-3 py-1 rounded-lg text-xs font-medium">Tambah</button>
                        @elseif($menu->category === 'Paket')
                            <button onclick="openPackageCustomization({{ json_encode(['name' => $menu->name, 'price' => $menu->price, 'add_ons' => $menu->add_ons ?? []]) }})" class="bg-[#634832] text-white px-3 py-1 rounded-lg text-xs font-medium">Tambah</button>
                        @else
                            <button onclick="addToCart('{{ $menu->name }}', {{ $menu->price }})" class="bg-[#634832] text-white px-3 py-1 rounded-lg text-xs font-medium">Tambah</button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Bottom Nav -->
    <div id="cart-footer" class="fixed bottom-0 left-0 right-0 p-4 bg-white border-t rounded-t-3xl shadow-lg hidden">
        <div class="flex justify-between items-center mb-4">
            <div>
                <p class="text-xs text-gray-500">Total Pesanan</p>
                <p id="cart-total" class="text-lg font-bold text-[#634832]">Rp 0</p>
            </div>
            <button onclick="toggleCart()" class="bg-[#634832] text-white px-8 py-3 rounded-2xl font-semibold shadow-md active:scale-95 transition-transform">
                Lanjut Pesan
            </button>
        </div>
    </div>

    <!-- Cart Modal -->
    <div id="cart-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-end">
        <div class="bg-white w-full rounded-t-3xl p-6 max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-800">Detail Pesanan</h2>
                <button onclick="toggleCart()" class="text-gray-400"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div id="cart-items" class="space-y-4 mb-6"></div>

            <div class="mb-6">
                <p class="text-sm font-bold text-gray-800 mb-3">Metode Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment-method" value="Cash" class="peer hidden" checked>
                        <div class="border-2 border-gray-100 rounded-xl p-3 text-center peer-checked:border-[#634832] peer-checked:bg-[#634832]/5">
                            <i class="fas fa-money-bill-wave mb-1 text-gray-400"></i>
                            <p class="text-xs font-semibold">Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment-method" value="QRIS" class="peer hidden">
                        <div class="border-2 border-gray-100 rounded-xl p-3 text-center peer-checked:border-[#634832] peer-checked:bg-[#634832]/5">
                            <i class="fas fa-qrcode mb-1 text-gray-400"></i>
                            <p class="text-xs font-semibold">QRIS</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-sm font-bold text-gray-800 mb-3">Catatan Pesanan (Opsional)</p>
                <textarea id="order-note" placeholder="Contoh: Minta sendok lebih..." class="w-full bg-gray-50 rounded-xl p-3 text-sm border border-transparent focus:border-[#634832] h-20 outline-none"></textarea>
            </div>

            <div class="border-t pt-4">
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal</span>
                        <span id="modal-subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Pajak (12%)</span>
                        <span id="modal-tax">Rp 0</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg text-[#634832] pt-2 border-t border-dashed">
                        <span>Total Akhir</span>
                        <span id="modal-total">Rp 0</span>
                    </div>
                </div>
                <button onclick="sendOrder()" class="w-full bg-[#634832] text-white py-4 rounded-2xl font-bold text-lg shadow-lg active:scale-95">
                    Kirim Pesanan
                </button>
            </div>
        </div>
    </div>

    <!-- Customization Modal -->
    <div id="custom-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden flex items-end">
        <div class="bg-white w-full rounded-t-3xl p-6 shadow-2xl transform transition-transform duration-300 translate-y-full" id="custom-sheet">
            <h2 id="custom-title" class="text-xl font-bold text-gray-800 mb-6">Kustomisasi</h2>
            <div class="space-y-6 mb-8">
                <div>
                    <p class="text-sm font-bold text-gray-800 mb-3">Tingkat Kemanisan</p>
                    <div class="flex gap-2">
                        <label><input type="radio" name="sweetness" value="Normal" class="peer hidden" checked><span class="px-4 py-2 rounded-xl border border-gray-200 text-sm peer-checked:bg-[#634832] peer-checked:text-white inline-block">Normal</span></label>
                        <label><input type="radio" name="sweetness" value="Less Sugar" class="peer hidden"><span class="px-4 py-2 rounded-xl border border-gray-200 text-sm peer-checked:bg-[#634832] peer-checked:text-white inline-block">Less Sugar</span></label>
                        <label><input type="radio" name="sweetness" value="No Sugar" class="peer hidden"><span class="px-4 py-2 rounded-xl border border-gray-200 text-sm peer-checked:bg-[#634832] peer-checked:text-white inline-block">No Sugar</span></label>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-800 mb-3">Catatan Item</p>
                    <textarea id="custom-note" placeholder="Contoh: Es sedikit..." class="w-full bg-gray-50 rounded-xl p-3 text-sm h-20 outline-none"></textarea>
                </div>
            </div>
            <button onclick="confirmCustomization()" class="w-full bg-[#634832] text-white py-4 rounded-2xl font-bold text-lg">Tambah</button>
        </div>
    </div>

    <!-- Package Add-on Modal -->
    <div id="package-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden flex items-end">
        <div class="bg-white w-full rounded-t-3xl p-6 shadow-2xl transform transition-transform duration-300 translate-y-full" id="package-sheet">
            <div class="flex items-start justify-between gap-4 mb-5">
                <div>
                    <h2 id="package-title" class="text-xl font-bold text-gray-800">Paket</h2>
                    <p id="package-base-price" class="text-sm font-bold text-[#634832] mt-1">Rp 0</p>
                </div>
                <button onclick="closePackageCustomization()" class="text-gray-400"><i class="fas fa-times text-xl"></i></button>
            </div>
            <div class="mb-6">
                <p class="text-sm font-bold text-gray-800 mb-3">Add-on</p>
                <div id="package-add-ons" class="space-y-3"></div>
            </div>
            <div class="mb-6">
                <p class="text-sm font-bold text-gray-800 mb-3">Catatan Paket</p>
                <textarea id="package-note" placeholder="Contoh: minuman dingin..." class="w-full bg-gray-50 rounded-xl p-3 text-sm h-20 outline-none"></textarea>
            </div>
            <button onclick="confirmPackageCustomization()" class="w-full bg-[#634832] text-white py-4 rounded-2xl font-bold text-lg">Tambah Paket</button>
        </div>
    </div>

    <script>
        let cart = [];
        let currentCustomItem = null;
        let currentPackageItem = null;
        let toastTimer = null;

        function showConfirm({ title = 'Konfirmasi', message = '', detail = '', okText = 'Lanjut', cancelText = 'Batal' }) {
            const modal = document.getElementById('confirm-modal');
            const detailBox = document.getElementById('confirm-detail');
            document.getElementById('confirm-title').innerText = title;
            document.getElementById('confirm-message').innerText = message;
            document.getElementById('confirm-ok').innerText = okText;
            document.getElementById('confirm-cancel').innerText = cancelText;

            detailBox.innerText = detail;
            detailBox.classList.toggle('hidden', !detail);
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

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toast-icon');
            document.getElementById('toast-message').innerText = message;

            toast.classList.toggle('error', type === 'error');
            icon.className = type === 'error'
                ? 'fas fa-exclamation-circle text-red-500 mt-1'
                : 'fas fa-check-circle text-[#634832] mt-1';

            clearTimeout(toastTimer);
            toast.classList.add('show');
            toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
        }

        function addToCart(name, price, options = null) {
            const existing = cart.find(item => item.name === name && JSON.stringify(item.options) === JSON.stringify(options));
            if (existing) { existing.quantity += 1; } else { cart.push({ name, price, quantity: 1, options }); }
            updateCartUI();
        }

        function openCustomization(name, price) {
            currentCustomItem = { name, price };
            document.getElementById('custom-title').innerText = name;
            const modal = document.getElementById('custom-modal');
            const sheet = document.getElementById('custom-sheet');
            modal.classList.remove('hidden');
            setTimeout(() => sheet.classList.remove('translate-y-full'), 10);
        }

        function confirmCustomization() {
            const sweetness = document.querySelector('input[name="sweetness"]:checked').value;
            const note = document.getElementById('custom-note').value;
            addToCart(currentCustomItem.name, currentCustomItem.price, { sweetness, note });
            closeCustomization();
        }

        function closeCustomization() {
            document.getElementById('custom-sheet').classList.add('translate-y-full');
            setTimeout(() => document.getElementById('custom-modal').classList.add('hidden'), 300);
        }

        function openPackageCustomization(menu) {
            currentPackageItem = menu;
            const modal = document.getElementById('package-modal');
            const sheet = document.getElementById('package-sheet');
            const addOns = menu.add_ons || [];

            document.getElementById('package-title').innerText = menu.name;
            document.getElementById('package-base-price').innerText = 'Rp ' + Number(menu.price).toLocaleString('id-ID');
            document.getElementById('package-note').value = '';

            const container = document.getElementById('package-add-ons');
            container.innerHTML = addOns.length ? '' : '<p class="text-xs text-gray-400 bg-gray-50 rounded-xl p-4">Paket ini belum punya add-on.</p>';
            addOns.forEach((addOn, index) => {
                const row = document.createElement('label');
                row.className = 'flex items-center justify-between gap-4 bg-gray-50 rounded-xl p-4 cursor-pointer';
                row.innerHTML = `
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="package-add-on rounded border-gray-300 text-[#634832] focus:ring-[#634832]" data-index="${index}">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">${addOn.name}</p>
                            <p class="text-xs text-gray-500">Tambah Rp ${Number(addOn.price || 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                    <i class="fas fa-plus text-[#634832] text-xs"></i>
                `;
                container.appendChild(row);
            });

            modal.classList.remove('hidden');
            setTimeout(() => sheet.classList.remove('translate-y-full'), 10);
        }

        function confirmPackageCustomization() {
            const selectedAddOns = Array.from(document.querySelectorAll('.package-add-on:checked'))
                .map(input => currentPackageItem.add_ons[Number(input.dataset.index)]);
            const addOnTotal = selectedAddOns.reduce((total, addOn) => total + Number(addOn.price || 0), 0);
            const note = document.getElementById('package-note').value;

            addToCart(currentPackageItem.name, Number(currentPackageItem.price) + addOnTotal, {
                type: 'Paket',
                addOns: selectedAddOns,
                note
            });
            closePackageCustomization();
        }

        function closePackageCustomization() {
            document.getElementById('package-sheet').classList.add('translate-y-full');
            setTimeout(() => document.getElementById('package-modal').classList.add('hidden'), 300);
        }

        function removeFromCart(name, options = null) {
            const idx = cart.findIndex(item => item.name === name && JSON.stringify(item.options) === JSON.stringify(options));
            if (idx > -1) { if (cart[idx].quantity > 1) { cart[idx].quantity -= 1; } else { cart.splice(idx, 1); } }
            updateCartUI();
        }

        function updateCartUI() {
            const count = cart.reduce((acc, i) => acc + i.quantity, 0);
            const subtotal = cart.reduce((acc, i) => acc + (i.price * i.quantity), 0);
            const tax = Math.round(subtotal * 0.12);
            const total = subtotal + tax;

            document.getElementById('cart-count').innerText = count;
            document.getElementById('cart-count').classList.toggle('hidden', count === 0);
            document.getElementById('cart-footer').classList.toggle('hidden', count === 0);
            document.getElementById('cart-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
            
            document.getElementById('modal-subtotal').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
            document.getElementById('modal-tax').innerText = 'Rp ' + tax.toLocaleString('id-ID');
            document.getElementById('modal-total').innerText = 'Rp ' + total.toLocaleString('id-ID');

            const container = document.getElementById('cart-items');
            container.innerHTML = cart.length ? '' : '<p class="text-center py-10 text-gray-400">Kosong</p>';
            cart.forEach(item => {
                const opt = item.options ? `<p class="text-[10px] text-gray-400">${formatOptions(item.options)}</p>` : '';
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-gray-50 p-3 rounded-xl';
                div.innerHTML = `<div class="flex-grow"><p class="font-medium text-sm">${item.name}</p>${opt}</div><div class="flex items-center gap-3"><button onclick="removeFromCart('${item.name}', ${item.options ? JSON.stringify(item.options).replace(/"/g, '&quot;') : 'null'})" class="w-8 h-8 rounded-full border flex items-center justify-center">-</button><span class="text-sm font-semibold">${item.quantity}</span><button onclick="addToCart('${item.name}', ${item.price}, ${item.options ? JSON.stringify(item.options).replace(/"/g, '&quot;') : 'null'})" class="w-8 h-8 rounded-full bg-[#634832] text-white flex items-center justify-center">+</button></div>`;
                container.appendChild(div);
            });
        }

        function formatOptions(options) {
            if (options.type === 'Paket') {
                const addOns = (options.addOns || []).map(addOn => addOn.name).join(', ');
                return `${addOns ? 'Add-on: ' + addOns : 'Tanpa add-on'}${options.note ? ' | ' + options.note : ''}`;
            }

            return `${options.sweetness || ''}${options.note ? ' | ' + options.note : ''}`;
        }

        function toggleCart() { document.getElementById('cart-modal').classList.toggle('hidden'); }

        function filterCategory(cat) {
            document.querySelectorAll('.category-pill').forEach(p => p.classList.toggle('active', p.innerText.toLowerCase().includes(cat.toLowerCase()) || (cat === 'all' && p.innerText === 'Semua')));
            document.querySelectorAll('.menu-item').forEach(i => i.style.display = (cat === 'all' || i.dataset.category === cat) ? 'block' : 'none');
        }

        async function sendOrder() {
            const table = document.getElementById('table-number').value;
            if (!table) return showToast('Input nomor meja dulu.', 'error');
            const payment = document.querySelector('input[name="payment-method"]:checked').value;
            const orderNote = document.getElementById('order-note').value;
            const subtotal = cart.reduce((a, b) => a + (b.price * b.quantity), 0);
            const tax = Math.round(subtotal * 0.12);
            const total = subtotal + tax;
            
            let summary = `Detail Pesanan - Meja ${table}\nPembayaran: ${payment}\n${orderNote ? 'Catatan: ' + orderNote + '\n' : ''}------------------\n`;
            cart.forEach(i => summary += `${i.name} x${i.quantity}${i.options ? ' (' + formatOptions(i.options) + ')' : ''}\n`);
            summary += `------------------\nSubtotal: Rp ${subtotal.toLocaleString('id-ID')}\nPajak (12%): Rp ${tax.toLocaleString('id-ID')}\nTotal Akhir: Rp ${total.toLocaleString('id-ID')}`;
            
            if (await showConfirm({
                title: 'Kirim pesanan?',
                message: 'Pastikan detail pesanan sudah benar sebelum dikirim ke kasir.',
                detail: summary,
                okText: 'Kirim'
            })) {
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
                            orderNote: orderNote,
                            items: cart,
                            subtotal: subtotal,
                            tax: tax,
                            total: total
                        })
                    });

                    if (!response.ok) throw new Error('Gagal mengirim pesanan');

                    showToast(payment === 'QRIS' ? 'Pesanan dikirim. Membuka QRIS...' : 'Pesanan dikirim!');
                    const tableNum = table; // save to local variable
                    cart = []; updateCartUI(); toggleCart();
                    document.getElementById('table-number').value = '';
                    document.getElementById('order-note').value = '';

                    if (payment === 'QRIS') {
                        const params = new URLSearchParams({
                            table: tableNum,
                            total: total
                        });
                        setTimeout(() => {
                            window.location.href = `/qris?${params.toString()}`;
                        }, 800);
                    } else {
                        // For Cash, go directly to estimation
                        setTimeout(() => {
                            window.location.href = `/estimation?table=${tableNum}`;
                        }, 800);
                    }
                } catch (error) {
                    showToast('Pesanan belum terkirim. Coba lagi.', 'error');
                    console.error(error);
                }
            }
        }
    </script>
</body>
</html>
