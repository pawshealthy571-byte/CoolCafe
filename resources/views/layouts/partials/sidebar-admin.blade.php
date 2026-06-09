<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Utama</p>
    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('dashboard*') && !Request::is('admin/management/*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-chart-pie w-5"></i>
        <span class="text-sm">Dashboard</span>
    </a>
</div>

<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Manajemen</p>
    <a href="/admin/management/tables" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/tables') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-qrcode w-5"></i>
        <span class="text-sm">Kelola Meja & QR</span>
    </a>
    <a href="/admin/management/menus" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/menus') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-list-check w-5"></i>
        <span class="text-sm">Kelola Menu</span>
    </a>
    <a href="/admin/management/ingredients" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/ingredients') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-box-open w-5"></i>
        <span class="text-sm">Kelola Bahan Baku</span>
    </a>
    <a href="/admin/management/reports" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/reports') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-chart-line w-5"></i>
        <span class="text-sm">Laporan Keuangan</span>
    </a>
</div>

<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Manajemen Akun</p>
    <a href="/admin/management/users" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/users*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-users-gear w-5"></i>
        <span class="text-sm">Kelola Pengguna</span>
    </a>
    <a href="/admin/management/vouchers" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/vouchers*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-ticket w-5"></i>
        <span class="text-sm">Kelola Voucher</span>
    </a>
</div>
