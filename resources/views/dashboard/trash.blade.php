@extends('layouts.app')

@section('title', 'Keranjang Sampah')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Keranjang Sampah</h2>
    <p class="text-gray-500 text-sm">Kelola data yang telah dihapus sementara (Soft Delete). Anda dapat memulihkannya kembali atau menghapusnya secara permanen.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Menu Terhapus -->
    <div class="card !p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-red-50/30">
            <h3 class="font-bold text-red-600 flex items-center gap-2">
                <i class="fas fa-list-check"></i> Menu Terhapus
            </h3>
        </div>
        <div class="p-4 space-y-3 max-h-[400px] overflow-y-auto" id="trash-menus">
            <p class="text-center text-xs text-gray-400 py-4">Memuat...</p>
        </div>
    </div>

    <!-- Pengguna Terhapus -->
    <div class="card !p-0 overflow-hidden">
        <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-red-50/30">
            <h3 class="font-bold text-red-600 flex items-center gap-2">
                <i class="fas fa-users"></i> Pengguna Terhapus
            </h3>
        </div>
        <div class="p-4 space-y-3 max-h-[400px] overflow-y-auto" id="trash-users">
            <p class="text-center text-xs text-gray-400 py-4">Memuat...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    async function loadTrash() {
        try {
            const res = await fetch('/api/admin/trash');
            const data = await res.json();
            
            const menusContainer = document.getElementById('trash-menus');
            if (data.menus && data.menus.length > 0) {
                menusContainer.innerHTML = data.menus.map(menu => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800 text-xs">${menu.name}</p>
                            <p class="text-[10px] text-gray-400">Dihapus pada: ${new Date(menu.deleted_at).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="restoreItem('menu', ${menu.id})" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-[10px] font-bold transition-colors">Restore</button>
                            <button onclick="forceDeleteItem('menu', ${menu.id})" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-[10px] font-bold transition-colors">Hapus Permanen</button>
                        </div>
                    </div>
                `).join('');
            } else {
                menusContainer.innerHTML = '<p class="text-center text-xs text-gray-400 py-4">Tidak ada menu di keranjang sampah.</p>';
            }

            const usersContainer = document.getElementById('trash-users');
            if (data.users && data.users.length > 0) {
                usersContainer.innerHTML = data.users.map(user => `
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div>
                            <p class="font-bold text-gray-800 text-xs">${user.name}</p>
                            <p class="text-[10px] text-gray-400">Dihapus pada: ${new Date(user.deleted_at).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="restoreItem('user', ${user.id})" class="px-3 py-1.5 bg-green-50 text-green-600 hover:bg-green-100 rounded-lg text-[10px] font-bold transition-colors">Restore</button>
                            <button onclick="forceDeleteItem('user', ${user.id})" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-[10px] font-bold transition-colors">Hapus Permanen</button>
                        </div>
                    </div>
                `).join('');
            } else {
                usersContainer.innerHTML = '<p class="text-center text-xs text-gray-400 py-4">Tidak ada pengguna di keranjang sampah.</p>';
            }
            
        } catch (e) {
            console.error(e);
            window.showToast('Gagal memuat data sampah.', 'error');
        }
    }

    async function restoreItem(type, id) {
        if (!await window.showConfirm({title: 'Pulihkan Data?', message: 'Data ini akan kembali aktif dalam sistem.'})) return;
        
        try {
            const res = await fetch('/api/admin/trash/restore', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type, id })
            });
            
            if (res.ok) {
                window.showToast('Data berhasil dipulihkan!');
                loadTrash();
            } else {
                window.showToast('Gagal memulihkan data.', 'error');
            }
        } catch(e) {
            console.error(e);
        }
    }

    async function forceDeleteItem(type, id) {
        if (!await window.showConfirm({title: 'Hapus Permanen?', message: 'PERINGATAN: Data ini akan dihapus selamanya dari database dan tidak dapat dikembalikan!', okText: 'YA, HAPUS PERMANEN'})) return;
        
        try {
            const res = await fetch('/api/admin/trash/force-delete', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ type, id })
            });
            
            if (res.ok) {
                window.showToast('Data berhasil dihapus permanen!');
                loadTrash();
            } else {
                window.showToast('Gagal menghapus data.', 'error');
            }
        } catch(e) {
            console.error(e);
        }
    }

    loadTrash();
</script>
@endpush
@endsection
