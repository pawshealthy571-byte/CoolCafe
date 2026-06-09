@php
    $isAdminOrSuperAdmin = in_array(Auth::user()->role, ['admin', 'superadmin']);
@endphp

<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Utama</p>
    <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('dashboard*') && !Request::is('admin/management/*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-chart-pie w-5"></i>
        <span class="text-sm">Dashboard</span>
    </a>
</div>

<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Manajemen</p>
    @if($isAdminOrSuperAdmin)
    <a href="/admin/management/tables" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/tables') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-qrcode w-5"></i>
        <span class="text-sm">Kelola Meja & QR</span>
    </a>
    @endif
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
    <a href="/admin/management/vouchers" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/vouchers*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-ticket w-5"></i>
        <span class="text-sm">Kelola Voucher</span>
    </a>
</div>

@if($isAdminOrSuperAdmin)
<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Manajemen Akun</p>
    <a href="/admin/management/users" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/users*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-users-gear w-5"></i>
        <span class="text-sm">Kelola Pengguna</span>
    </a>
</div>

<div class="mb-4 px-2">
    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Sistem</p>
    <a href="/admin/management/activity-logs" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/activity-logs*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-history w-5"></i>
        <span class="text-sm">Riwayat Aktivitas</span>
    </a>
    <a href="/admin/management/trash" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/trash*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-trash-can w-5"></i>
        <span class="text-sm">Keranjang Sampah</span>
    </a>
    <a href="/admin/management/backup" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/backup*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
        <i class="fas fa-database w-5"></i>
        <span class="text-sm">Backup Data</span>
    </a>
</div>
@endif
