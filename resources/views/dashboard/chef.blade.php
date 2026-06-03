@extends('layouts.app')

@section('title', 'Chef Dashboard')

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
</style>
@endpush

@section('content')
<div id="toast" class="toast">
    <div class="flex items-start gap-3">
        <i class="fas fa-check-circle text-green-500 mt-1"></i>
        <p id="toast-message" class="text-sm font-semibold"></p>
    </div>
</div>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Dapur CoolCafe</h2>
        <p class="text-gray-500 text-sm">Kelola antrean pesanan yang harus dimasak.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="bg-amber-100 text-amber-700 px-4 py-2 rounded-xl text-xs font-bold">
            <i class="fas fa-fire-burner mr-2"></i>Kitchen Mode
        </span>
    </div>
</div>

<section id="orders-section">
    <div id="orders-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Order Cards will appear here -->
    </div>

    <div id="empty-state" class="hidden flex flex-col items-center justify-center py-20 text-gray-300">
        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
            <i class="fas fa-utensils text-4xl"></i>
        </div>
        <p class="text-lg font-medium text-gray-400">Belum ada pesanan untuk dimasak</p>
    </div>
</section>
@endsection

@push('scripts')
<script>
    let toastTimer = null;

    function showToast(message) {
        const toast = document.getElementById('toast');
        document.getElementById('toast-message').innerText = message;

        clearTimeout(toastTimer);
        toast.classList.add('show');
        toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
    }

    const orderCards = new Map();

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

            // Remove cards that are no longer in the list
            for (const [id, element] of orderCards.entries()) {
                if (!currentIds.has(id)) {
                    element.remove();
                    orderCards.delete(id);
                }
            }

            // Update or add cards
            orders.forEach((order) => {
                const id = String(order.id);
                const isReady = order.status === 'ready';
                const statusJson = JSON.stringify({ status: order.status, items: (order.items || []).length });
                
                let card = orderCards.get(id);
                
                if (card) {
                    // Only update if status/data changed
                    if (card.dataset.state === statusJson) return;
                    card.dataset.state = statusJson;
                } else {
                    card = document.createElement('div');
                    card.className = 'order-card card !p-0 overflow-hidden';
                    card.id = `order-${id}`;
                    grid.appendChild(card);
                    orderCards.set(id, card);
                }

                card.dataset.state = statusJson;
                card.className = `order-card card !p-0 overflow-hidden transition-all duration-300 ${isReady ? 'opacity-60 grayscale-[0.5]' : ''}`;
                
                let itemsHtml = (order.items || []).map(item => `
                    <div class="flex justify-between items-start py-2 border-b border-gray-50 last:border-0">
                        <div>
                            <p class="text-sm font-bold text-gray-800">${item.name} <span class="text-coffee">x${item.quantity}</span></p>
                            ${item.options ? `<p class="text-[10px] text-gray-400">(${formatItemOptions(item.options)})</p>` : ''}
                        </div>
                    </div>
                `).join('');

                card.innerHTML = `
                    <div class="p-5">
                        <div class="flex justify-between items-center mb-4">
                            <span class="bg-coffee text-white px-4 py-1.5 rounded-xl font-bold text-[10px] shadow-sm">Meja ${order.table}</span>
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
                            ${isReady ? `
                                <div class="bg-green-50 text-green-600 p-3 rounded-xl flex items-center justify-center gap-2 font-bold text-xs">
                                    <i class="fas fa-check-double"></i> Sudah Siap
                                </div>
                            ` : `
                                <button onclick="markReady('${order.id}')" class="w-full bg-green-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-green-700 transition-all shadow-lg shadow-green-100">
                                    <i class="fas fa-concierge-bell mr-2"></i> Pesanan Siap!
                                </button>
                            `}
                        </div>
                    </div>
                `;
            });
        } catch (error) {
            console.error('Error loading orders:', error);
        }
    }

    async function markReady(id) {
        const response = await fetch(`/orders/${id}/ready`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            showToast('Notifikasi dikirim ke kasir!');
            loadOrders();
        }
    }

    function formatItemOptions(options) {
        if (options.type === 'Paket') {
            const addOns = (options.addOns || []).map(addOn => addOn.name).join(', ');
            return `${addOns ? 'Add-on: ' + addOns : 'Tanpa add-on'}${options.note ? ', ' + options.note : ''}`;
        }

        return `${options.sweetness || ''}${options.note ? ', ' + options.note : ''}`;
    }

    loadOrders();
    setInterval(loadOrders, 5000);
</script>
@endpush
