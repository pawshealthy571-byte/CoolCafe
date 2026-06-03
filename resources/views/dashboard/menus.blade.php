@extends('layouts.app')

@section('title', 'Manajemen Menu')

@section('content')
<div id="menu-management-section">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Menu</h2>
            <p class="text-xs text-gray-500 mt-1">Kelola menu satuan dan menu paket beserta add-on.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="openMenuModal(null, 'Paket')" class="btn-primary !text-xs bg-amber-600 hover:bg-amber-700">
                <i class="fas fa-box-open mr-2"></i>Tambah Paket
            </button>
            <button onclick="openMenuModal()" class="btn-primary !text-xs bg-green-600 hover:bg-green-700">
                <i class="fas fa-plus mr-2"></i>Tambah Menu
            </button>
        </div>
    </div>

    <div class="flex overflow-x-auto gap-2 mb-4 pb-1">
        <button type="button" onclick="filterMenuCategory('all')" data-menu-filter="all" class="menu-filter bg-coffee text-white px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">Semua</button>
        <button type="button" onclick="filterMenuCategory('Paket')" data-menu-filter="Paket" class="menu-filter bg-white border border-gray-100 text-gray-500 px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">Paket</button>
        <button type="button" onclick="filterMenuCategory('Bakery')" data-menu-filter="Bakery" class="menu-filter bg-white border border-gray-100 text-gray-500 px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">Bakery</button>
        <button type="button" onclick="filterMenuCategory('Beverages')" data-menu-filter="Beverages" class="menu-filter bg-white border border-gray-100 text-gray-500 px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">Beverages</button>
        <button type="button" onclick="filterMenuCategory('Main Course')" data-menu-filter="Main Course" class="menu-filter bg-white border border-gray-100 text-gray-500 px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap">Main Course</button>
    </div>

    <div class="card !p-0 overflow-hidden mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
                    <tr>
                        <th class="text-left px-6 py-4">Menu</th>
                        <th class="text-left px-6 py-4">Kategori</th>
                        <th class="text-left px-6 py-4">Harga</th>
                        <th class="text-left px-6 py-4">Status</th>
                        <th class="text-right px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody id="menu-table-body" class="divide-y divide-gray-50">
                    <!-- Menu items will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
    <a href="/dashboard" class="text-xs text-coffee font-bold hover:underline mb-8 inline-block">
        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dashboard
    </a>
</div>

<!-- Menu Modal -->
<div id="menu-modal" class="fixed inset-0 bg-black bg-opacity-50 z-[100] hidden flex items-start md:items-center justify-center p-3 md:p-4 overflow-y-auto">
    <div class="bg-white w-full max-w-md rounded-[1.5rem] shadow-2xl overflow-hidden border border-gray-100 my-4 md:my-0 max-h-[92vh] flex flex-col">
        <div class="bg-coffee text-white p-5 flex-shrink-0">
            <h3 id="modal-title" class="text-lg font-bold">Tambah Menu Baru</h3>
        </div>
        <form id="menu-form" class="flex-1 min-h-0 flex flex-col">
            <div class="p-5 space-y-4 overflow-y-auto">
            <input type="hidden" id="menu-id">
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Nama Menu</label>
                <input type="text" id="menu-name" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Kategori</label>
                <select id="menu-category" required onchange="toggleAddOnField()" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30">
                    <option value="Bakery">Bakery</option>
                    <option value="Beverages">Beverages</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Paket">Paket</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Harga (Rp)</label>
                <input type="number" id="menu-price" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30">
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Foto Menu</label>
                <div class="mt-2 flex items-center gap-3">
                    <label class="flex-1 cursor-pointer">
                        <div class="bg-gray-100 border border-gray-100 py-3 rounded-2xl text-[10px] font-bold text-gray-600 hover:bg-gray-200 transition-all uppercase tracking-widest text-center">
                            <i class="fas fa-upload mr-2"></i> Upload Foto
                        </div>
                        <input type="file" id="menu-image-file" name="image_file" class="hidden" accept="image/*" onchange="previewImage(this)">
                    </label>
                    <div id="image-preview-container" class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden hidden">
                        <img id="menu-image-preview" src="" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Deskripsi</label>
                <textarea id="menu-description" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30 h-20"></textarea>
            </div>
            <div id="menu-add-ons-group">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Add-on Paket</label>
                <textarea id="menu-add-ons" placeholder="Extra Espresso | 7000&#10;French Fries | 12000" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30 h-24"></textarea>
                <p class="text-[9px] text-gray-400 mt-1 ml-1">Satu add-on per baris. Format: Nama | Harga</p>
            </div>
            <div class="flex items-center gap-2 ml-1">
                <input type="checkbox" id="menu-available" checked class="rounded border-gray-300 text-coffee focus:ring-coffee">
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest cursor-pointer">Tersedia</label>
            </div>
            </div>
            <div class="grid grid-cols-2 gap-3 p-5 border-t border-gray-100 bg-white flex-shrink-0">
                <button type="button" onclick="closeMenuModal()" class="bg-gray-100 text-gray-700 py-3 rounded-2xl font-bold text-sm">Batal</button>
                <button type="submit" class="bg-coffee text-white py-3 rounded-2xl font-bold text-sm shadow-lg shadow-coffee/20">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const STORAGE_URL = "{{ asset('storage') }}";
    let menus = [];
    let activeMenuCategory = 'all';

    async function loadMenus() {
        const response = await fetch('/admin/menus', { headers: { 'Accept': 'application/json' } });
        menus = await response.json();
        renderMenuTable();
    }

    function filterMenuCategory(category) {
        activeMenuCategory = category;
        document.querySelectorAll('.menu-filter').forEach(button => {
            const active = button.dataset.menuFilter === category;
            button.className = active
                ? 'menu-filter bg-coffee text-white px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap'
                : 'menu-filter bg-white border border-gray-100 text-gray-500 px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap';
        });
        renderMenuTable();
    }

    function renderMenuTable() {
        const tbody = document.getElementById('menu-table-body');
        const visibleMenus = activeMenuCategory === 'all'
            ? menus
            : menus.filter(menu => menu.category === activeMenuCategory);

        tbody.innerHTML = visibleMenus.length ? '' : '<tr><td colspan="5" class="px-6 py-10 text-center text-gray-400">Belum ada menu di kategori ini.</td></tr>';
        
        visibleMenus.forEach(menu => {
            let imageUrl = menu.image || 'https://placehold.co/200x200?text=No+Image';
            if (menu.image && !menu.image.startsWith('http')) {
                imageUrl = `${STORAGE_URL}/${menu.image}`;
            }

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors';
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="${imageUrl}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-xs">${menu.name}</p>
                            <p class="text-[10px] text-gray-400 line-clamp-1">${menu.description || '-'}</p>
                            ${menu.category === 'Paket' ? `<p class="text-[9px] text-amber-600 font-bold mt-1">${(menu.add_ons || []).length} add-on</p>` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="text-[10px] font-bold text-gray-500 uppercase">${menu.category}</span>
                </td>
                <td class="px-6 py-4">
                    <p class="font-bold text-coffee text-xs">Rp ${Number(menu.price).toLocaleString('id-ID')}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-lg text-[8px] font-bold uppercase tracking-widest ${menu.is_available ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'}">
                        ${menu.is_available ? 'Tersedia' : 'Habis'}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-2">
                        <button onclick="openMenuModal(${menu.id})" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-edit text-[10px]"></i>
                        </button>
                        <button onclick="deleteMenu(${menu.id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all flex items-center justify-center">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openMenuModal(id = null, presetCategory = null) {
        const modal = document.getElementById('menu-modal');
        const form = document.getElementById('menu-form');
        const title = document.getElementById('modal-title');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('menu-image-preview');
        
        form.reset();
        document.getElementById('menu-id').value = '';
        document.getElementById('menu-image-file').value = '';
        document.getElementById('menu-add-ons').value = '';
        previewContainer.classList.add('hidden');
        previewImg.src = '';
        title.innerText = id ? 'Edit Menu' : 'Tambah Menu Baru';
        if (presetCategory) {
            document.getElementById('menu-category').value = presetCategory;
            title.innerText = presetCategory === 'Paket' ? 'Tambah Paket' : 'Tambah Menu Baru';
        }

        if (id) {
            const menu = menus.find(m => m.id === id);
            document.getElementById('menu-id').value = menu.id;
            document.getElementById('menu-name').value = menu.name;
            document.getElementById('menu-category').value = menu.category;
            document.getElementById('menu-price').value = menu.price;
            document.getElementById('menu-description').value = menu.description || '';
            document.getElementById('menu-add-ons').value = (menu.add_ons || [])
                .map(addOn => `${addOn.name} | ${Number(addOn.price || 0)}`)
                .join('\n');
            document.getElementById('menu-available').checked = menu.is_available;

            if (menu.image) {
                previewImg.src = menu.image.startsWith('http') ? menu.image : `${STORAGE_URL}/${menu.image}`;
                previewContainer.classList.remove('hidden');
            }
        }

        toggleAddOnField();
        modal.classList.remove('hidden');
        form.scrollTop = 0;
    }

    function previewImage(input) {
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('menu-image-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function compressImage(file) {
        const canCompress = ['image/jpeg', 'image/png', 'image/webp'].includes(file.type);
        if (!canCompress || file.size <= 1500 * 1024) {
            return Promise.resolve(file);
        }

        return new Promise((resolve, reject) => {
            const img = new Image();
            const objectUrl = URL.createObjectURL(file);

            img.onload = () => {
                URL.revokeObjectURL(objectUrl);

                const maxDimension = 1600;
                const scale = Math.min(1, maxDimension / Math.max(img.width, img.height));
                const canvas = document.createElement('canvas');
                canvas.width = Math.round(img.width * scale);
                canvas.height = Math.round(img.height * scale);

                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(blob => {
                    if (!blob) {
                        resolve(file);
                        return;
                    }

                    const compressedName = file.name.replace(/\.[^.]+$/, '.jpg');
                    resolve(new File([blob], compressedName, {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    }));
                }, 'image/jpeg', 0.85);
            };

            img.onerror = () => {
                URL.revokeObjectURL(objectUrl);
                reject(new Error('Gambar tidak bisa dibaca.'));
            };

            img.src = objectUrl;
        });
    }

    function toggleAddOnField() {
        const isPackage = document.getElementById('menu-category').value === 'Paket';
        const group = document.getElementById('menu-add-ons-group');
        group.classList.toggle('hidden', !isPackage);
        if (!isPackage) {
            document.getElementById('menu-add-ons').value = '';
        }
    }

    function closeMenuModal() {
        document.getElementById('menu-modal').classList.add('hidden');
    }

    function parseAddOns() {
        if (document.getElementById('menu-category').value !== 'Paket') {
            return [];
        }

        return document.getElementById('menu-add-ons').value
            .split('\n')
            .map(row => row.trim())
            .filter(Boolean)
            .map(row => {
                const [name, price = '0'] = row.split('|').map(part => part.trim());
                return { name, price: Number(price.replace(/[^\d]/g, '')) || 0 };
            })
            .filter(addOn => addOn.name);
    }

    document.getElementById('menu-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerText;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerText = 'Menyimpan...';

            const id = document.getElementById('menu-id').value;
            const formData = new FormData();
            
            formData.append('name', document.getElementById('menu-name').value);
            formData.append('category', document.getElementById('menu-category').value);
            formData.append('price', document.getElementById('menu-price').value);
            formData.append('description', document.getElementById('menu-description').value);
            formData.append('add_ons', JSON.stringify(parseAddOns()));
            formData.append('is_available', document.getElementById('menu-available').checked ? '1' : '0');
            
            const fileInput = document.getElementById('menu-image-file');
            if (fileInput.files[0]) {
                const imageFile = await compressImage(fileInput.files[0]);
                formData.append('image_file', imageFile);
            }

            let url = id ? `/admin/menus/${id}` : '/admin/menus';
            
            // Method spoofing for PUT because FormData doesn't support PUT directly in some PHP versions/Laravel setups
            if (id) {
                formData.append('_method', 'PUT');
            }

            const response = await fetch(url, {
                method: 'POST', // Always POST when using FormData with file
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            if (response.ok) {
                closeMenuModal();
                await loadMenus();
                alert('Berhasil disimpan!');
            } else {
                let errorMessage = 'Periksa kembali input Anda.';
                try {
                    const err = await response.json();
                    errorMessage = err.message || errorMessage;
                    if (err.errors) {
                        errorMessage = Object.values(err.errors).flat().join('\n');
                    }
                    console.error('Save error details:', err);
                } catch (e) {
                    console.error('Non-JSON error response');
                }
                alert('Gagal menyimpan: ' + errorMessage);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            alert('Terjadi kesalahan sistem atau koneksi. Silakan coba lagi.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerText = originalBtnText;
        }
    });

    async function deleteMenu(id) {
        if (confirm('Yakin ingin menghapus menu ini?')) {
            const response = await fetch(`/admin/menus/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                loadMenus();
            }
        }
    }

    // Load menus on page load
    document.addEventListener('DOMContentLoaded', loadMenus);
</script>
@endpush
