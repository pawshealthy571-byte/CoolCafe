<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - Digital Menu</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
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
            z-index: 150;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(36, 24, 15, 0.4);
            backdrop-filter: blur(8px);
        }
        .confirm-backdrop.show {
            display: flex;
        }
        .confirm-box {
            width: 100%;
            max-width: 420px;
            border-radius: 28px;
            background: #ffffff;
            box-shadow: 0 25px 55px rgba(36, 24, 15, 0.15);
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
        .bottom-sheet {
            transition: transform 0.3s cubic-bezier(0.32, 0.94, 0.6, 1);
        }
    </style>
</head>
<body class="pb-28 bg-coffee-50 min-h-screen font-sans antialiased selection:bg-coffee-200">
    <!-- Toast Notification -->
    <div id="toast" class="toast">
        <div class="flex items-start gap-3">
            <div id="toast-icon-bg" class="w-6 h-6 rounded-full bg-coffee-50 flex items-center justify-center flex-shrink-0">
                <i id="toast-icon" class="fas fa-check text-coffee text-xs"></i>
            </div>
            <p id="toast-message" class="text-xs font-bold text-coffee-900"></p>
        </div>
    </div>

    <!-- Confirm Dialog Modal -->
    <div id="confirm-modal" class="confirm-backdrop">
        <div class="confirm-box p-6 border border-coffee-950/5">
            <div class="w-12 h-12 rounded-2xl bg-coffee-100 text-coffee flex items-center justify-center mb-4 shadow-sm">
                <i class="fas fa-question text-lg"></i>
            </div>
            <h3 id="confirm-title" class="font-serif text-lg font-bold text-coffee-950 mb-1">Konfirmasi</h3>
            <p id="confirm-message" class="text-xs text-coffee-700 leading-relaxed mb-4"></p>
            <pre id="confirm-detail" class="confirm-detail hidden bg-coffee-50/70 border border-coffee-100 text-coffee-800 text-[11px] rounded-2xl p-4 mb-5 font-sans leading-relaxed"></pre>
            <div class="grid grid-cols-2 gap-3">
                <button id="confirm-cancel" type="button" class="bg-coffee-100 hover:bg-coffee-200 text-coffee-900 py-3.5 rounded-2xl font-bold text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button id="confirm-ok" type="button" class="bg-coffee text-white hover:bg-coffee-dark py-3.5 rounded-2xl font-bold text-xs shadow-md shadow-coffee-700/10 transition-colors cursor-pointer">
                    Lanjut
                </button>
            </div>
        </div>
    </div>

    <!-- Header & Table Info -->
    <header class="sticky top-0 z-40 bg-white/70 backdrop-blur-md border-b border-coffee-950/5 p-4 mb-6 w-full">
        <div class="w-full max-w-md mx-auto flex justify-between items-center mb-4">
            <div>
                <h1 class="font-serif text-2xl font-bold text-coffee-950 tracking-tight">CoolCafe</h1>
                <p class="text-[10px] text-coffee-600 uppercase tracking-widest font-bold">Digital Menu & Ordering</p>
            </div>
            <div class="relative w-11 h-11 bg-white hover:bg-coffee-50 border border-coffee-100 rounded-xl flex items-center justify-center shadow-sm cursor-pointer transition-all active:scale-95" onclick="toggleCart()">
                <i class="fas fa-shopping-basket text-lg text-coffee"></i>
                <span id="cart-count" class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full text-[9px] font-bold px-1.5 py-0.5 border-2 border-white hidden">0</span>
            </div>
        </div>
        
        <div class="w-full max-w-md mx-auto flex items-center gap-3 px-4">
            <!-- Hidden Table Number -->
            <input type="hidden" id="table-number" value="">
            
            <!-- Table Number Display (Visible) -->
            <div id="table-display" class="bg-coffee-50 rounded-xl px-3 py-2.5 border border-coffee-100 shadow-sm flex items-center justify-center gap-1.5 hidden shrink-0">
                <i class="fas fa-chair text-coffee-500 text-[10px]"></i>
                <span id="table-number-text" class="text-[10px] font-bold text-coffee-950 truncate"></span>
            </div>
            
            <!-- Search Bar -->
            <div id="search-bar" class="flex-grow bg-white rounded-xl px-3 py-2.5 border border-coffee-100 shadow-sm flex items-center gap-2 min-w-0">
                <i class="fas fa-search text-coffee-400 text-xs flex-shrink-0"></i>
                <input type="text" id="search-input" oninput="searchMenu()" placeholder="Cari..." class="w-full bg-transparent focus:outline-none text-xs font-semibold text-coffee-900 min-w-0">
                <button type="button" onclick="startVoiceSearch()" id="voice-search-btn" class="text-coffee-400 hover:text-coffee transition-colors flex-shrink-0">
                    <i class="fas fa-microphone text-xs"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Categories Navigation -->
    <div class="w-full max-w-md mx-auto mb-6">
        <nav class="flex overflow-x-auto gap-2 no-scrollbar px-4 pb-2">
            <button onclick="filterCategory('all', this)" class="category-pill active shrink-0">Semua</button>
            <button onclick="filterCategory('Bakery', this)" class="category-pill shrink-0">Bakery</button>
            <button onclick="filterCategory('Minuman', this)" class="category-pill shrink-0">Minuman</button>
            <button onclick="filterCategory('Main Course', this)" class="category-pill shrink-0">Main Course</button>
            <button onclick="filterCategory('Paket', this)" class="category-pill shrink-0">Paket</button>
        </nav>
    </div>

    <!-- Menu List -->
    <div class="w-full max-w-md mx-auto px-4">
        <div id="menu-container" class="space-y-4">
            @foreach($menus as $menu)
            <div class="menu-item w-full" data-category="{{ $menu->category }}" data-barcode="{{ $menu->barcode }}">
                <div class="card p-3 flex gap-3 bg-white w-full rounded-2xl">
                    <div class="w-20 h-20 bg-coffee-50 border border-coffee-950/5 rounded-xl overflow-hidden flex-shrink-0 relative group">
                        @php
                            $imageUrl = $menu->image ?? 'https://placehold.co/200x200?text=No+Image';
                            if ($menu->image && !str_starts_with($menu->image, 'http')) {
                                $imageUrl = asset('storage/' . $menu->image);
                            }
                        @endphp
                        <img src="{{ $imageUrl }}" alt="{{ $menu->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                    </div>
                    <div class="flex flex-col justify-between flex-grow min-w-0">
                        <div class="space-y-0.5">
                            <div class="flex justify-between items-start">
                                <h3 class="font-serif font-bold text-coffee-950 text-sm leading-snug truncate">{{ $menu->name }}</h3>
                                <button onclick="speakText('{{ addslashes($menu->name) }}. {{ addslashes($menu->description) }}. Harga: {{ number_format($menu->price, 0, ',', '') }} Rupiah.')" class="text-coffee-300 hover:text-coffee transition-colors ml-1 flex-shrink-0" title="Dengarkan detail menu">
                                    <i class="fas fa-volume-up text-[10px] bg-coffee-50 p-1.5 rounded-full"></i>
                                </button>
                            </div>
                            <p class="text-[10px] text-coffee-700/80 leading-relaxed line-clamp-2">{{ $menu->description }}</p>
                        </div>
                        <div class="flex justify-between items-center mt-2">
                            <span class="font-serif font-bold text-coffee-800 text-xs truncate">Rp {{ number_format($menu->price, 0, ',', '.') }}</span>
                            <!-- Action Wrapper -->
                            <div class="menu-action-wrapper" 
                                 data-name="{{ $menu->name }}" 
                                 data-price="{{ $menu->price }}" 
                                 data-category="{{ $menu->category }}" 
                                 data-addons="{{ json_encode($menu->add_ons ?? []) }}">
                                <!-- Populated dynamically by updateMenuStepperUI() -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Floating Checkout Bottom Bar -->
    <div id="cart-footer" class="fixed bottom-4 left-4 right-4 max-w-md mx-auto z-40 hidden">
        <div class="bg-coffee-900/90 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-white/10 flex justify-between items-center animate-slide-up">
            <div class="text-white">
                <p class="text-[9px] uppercase tracking-widest text-coffee-200/80 font-bold">Total Pesanan</p>
                <p id="cart-total" class="font-serif text-lg font-bold text-coffee-100">Rp 0</p>
            </div>
            <button onclick="toggleCart()" class="bg-white hover:bg-coffee-50 text-coffee-950 px-6 py-3 rounded-xl font-bold text-xs transition-all shadow-md active:scale-95 cursor-pointer">
                Lanjut Pesan <i class="fas fa-arrow-right ml-2 text-[10px]"></i>
            </button>
        </div>
    </div>

    <!-- Cart Modal Bottom Sheet -->
    <div id="cart-modal" class="fixed inset-0 bg-coffee-950/40 z-[60] hidden flex items-end justify-center">
        <div class="bg-white w-full max-w-md rounded-t-[2.5rem] p-6 max-h-[90vh] overflow-y-auto bottom-sheet border-t border-coffee-100 shadow-2xl">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="font-serif text-xl font-bold text-coffee-950">Detail Pesanan</h2>
                    <p class="text-[10px] text-coffee-600 font-bold uppercase tracking-widest">CoolCafe Receipt</p>
                </div>
                <button onclick="toggleCart()" class="w-8 h-8 rounded-full bg-coffee-50 text-coffee-600 hover:bg-coffee-100 flex items-center justify-center cursor-pointer transition-colors"><i class="fas fa-times text-sm"></i></button>
            </div>
            
            <!-- Items list -->
            <div id="cart-items" class="space-y-3 mb-6"></div>

            <!-- Payment Method -->
            <div class="mb-6 bg-coffee-50/50 border border-coffee-100 rounded-2xl p-4">
                <p class="text-xs font-bold text-coffee-950 mb-3 uppercase tracking-wider">Metode Pembayaran</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment-method" value="Cash" class="peer hidden" checked>
                        <div class="border border-coffee-100 bg-white rounded-xl p-3.5 text-center transition-all peer-checked:border-coffee-500 peer-checked:bg-coffee-50 shadow-sm flex flex-col items-center justify-center gap-1.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-500 flex items-center justify-center text-sm shadow-inner"><i class="fas fa-money-bill-wave"></i></div>
                            <p class="text-xs font-bold text-coffee-900">Tunai / Cash</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="payment-method" value="QRIS" class="peer hidden">
                        <div class="border border-coffee-100 bg-white rounded-xl p-3.5 text-center transition-all peer-checked:border-coffee-500 peer-checked:bg-coffee-50 shadow-sm flex flex-col items-center justify-center gap-1.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center text-sm shadow-inner"><i class="fas fa-qrcode"></i></div>
                            <p class="text-xs font-bold text-coffee-900">QRIS Scan</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Order Note -->
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-coffee-950 uppercase tracking-wider">Catatan Pesanan (Opsional)</p>
                    <button type="button" onclick="startVoiceToText('order-note')" class="text-coffee-400 hover:text-coffee transition-colors flex items-center gap-1">
                        <i class="fas fa-microphone text-[10px]"></i>
                        <span class="text-[9px] font-bold">Dikte</span>
                    </button>
                </div>
                <textarea id="order-note" placeholder="Contoh: Minta sendok lebih, kopi manis..." class="w-full bg-coffee-50/30 rounded-xl p-3 text-xs border border-coffee-100 focus:border-coffee-400 focus:bg-white h-20 outline-none transition-all placeholder:text-coffee-300 font-semibold"></textarea>
            </div>

            <!-- Summary Price -->
            <div class="border-t border-coffee-100 pt-4">
                <div class="space-y-2.5 mb-6">
                    <div class="flex justify-between text-xs font-semibold text-coffee-750">
                        <span>Subtotal</span>
                        <span id="modal-subtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-xs font-semibold text-coffee-750">
                        <span>Pajak (12%)</span>
                        <span id="modal-tax">Rp 0</span>
                    </div>
                    <div class="flex justify-between font-serif font-bold text-lg text-coffee-950 pt-2.5 border-t border-dashed border-coffee-200">
                        <span>Total Akhir</span>
                        <span id="modal-total" class="text-coffee-800">Rp 0</span>
                    </div>
                </div>
                <button onclick="sendOrder()" class="w-full bg-coffee text-white hover:bg-coffee-dark py-4 rounded-2xl font-bold text-sm shadow-lg shadow-coffee-700/10 active:scale-98 transition-all cursor-pointer">
                    Kirim Pesanan Ke Dapur
                </button>
            </div>
        </div>
    </div>

    <!-- Beverage Customization Modal (Sweetness, Notes) -->
    <div id="custom-modal" class="fixed inset-0 bg-coffee-950/40 z-[70] hidden flex items-end justify-center">
        <div class="bg-white w-full max-w-md rounded-t-[2.5rem] p-6 shadow-2xl bottom-sheet border-t border-coffee-100" id="custom-sheet">
            <div class="flex justify-between items-center mb-6">
                <h2 id="custom-title" class="font-serif text-xl font-bold text-coffee-950">Kustomisasi Minuman</h2>
                <button onclick="closeCustomization()" class="w-8 h-8 rounded-full bg-coffee-50 text-coffee-600 hover:bg-coffee-100 flex items-center justify-center cursor-pointer transition-colors"><i class="fas fa-times text-sm"></i></button>
            </div>
            
            <div class="space-y-6 mb-8">
                <div>
                    <p class="text-xs font-bold text-coffee-950 mb-3 uppercase tracking-wider">Tingkat Kemanisan</p>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="sweetness" value="Normal" class="peer hidden" checked>
                            <span class="px-3 py-2.5 rounded-xl border border-coffee-100 text-xs font-semibold text-coffee-800 peer-checked:bg-coffee peer-checked:text-white peer-checked:border-coffee transition-all text-center block bg-white">Normal</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="sweetness" value="Less Sugar" class="peer hidden">
                            <span class="px-3 py-2.5 rounded-xl border border-coffee-100 text-xs font-semibold text-coffee-800 peer-checked:bg-coffee peer-checked:text-white peer-checked:border-coffee transition-all text-center block bg-white">Less Sugar</span>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="sweetness" value="No Sugar" class="peer hidden">
                            <span class="px-3 py-2.5 rounded-xl border border-coffee-100 text-xs font-semibold text-coffee-800 peer-checked:bg-coffee peer-checked:text-white peer-checked:border-coffee transition-all text-center block bg-white">No Sugar</span>
                        </label>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-xs font-bold text-coffee-950 uppercase tracking-wider">Catatan Item</p>
                        <button type="button" onclick="startVoiceToText('custom-note')" class="text-coffee-400 hover:text-coffee transition-colors flex items-center gap-1">
                            <i class="fas fa-microphone text-[10px]"></i>
                            <span class="text-[9px] font-bold">Dikte</span>
                        </button>
                    </div>
                    <textarea id="custom-note" placeholder="Contoh: Es sedikit, susu ganti oats..." class="w-full bg-coffee-50/30 rounded-xl p-3 text-xs border border-coffee-100 focus:border-coffee-400 focus:bg-white h-20 outline-none transition-all placeholder:text-coffee-300 font-semibold"></textarea>
                </div>
            </div>
            <button onclick="confirmCustomization()" class="w-full bg-coffee text-white hover:bg-coffee-dark py-4 rounded-2xl font-bold text-sm shadow-md transition-colors cursor-pointer">Tambah Minuman</button>
        </div>
    </div>

    <!-- Package Add-on Modal -->
    <div id="package-modal" class="fixed inset-0 bg-coffee-950/40 z-[70] hidden flex items-end justify-center">
        <div class="bg-white w-full max-w-md rounded-t-[2.5rem] p-6 shadow-2xl bottom-sheet border-t border-coffee-100" id="package-sheet">
            <div class="flex items-start justify-between gap-4 mb-6">
                <div>
                    <h2 id="package-title" class="font-serif text-xl font-bold text-coffee-950">Paket Hemat</h2>
                    <p id="package-base-price" class="text-sm font-serif font-bold text-coffee-700 mt-1">Rp 0</p>
                </div>
                <button onclick="closePackageCustomization()" class="w-8 h-8 rounded-full bg-coffee-50 text-coffee-600 hover:bg-coffee-100 flex items-center justify-center cursor-pointer transition-colors"><i class="fas fa-times text-sm"></i></button>
            </div>
            
            <div class="mb-6">
                <p class="text-xs font-bold text-coffee-950 mb-3 uppercase tracking-wider">Pilihan Add-on</p>
                <div id="package-add-ons" class="space-y-2"></div>
            </div>
            
            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <p class="text-xs font-bold text-coffee-950 uppercase tracking-wider">Catatan Paket</p>
                    <button type="button" onclick="startVoiceToText('package-note')" class="text-coffee-400 hover:text-coffee transition-colors flex items-center gap-1">
                        <i class="fas fa-microphone text-[10px]"></i>
                        <span class="text-[9px] font-bold">Dikte</span>
                    </button>
                </div>
                <textarea id="package-note" placeholder="Contoh: kopi hangat, ekstra saos..." class="w-full bg-coffee-50/30 rounded-xl p-3 text-xs border border-coffee-100 focus:border-coffee-400 focus:bg-white h-20 outline-none transition-all placeholder:text-coffee-300 font-semibold"></textarea>
            </div>
            <button onclick="confirmPackageCustomization()" class="w-full bg-coffee text-white hover:bg-coffee-dark py-4 rounded-2xl font-bold text-sm shadow-md transition-all cursor-pointer">Tambah Paket</button>
        </div>
    </div>

    <!-- Script Section -->
    <script>
        let cart = [];
        let currentCustomItem = null;
        let currentPackageItem = null;
        let toastTimer = null;

        document.addEventListener('DOMContentLoaded', () => {
            // Get table from URL parameter
            const urlParams = new URLSearchParams(window.location.search);
            const table = urlParams.get('table');
            
            if (table) {
                document.getElementById('table-number').value = table;
                document.getElementById('table-number-text').innerText = 'Meja ' + table;
                document.getElementById('table-display').classList.remove('hidden');
            } else {
                // Optionally show a warning if no table is found
                showToast('Nomor meja tidak ditemukan. Mohon scan ulang QR code.', 'error');
            }

            updateMenuStepperUI();
            
            // Listen to backdrop clicks on sheets
            const sheets = ['cart-modal', 'custom-modal', 'package-modal'];
            sheets.forEach(id => {
                const el = document.getElementById(id);
                el.addEventListener('click', (e) => {
                    if (e.target === el) {
                        if (id === 'cart-modal') toggleCart();
                        if (id === 'custom-modal') closeCustomization();
                        if (id === 'package-modal') closePackageCustomization();
                    }
                });
            });
        });

        function speakText(text) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'id-ID';
                utterance.rate = 0.9; // Slightly slower for clarity
                utterance.pitch = 1;
                window.speechSynthesis.speak(utterance);
            } else {
                showToast('Browser Anda tidak mendukung fitur suara.', 'error');
            }
        }

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
                    resolve(result);
                };
                const onOk = () => close(true);
                const onCancel = () => close(false);

                okButton.addEventListener('click', onOk, { once: true });
                cancelButton.addEventListener('click', onCancel, { once: true });
            });
        }

        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toast-icon');
            const bg = document.getElementById('toast-icon-bg');
            document.getElementById('toast-message').innerText = message;

            toast.classList.toggle('error', type === 'error');
            if (type === 'error') {
                icon.className = 'fas fa-exclamation-circle text-red-500';
                bg.className = 'w-6 h-6 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0';
            } else {
                icon.className = 'fas fa-check text-coffee-800';
                bg.className = 'w-6 h-6 rounded-full bg-coffee-100 flex items-center justify-center flex-shrink-0';
            }

            clearTimeout(toastTimer);
            toast.classList.add('show');
            toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
        }

        function addToCart(name, price, options = null) {
            const existing = cart.find(item => item.name === name && JSON.stringify(item.options) === JSON.stringify(options));
            if (existing) { 
                existing.quantity += 1; 
            } else { 
                cart.push({ name, price, quantity: 1, options }); 
            }
            updateCartUI();
        }

        function removeFromCart(name, options = null) {
            const idx = cart.findIndex(item => item.name === name && JSON.stringify(item.options) === JSON.stringify(options));
            if (idx > -1) { 
                if (cart[idx].quantity > 1) { 
                    cart[idx].quantity -= 1; 
                } else { 
                    cart.splice(idx, 1); 
                } 
            }
            updateCartUI();
        }

        function openCustomization(name, price) {
            currentCustomItem = { name, price };
            document.getElementById('custom-title').innerText = name;
            document.getElementById('custom-note').value = '';
            // Reset radio option
            document.querySelector('input[name="sweetness"][value="Normal"]').checked = true;
            
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
            container.innerHTML = addOns.length ? '' : '<p class="text-xs text-coffee-400 bg-coffee-50/50 rounded-xl p-4">Paket ini tidak memerlukan add-on.</p>';
            addOns.forEach((addOn, index) => {
                const row = document.createElement('label');
                row.className = 'flex items-center justify-between gap-4 bg-coffee-50/30 border border-coffee-100 rounded-xl p-3 cursor-pointer select-none';
                row.innerHTML = `
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="package-add-on rounded border-coffee-200 text-coffee focus:ring-coffee" data-index="${index}">
                        <div>
                            <p class="text-xs font-bold text-coffee-950">${addOn.name}</p>
                            <p class="text-[10px] text-coffee-600 font-semibold">+ Rp ${Number(addOn.price || 0).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                    <i class="fas fa-plus text-coffee-400 text-xs"></i>
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
            container.innerHTML = cart.length ? '' : '<p class="text-center py-12 text-xs font-bold text-coffee-300">Keranjang pesanan masih kosong</p>';
            cart.forEach(item => {
                const opt = item.options ? `<p class="text-[10px] text-coffee-500 mt-1 font-semibold">${formatOptions(item.options)}</p>` : '';
                const div = document.createElement('div');
                div.className = 'flex justify-between items-center bg-coffee-50/40 border border-coffee-100 p-3.5 rounded-2xl';
                div.innerHTML = `
                    <div class="flex-grow pr-4">
                        <p class="font-bold text-xs text-coffee-950">${item.name}</p>
                        ${opt}
                        <p class="text-[10px] text-coffee-600 font-bold mt-1">Rp ${Number(item.price).toLocaleString('id-ID')}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button onclick="removeFromCart('${item.name}', ${item.options ? JSON.stringify(item.options).replace(/"/g, '&quot;') : 'null'})" class="w-8 h-8 rounded-xl border border-coffee-200 bg-white text-coffee-800 flex items-center justify-center font-bold text-xs cursor-pointer active:scale-90 transition-transform">-</button>
                        <span class="text-xs font-bold text-coffee-950 w-4 text-center">${item.quantity}</span>
                        <button onclick="addToCart('${item.name}', ${item.price}, ${item.options ? JSON.stringify(item.options).replace(/"/g, '&quot;') : 'null'})" class="w-8 h-8 rounded-xl bg-coffee text-white flex items-center justify-center font-bold text-xs cursor-pointer active:scale-90 transition-transform">+</button>
                    </div>
                `;
                container.appendChild(div);
            });

            updateMenuStepperUI();
        }

        function updateMenuStepperUI() {
            document.querySelectorAll('.menu-action-wrapper').forEach(wrapper => {
                const name = wrapper.dataset.name;
                const price = Number(wrapper.dataset.price);
                const category = wrapper.dataset.category;
                const addOns = JSON.parse(wrapper.dataset.addons || '[]');
                
                const isBeverage = (category === 'Minuman' || category === 'Beverages');
                const isPackage = (category === 'Paket');
                
                if (!isBeverage && !isPackage) {
                    // Simple item
                    const cartItem = cart.find(item => item.name === name && item.options === null);
                    if (cartItem && cartItem.quantity > 0) {
                        wrapper.innerHTML = `
                            <div class="flex items-center gap-2 bg-coffee-50 border border-coffee-200 rounded-xl p-1">
                                <button onclick="event.stopPropagation(); removeFromCart('${name}', null)" class="w-7 h-7 rounded-lg bg-white border border-coffee-200 text-coffee-800 flex items-center justify-center font-bold text-xs active:scale-90 transition-transform cursor-pointer">-</button>
                                <span class="text-xs font-bold text-coffee-950 px-1 min-w-[16px] text-center">${cartItem.quantity}</span>
                                <button onclick="event.stopPropagation(); addToCart('${name}', ${price}, null)" class="w-7 h-7 rounded-lg bg-coffee text-white flex items-center justify-center font-bold text-xs active:scale-90 transition-transform cursor-pointer">+</button>
                            </div>
                        `;
                    } else {
                        wrapper.innerHTML = `
                            <button onclick="addToCart('${name}', ${price})" class="bg-coffee hover:bg-coffee-dark text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">Tambah</button>
                        `;
                    }
                } else if (isBeverage) {
                    // Beverage item
                    const totalQty = cart.filter(item => item.name === name).reduce((sum, item) => sum + item.quantity, 0);
                    if (totalQty > 0) {
                        wrapper.innerHTML = `
                            <button onclick="openCustomization('${name}', ${price})" class="bg-coffee-500 hover:bg-coffee-600 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer flex items-center gap-1.5">
                                <span>Kustom</span>
                                <span class="bg-white text-coffee-700 rounded-full px-1.5 py-0.5 text-[9px] font-bold">${totalQty}</span>
                            </button>
                        `;
                    } else {
                        wrapper.innerHTML = `
                            <button onclick="openCustomization('${name}', ${price})" class="bg-coffee hover:bg-coffee-dark text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">Tambah</button>
                        `;
                    }
                } else if (isPackage) {
                    // Package item
                    const totalQty = cart.filter(item => item.name === name).reduce((sum, item) => sum + item.quantity, 0);
                    const pkgData = { name, price, add_ons: addOns };
                    if (totalQty > 0) {
                        wrapper.innerHTML = `
                            <button onclick='openPackageCustomization(${JSON.stringify(pkgData)})' class="bg-coffee-500 hover:bg-coffee-600 text-white px-3 py-2 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer flex items-center gap-1.5">
                                <span>Kustom</span>
                                <span class="bg-white text-coffee-700 rounded-full px-1.5 py-0.5 text-[9px] font-bold">${totalQty}</span>
                            </button>
                        `;
                    } else {
                        wrapper.innerHTML = `
                            <button onclick='openPackageCustomization(${JSON.stringify(pkgData)})' class="bg-coffee hover:bg-coffee-dark text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95 cursor-pointer">Tambah</button>
                        `;
                    }
                }
            });
        }

        function formatOptions(options) {
            if (options.type === 'Paket') {
                const addOns = (options.addOns || []).map(addOn => addOn.name).join(', ');
                return `${addOns ? 'Add-on: ' + addOns : 'Tanpa add-on'}${options.note ? ' | ' + options.note : ''}`;
            }
            return `${options.sweetness || 'Normal'}${options.note ? ' | ' + options.note : ''}`;
        }

        function toggleCart() { 
            document.getElementById('cart-modal').classList.toggle('hidden'); 
        }

        function filterCategory(cat, btn) {
            document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
            btn.classList.add('active');
            
            const query = document.getElementById('search-input').value.toLowerCase();
            
            document.querySelectorAll('.menu-item').forEach(item => {
                const name = item.querySelector('h3').innerText.toLowerCase();
                const desc = item.querySelector('p').innerText.toLowerCase();
                const itemCat = item.dataset.category;
                const matchesSearch = name.includes(query) || desc.includes(query);
                
                let matchesCategory = false;
                if (cat === 'all') {
                    matchesCategory = true;
                } else if (cat === 'Minuman' || cat === 'Beverages') {
                    matchesCategory = (itemCat === 'Minuman' || itemCat === 'Beverages');
                } else {
                    matchesCategory = (itemCat === cat);
                }
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function searchMenu() {
            // Find current active category pill
            const activePill = document.querySelector('.category-pill.active');
            const activeCatText = activePill ? activePill.innerText : 'Semua';
            
            let cat = 'all';
            if (activeCatText === 'Bakery') cat = 'Bakery';
            else if (activeCatText === 'Minuman') cat = 'Minuman';
            else if (activeCatText === 'Main Course') cat = 'Main Course';
            else if (activeCatText === 'Paket') cat = 'Paket';
            
            const query = document.getElementById('search-input').value.toLowerCase();
            
            document.querySelectorAll('.menu-item').forEach(item => {
                const name = item.querySelector('h3').innerText.toLowerCase();
                const desc = item.querySelector('p').innerText.toLowerCase();
                const itemCat = item.dataset.category;
                const matchesSearch = name.includes(query) || desc.includes(query);
                
                let matchesCategory = false;
                if (cat === 'all') {
                    matchesCategory = true;
                } else if (cat === 'Minuman' || cat === 'Beverages') {
                    matchesCategory = (itemCat === 'Minuman' || itemCat === 'Beverages');
                } else {
                    matchesCategory = (itemCat === cat);
                }
                
                if (matchesSearch && matchesCategory) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function startVoiceSearch() {
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                return showToast('Browser Anda tidak mendukung pencarian suara.', 'error');
            }

            const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new Recognition();
            const btn = document.getElementById('voice-search-btn');
            const icon = btn.querySelector('i');

            recognition.lang = 'id-ID';
            recognition.interimResults = false;
            recognition.maxAlternatives = 1;

            recognition.onstart = () => {
                icon.className = 'fas fa-microphone text-red-500 animate-pulse';
                showToast('Mendengarkan...', 'info');
            };

            recognition.onerror = (event) => {
                icon.className = 'fas fa-microphone text-coffee-400';
                console.error('Speech recognition error', event.error);
                showToast('Gagal mendengarkan suara.', 'error');
            };

            recognition.onend = () => {
                icon.className = 'fas fa-microphone text-coffee-400';
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                const input = document.getElementById('search-input');
                input.value = transcript;
                searchMenu();
                showToast(`Mencari: "${transcript}"`);
            };

            recognition.start();
        }

        function startVoiceToText(targetId) {
            if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
                return showToast('Browser Anda tidak mendukung dikte suara.', 'error');
            }

            const Recognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new Recognition();

            recognition.lang = 'id-ID';
            recognition.interimResults = false;

            recognition.onstart = () => {
                showToast('Mendengarkan catatan...', 'info');
            };

            recognition.onerror = (event) => {
                console.error('Speech recognition error', event.error);
                showToast('Gagal mendengarkan suara.', 'error');
            };

            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                const textarea = document.getElementById(targetId);
                textarea.value = (textarea.value ? textarea.value + ' ' : '') + transcript;
                showToast('Catatan ditambahkan!');
            };

            recognition.start();
        }

        async function sendOrder() {
            const table = document.getElementById('table-number').value;
            if (!table) return showToast('Nomor meja harus diisi.', 'error');
            
            const payment = document.querySelector('input[name="payment-method"]:checked').value;
            const orderNote = document.getElementById('order-note').value;
            const subtotal = cart.reduce((a, b) => a + (b.price * b.quantity), 0);
            const tax = Math.round(subtotal * 0.12);
            const total = subtotal + tax;
            
            let summary = `Meja: ${table}\nPembayaran: ${payment}\n${orderNote ? 'Catatan: ' + orderNote + '\n' : ''}--------------------------------\n`;
            cart.forEach(i => summary += `${i.name} x${i.quantity}${i.options ? '\n   (' + formatOptions(i.options) + ')' : ''}\n`);
            summary += `--------------------------------\nSubtotal: Rp ${subtotal.toLocaleString('id-ID')}\nPajak (12%): Rp ${tax.toLocaleString('id-ID')}\nTotal Akhir: Rp ${total.toLocaleString('id-ID')}`;
            
            if (await showConfirm({
                title: 'Kirim pesanan?',
                message: 'Pesanan akan langsung dikirim ke kasir dan dapur. Silakan periksa kembali detail pesanan Anda.',
                detail: summary,
                okText: 'Kirim Pesanan'
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

                    showToast(payment === 'QRIS' ? 'Menyiapkan QRIS...' : 'Pesanan berhasil dikirim!');
                    const tableNum = table; 
                    cart = []; 
                    updateCartUI(); 
                    toggleCart();
                    
                    // Keep table number persistent for the session
                    document.getElementById('table-number').value = tableNum;
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
                        setTimeout(() => {
                            window.location.href = `/estimation?table=${tableNum}`;
                        }, 800);
                    }
                } catch (error) {
                    showToast('Gagal mengirim pesanan. Silakan coba lagi.', 'error');
                    console.error(error);
                }
            }
        }
    </script>
</body>
</html>
