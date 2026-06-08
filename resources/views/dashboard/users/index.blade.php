@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna</h2>
        <p class="text-gray-500 text-sm">Kelola akun dan hak akses sistem CoolCafe dalam satu panel.</p>
    </div>
    <div class="flex items-center gap-3 w-full sm:w-auto">
        <!-- Role Filter Dropdown -->
        <select id="filter-role" onchange="loadUsers()" class="bg-white border border-gray-100 rounded-xl px-4 py-3 text-xs font-bold text-gray-600 outline-none shadow-sm cursor-pointer">
            <option value="">Semua Role</option>
            <option value="admin">Admin</option>
            <option value="chef">Chef</option>
            <option value="cashier">Kasir</option>
            <option value="manager">Manager</option>
            <option value="superadmin">Super Admin</option>
        </select>
        
        <button onclick="openUserModal()" class="btn-primary !text-xs whitespace-nowrap">
            <i class="fas fa-user-plus mr-2"></i>Tambah Pengguna
        </button>
    </div>
</div>

<div class="card !p-0 overflow-hidden border-none shadow-md">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
                <tr>
                    <th class="text-left px-6 py-5">Nama</th>
                    <th class="text-left px-6 py-5">Email</th>
                    <th class="text-left px-6 py-5">Role</th>
                    <th class="text-right px-6 py-5">Aksi</th>
                </tr>
            </thead>
            <tbody id="user-table-body" class="divide-y divide-gray-50">
                <!-- Data loaded via JS -->
            </tbody>
        </table>
    </div>
</div>

<!-- User Modal -->
<div id="user-modal" class="fixed inset-0 bg-black/50 z-[100] hidden flex items-center justify-center p-4 backdrop-blur-sm">
    <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 id="modal-title" class="font-bold text-gray-800">Tambah Pengguna</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="user-form" class="p-6 space-y-4">
            <input type="hidden" id="user-id">
            
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
                <input type="text" id="user-name" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-coffee/30">
            </div>
            
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Email</label>
                <input type="email" id="user-email" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-coffee/30">
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Role / Hak Akses</label>
                <select id="user-role" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-coffee/30 cursor-pointer">
                    <option value="admin">Admin</option>
                    <option value="chef">Chef</option>
                    <option value="cashier">Kasir</option>
                    <option value="manager">Manager</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>
            
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                <input type="password" id="user-password" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-coffee/30" placeholder="Minimal 8 karakter">
                <p id="password-hint" class="text-[9px] text-gray-400 mt-1 ml-1 hidden">Kosongkan jika tidak ingin mengubah password.</p>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-4">
                <button type="button" onclick="closeUserModal()" class="bg-gray-100 text-gray-700 py-3 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="bg-coffee text-white py-3 rounded-xl font-bold text-sm shadow-lg shadow-coffee/20">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let users = [];

    async function loadUsers() {
        try {
            const roleFilter = document.getElementById('filter-role').value;
            const url = roleFilter ? `/admin/users?role=${roleFilter}` : '/admin/users';
            
            const response = await fetch(url);
            users = await response.json();
            renderTable();
        } catch (error) {
            console.error('Error loading users:', error);
            window.showToast('Gagal memuat data pengguna.', 'error');
        }
    }

    function getRoleBadgeClass(role) {
        switch (role.toLowerCase()) {
            case 'superadmin':
                return 'bg-rose-50 text-rose-600 border border-rose-100';
            case 'admin':
                return 'bg-blue-50 text-blue-600 border border-blue-100';
            case 'chef':
                return 'bg-amber-50 text-amber-600 border border-amber-100';
            case 'cashier':
                return 'bg-green-50 text-green-600 border border-green-100';
            case 'manager':
                return 'bg-purple-50 text-purple-600 border border-purple-100';
            default:
                return 'bg-gray-50 text-gray-600 border border-gray-100';
        }
    }

    function renderTable() {
        const tbody = document.getElementById('user-table-body');
        tbody.innerHTML = users.length ? '' : '<tr><td colspan="4" class="px-6 py-10 text-center text-gray-400">Belum ada data pengguna.</td></tr>';

        users.forEach(user => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors';
            tr.innerHTML = `
                <td class="px-6 py-4 font-bold text-gray-800 text-xs">${user.name}</td>
                <td class="px-6 py-4 text-gray-500 text-xs">${user.email}</td>
                <td class="px-6 py-4">
                    <span class="${getRoleBadgeClass(user.role)} px-2 py-1 rounded-lg text-[10px] font-bold uppercase">${user.role}</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="openUserModal(${user.id})" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-coffee hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-edit text-[10px]"></i>
                        </button>
                        <button onclick="deleteUser(${user.id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openUserModal(id = null) {
        const modal = document.getElementById('user-modal');
        const form = document.getElementById('user-form');
        const title = document.getElementById('modal-title');
        const hint = document.getElementById('password-hint');
        const passInput = document.getElementById('user-password');

        form.reset();
        document.getElementById('user-id').value = id || '';
        title.innerText = id ? 'Edit Pengguna' : 'Tambah Pengguna Baru';
        hint.classList.toggle('hidden', !id);
        passInput.required = !id;

        if (id) {
            const user = users.find(u => u.id === id);
            if (user) {
                document.getElementById('user-name').value = user.name;
                document.getElementById('user-email').value = user.email;
                document.getElementById('user-role').value = user.role;
            }
        }

        modal.classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('user-modal').classList.add('hidden');
    }

    document.getElementById('user-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('user-id').value;
        const data = {
            name: document.getElementById('user-name').value,
            email: document.getElementById('user-email').value,
            role: document.getElementById('user-role').value,
            password: document.getElementById('user-password').value,
        };

        // Don't submit blank password on edit
        if (id && !data.password) {
            delete data.password;
        }

        const url = id ? `/admin/users/${id}` : '/admin/users';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(data)
            });

            if (response.ok) {
                window.showToast(id ? 'Pengguna berhasil diperbarui!' : 'Pengguna baru berhasil ditambahkan!');
                closeUserModal();
                loadUsers();
            } else {
                const result = await response.json();
                window.showToast(result.message || 'Gagal menyimpan data.', 'error');
            }
        } catch (error) {
            console.error('Error saving user:', error);
            window.showToast('Terjadi kesalahan sistem.', 'error');
        }
    });

    async function deleteUser(id) {
        const confirmed = await window.showConfirm({
            title: 'Hapus Pengguna?',
            message: 'Akun ini akan dihapus secara permanen dari sistem. Lanjutkan?',
            okText: 'YA, HAPUS'
        });

        if (confirmed) {
            try {
                const response = await fetch(`/admin/users/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    window.showToast('Pengguna berhasil dihapus!');
                    loadUsers();
                } else {
                    window.showToast('Gagal menghapus pengguna.', 'error');
                }
            } catch (error) {
                console.error('Error deleting user:', error);
                window.showToast('Terjadi kesalahan sistem saat menghapus.', 'error');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', loadUsers);
</script>
@endpush
