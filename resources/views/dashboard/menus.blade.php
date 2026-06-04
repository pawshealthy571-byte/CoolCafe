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
                    <input type="file" id="menu-image-file" accept="image/*" class="hidden" onchange="handleFileSelect(this)">
                    <button type="button" onclick="document.getElementById('menu-image-file').click()" class="flex-1 bg-gray-100 border border-gray-100 py-3 rounded-2xl text-[10px] font-bold text-gray-600 hover:bg-gray-200 transition-all uppercase tracking-widest">
                        <i class="fas fa-camera mr-2"></i> Pilih Foto dari Galeri
                    </button>
                    <div id="image-preview-container" class="w-12 h-12 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden hidden">
                        <img id="menu-image-preview" src="" class="w-full h-full object-cover">
                    </div>
                </div>
                <input type="hidden" id="menu-image">
            </div>
            <div>
                <label id="desc-label" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Deskripsi</label>
                <textarea id="menu-description" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm outline-none focus:border-coffee/30 h-20"></textarea>
            </div>
            <div id="package-items-group" class="hidden space-y-3 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
                <label class="text-[10px] font-bold text-coffee uppercase tracking-widest ml-1 flex items-center gap-2">
                    <i class="fas fa-boxes-stacked"></i> Isi Paket
                </label>
                <div class="flex gap-2">
                    <select id="package-item-select" class="flex-1 bg-white border border-gray-200 rounded-xl px-3 py-2 text-xs outline-none focus:border-coffee/30">
                        <option value="">-- Pilih Menu untuk Paket --</option>
                    </select>
                    <button type="button" onclick="addPackageItem()" class="bg-coffee text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-coffee/20 hover:scale-105 active:scale-95 transition-all">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <div id="package-items-list" class="space-y-2 max-h-40 overflow-y-auto pr-1">
                    <!-- Selected package items will appear here -->
                </div>
                <div id="package-normal-price" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-right px-1">
                    Total Harga Normal: <span class="text-gray-600">Rp 0</span>
                </div>
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

<!-- Crop Modal -->
<div id="crop-modal" class="fixed inset-0 bg-black bg-opacity-70 z-[200] hidden flex items-center justify-center p-3 md:p-4">
    <div class="bg-white w-full max-w-xl rounded-[1.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Sesuaikan Foto</h3>
            <button onclick="closeCropModal()" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
        <div class="p-5 flex-1 bg-gray-50 flex items-center justify-center overflow-hidden">
            <div class="cropper-container w-full h-full flex items-center justify-center">
                <img id="cropper-image" src="">
            </div>
        </div>
        <div class="p-5 border-t border-gray-100 grid grid-cols-2 gap-3">
            <button type="button" onclick="closeCropModal()" class="bg-gray-100 text-gray-700 py-3 rounded-2xl font-bold text-sm">Batal</button>
            <button type="button" onclick="cropAndUpload()" class="bg-coffee text-white py-3 rounded-2xl font-bold text-sm shadow-lg shadow-coffee/20">Simpan & Unggah</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
    .cropper-container {
        max-height: 400px;
        background-color: #f8fafc;
    }
    #cropper-image {
        display: block;
        max-width: 100%;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    let menus = [];
    let activeMenuCategory = 'all';
    let cropper = null;
    let selectedFile = null;

    async function loadMenus() {
        try {
            const response = await fetch('/admin/menus', { headers: { 'Accept': 'application/json' } });
            menus = await response.json();
            renderMenuTable();
        } catch (error) {
            console.error('Error loading menus:', error);
        }
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
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors';
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                            <img src="${menu.image || 'https://placehold.co/200x200?text=No+Image'}" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-xs">${menu.name}</p>
                            <p class="text-[10px] text-gray-400 line-clamp-1">${menu.description || '-'}</p>
                            ${menu.category === 'Paket' ? '<p class="text-[9px] text-amber-600 font-bold mt-1">' + (menu.add_ons || []).length + ' add-on</p>' : ''}
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

    let packageItems = [];

    function toggleAddOnField() {
        const category = document.getElementById('menu-category').value;
        const isPackage = category === 'Paket';
        const addOnGroup = document.getElementById('menu-add-ons-group');
        const packageGroup = document.getElementById('package-items-group');
        const descLabel = document.getElementById('desc-label');
        const descTextarea = document.getElementById('menu-description');

        if (addOnGroup) addOnGroup.classList.toggle('hidden', !isPackage);
        if (packageGroup) packageGroup.classList.toggle('hidden', !isPackage);
        
        if (isPackage) {
            if (descLabel) descLabel.innerText = 'Detail Isi Paket';
            if (descTextarea) {
                descTextarea.placeholder = 'Detail ini akan terisi otomatis saat Anda memilih menu di bawah.';
                descTextarea.readOnly = true;
            }
            populatePackageItemSelector();
        } else {
            if (descLabel) descLabel.innerText = 'Deskripsi';
            if (descTextarea) {
                descTextarea.placeholder = 'Masukkan deskripsi menu...';
                descTextarea.readOnly = false;
            }
        }

        if (!isPackage && document.getElementById('menu-add-ons')) {
            document.getElementById('menu-add-ons').value = '';
        }
    }

    function populatePackageItemSelector() {
        const selector = document.getElementById('package-item-select');
        if (!selector) return;

        const currentId = document.getElementById('menu-id').value;
        
        selector.innerHTML = '<option value="">-- Pilih Menu untuk Paket --</option>';
        
        menus.filter(m => m.category !== 'Paket' && String(m.id) !== String(currentId)).forEach(menu => {
            const option = document.createElement('option');
            option.value = menu.id;
            option.textContent = `${menu.name} (Rp ${Number(menu.price).toLocaleString('id-ID')})`;
            selector.appendChild(option);
        });
    }

    function addPackageItem() {
        const selector = document.getElementById('package-item-select');
        if (!selector) return;

        const menuId = selector.value;
        if (!menuId) return;

        const menu = menus.find(m => String(m.id) === String(menuId));
        if (!menu) return;

        const existing = packageItems.find(item => String(item.id) === String(menuId));

        if (existing) {
            existing.quantity++;
        } else {
            packageItems.push({ id: menu.id, name: menu.name, price: menu.price, quantity: 1 });
        }

        selector.value = '';
        renderPackageItems();
    }

    function removePackageItem(id) {
        packageItems = packageItems.filter(item => String(item.id) !== String(id));
        renderPackageItems();
    }

    function updatePackageItemQty(id, delta) {
        const item = packageItems.find(i => String(i.id) === String(id));
        if (item) {
            item.quantity = Math.max(1, item.quantity + delta);
            renderPackageItems();
        }
    }

    function renderPackageItems() {
        const list = document.getElementById('package-items-list');
        if (!list) return;

        list.innerHTML = '';
        
        let totalNormal = 0;
        let detailParts = [];

        packageItems.forEach(item => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between bg-white p-3 rounded-xl border border-gray-100 shadow-sm';
            row.innerHTML = `
                <div class="flex-1">
                    <p class="text-xs font-bold text-gray-800">${item.name}</p>
                    <p class="text-[10px] text-gray-400">Rp ${Number(item.price).toLocaleString('id-ID')}</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex items-center bg-gray-50 rounded-lg p-1">
                        <button type="button" onclick="updatePackageItemQty('${item.id}', -1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-coffee"><i class="fas fa-minus text-[8px]"></i></button>
                        <span class="w-8 text-center text-xs font-bold text-gray-700">${item.quantity}</span>
                        <button type="button" onclick="updatePackageItemQty('${item.id}', 1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-coffee"><i class="fas fa-plus text-[8px]"></i></button>
                    </div>
                    <button type="button" onclick="removePackageItem('${item.id}')" class="text-red-400 hover:text-red-600 transition-colors p-1">
                        <i class="fas fa-trash-can text-xs"></i>
                    </button>
                </div>
            `;
            list.appendChild(row);
            
            totalNormal += item.price * item.quantity;
            detailParts.push(`${item.quantity}x ${item.name}`);
        });

        const normalPriceSpan = document.querySelector('#package-normal-price span');
        if (normalPriceSpan) normalPriceSpan.textContent = `Rp ${totalNormal.toLocaleString('id-ID')}`;
        
        const descTextarea = document.getElementById('menu-description');
        if (descTextarea) descTextarea.value = detailParts.join(', ');
    }

    function openMenuModal(id = null, presetCategory = null) {
        const modal = document.getElementById('menu-modal');
        const form = document.getElementById('menu-form');
        const title = document.getElementById('modal-title');
        const previewContainer = document.getElementById('image-preview-container');
        const previewImg = document.getElementById('menu-image-preview');
        
        if (form) form.reset();
        packageItems = [];
        renderPackageItems();
        
        if (document.getElementById('menu-id')) document.getElementById('menu-id').value = '';
        if (document.getElementById('menu-image')) document.getElementById('menu-image').value = '';
        if (document.getElementById('menu-add-ons')) document.getElementById('menu-add-ons').value = '';
        if (previewContainer) previewContainer.classList.add('hidden');
        if (previewImg) previewImg.src = '';
        
        if (title) title.innerText = id ? 'Edit Menu' : 'Tambah Menu Baru';
        
        if (presetCategory) {
            if (document.getElementById('menu-category')) document.getElementById('menu-category').value = presetCategory;
            if (title) title.innerText = presetCategory === 'Paket' ? 'Tambah Paket' : 'Tambah Menu Baru';
        }

        if (id) {
            const menu = menus.find(m => m.id === id);
            if (menu) {
                if (document.getElementById('menu-id')) document.getElementById('menu-id').value = menu.id;
                if (document.getElementById('menu-name')) document.getElementById('menu-name').value = menu.name;
                if (document.getElementById('menu-category')) document.getElementById('menu-category').value = menu.category;
                if (document.getElementById('menu-price')) document.getElementById('menu-price').value = menu.price;
                if (document.getElementById('menu-image')) document.getElementById('menu-image').value = menu.image || '';
                if (document.getElementById('menu-description')) document.getElementById('menu-description').value = menu.description || '';
                
                if (document.getElementById('menu-add-ons')) {
                    document.getElementById('menu-add-ons').value = (menu.add_ons || [])
                        .map(addOn => `${addOn.name} | ${Number(addOn.price || 0)}`)
                        .join('\n');
                }
                
                if (document.getElementById('menu-available')) document.getElementById('menu-available').checked = menu.is_available;

                if (menu.image && previewImg && previewContainer) {
                    previewImg.src = menu.image;
                    previewContainer.classList.remove('hidden');
                }

                // If it's a package, we try to reconstruct packageItems from the description string
                if (menu.category === 'Paket' && menu.description) {
                    const parts = menu.description.split(', ');
                    parts.forEach(part => {
                        const match = part.match(/^(\d+)x\s+(.+)$/);
                        if (match) {
                            const qty = parseInt(match[1]);
                            const name = match[2];
                            const sourceMenu = menus.find(m => m.name === name);
                            if (sourceMenu) {
                                packageItems.push({ id: sourceMenu.id, name: sourceMenu.name, price: sourceMenu.price, quantity: qty });
                            }
                        }
                    });
                    renderPackageItems();
                }
            }
        }

        toggleAddOnField();
        if (modal) modal.classList.remove('hidden');
        if (form) form.scrollTop = 0;
    }

    async function handleFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        selectedFile = input.files[0];
        
        const reader = new FileReader();
        reader.onload = (e) => {
            const cropModal = document.getElementById('crop-modal');
            const cropperImg = document.getElementById('cropper-image');
            
            if (cropperImg) cropperImg.src = e.target.result;
            if (cropModal) cropModal.classList.remove('hidden');
            
            if (cropper) cropper.destroy();
            
            if (cropperImg) {
                cropper = new Cropper(cropperImg, {
                    aspectRatio: 1,
                    viewMode: 2,
                    dragMode: 'move',
                    autoCropArea: 1,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            }
        };
        reader.readAsDataURL(selectedFile);
    }

    function closeCropModal() {
        const cropModal = document.getElementById('crop-modal');
        if (cropModal) cropModal.classList.add('hidden');
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        if (document.getElementById('menu-image-file')) document.getElementById('menu-image-file').value = '';
    }

    async function cropAndUpload() {
        if (!cropper) return;
        
        const canvas = cropper.getCroppedCanvas({
            width: 800,
            height: 800,
        });

        canvas.toBlob(async (blob) => {
            const formData = new FormData();
            formData.append('image', blob, 'cropped-menu-image.jpg');

            try {
                const response = await fetch('/admin/upload-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                if (response.ok) {
                    const result = await response.json();
                    if (document.getElementById('menu-image')) document.getElementById('menu-image').value = result.path;
                    
                    const previewContainer = document.getElementById('image-preview-container');
                    const previewImg = document.getElementById('menu-image-preview');
                    if (previewImg) previewImg.src = result.path;
                    if (previewContainer) previewContainer.classList.remove('hidden');
                    
                    window.showToast('Foto berhasil disesuaikan & diunggah!');
                    closeCropModal();
                } else {
                    window.showToast('Gagal mengunggah foto.', 'error');
                }
            } catch (error) {
                console.error('Error uploading image:', error);
                window.showToast('Terjadi kesalahan saat mengunggah.', 'error');
            }
        }, 'image/jpeg', 0.85);
    }

    function closeMenuModal() {
        const modal = document.getElementById('menu-modal');
        if (modal) modal.classList.add('hidden');
    }

    function parseAddOns() {
        const categorySelect = document.getElementById('menu-category');
        if (!categorySelect || categorySelect.value !== 'Paket') {
            return [];
        }

        const addOnsTextarea = document.getElementById('menu-add-ons');
        if (!addOnsTextarea) return [];

        return addOnsTextarea.value
            .split('\n')
            .map(row => row.trim())
            .filter(Boolean)
            .map(row => {
                const [name, price = '0'] = row.split('|').map(part => part.trim());
                return { name, price: Number(price.replace(/[^\d]/g, '')) || 0 };
            })
            .filter(addOn => addOn.name);
    }

    const menuForm = document.getElementById('menu-form');
    if (menuForm) {
        menuForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('menu-id').value;
            const data = {
                name: document.getElementById('menu-name').value,
                category: document.getElementById('menu-category').value,
                price: document.getElementById('menu-price').value,
                image: document.getElementById('menu-image').value,
                description: document.getElementById('menu-description').value,
                add_ons: parseAddOns(),
                is_available: document.getElementById('menu-available').checked,
            };

            const url = id ? `/admin/menus/${id}` : '/admin/menus';
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
                    closeMenuModal();
                    loadMenus();
                    window.showToast(id ? 'Menu berhasil diperbarui!' : 'Menu baru berhasil ditambahkan!');
                } else {
                    const result = await response.json();
                    if (result.errors) {
                        const firstError = Object.values(result.errors)[0][0];
                        window.showToast(firstError, 'error');
                    } else {
                        window.showToast('Gagal menyimpan menu. Periksa input Anda.', 'error');
                    }
                }
            } catch (error) {
                console.error('Error saving menu:', error);
                window.showToast('Terjadi kesalahan saat menyimpan.', 'error');
            }
        });
    }

    async function deleteMenu(id) {
        const confirmed = await window.showConfirm({
            title: 'Hapus Menu?',
            message: 'Menu ini akan dihapus permanen dari sistem. Lanjutkan?',
            okText: 'YA, HAPUS'
        });

        if (confirmed) {
            try {
                const response = await fetch(`/admin/menus/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                if (response.ok) {
                    loadMenus();
                    window.showToast('Menu berhasil dihapus!');
                } else {
                    window.showToast('Gagal menghapus menu.', 'error');
                }
            } catch (error) {
                console.error('Error deleting menu:', error);
                window.showToast('Terjadi kesalahan saat menghapus.', 'error');
            }
        }
    }

    // Load menus on page load
    document.addEventListener('DOMContentLoaded', loadMenus);
</script>
@endpush
