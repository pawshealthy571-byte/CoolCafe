<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Kasir</p>
    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('dashboard*') || Request::is('cashier*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-cash-register w-5"></i>
        <span class="text-sm">Monitor Pesanan</span>
    </a>
</div>
