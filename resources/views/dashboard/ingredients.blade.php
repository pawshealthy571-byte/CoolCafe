@extends('layouts.app')

@section('title', 'Manajemen Bahan Baku')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Manajemen Bahan Baku</h2>
    <p class="text-gray-500 text-sm">Kelola stok bahan baku untuk keperluan operasional chef.</p>
</div>

<div class="card p-6 mb-8">
    <h3 class="font-bold text-gray-800 mb-4">Tambah Bahan Baku</h3>
    <form id="add-ingredient-form" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="name" placeholder="Nama Bahan" class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm outline-none focus:border-coffee/30" required>
        <input type="number" name="stock" placeholder="Stok" class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm outline-none focus:border-coffee/30" required>
        <input type="text" name="unit" placeholder="Satuan (kg/pcs)" class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm outline-none focus:border-coffee/30" required>
        <button type="submit" class="bg-coffee text-white rounded-xl font-bold text-sm">Tambah</button>
    </form>
</div>

<div class="card !p-0 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
            <tr>
                <th class="text-left px-6 py-4">Nama Bahan</th>
                <th class="text-left px-6 py-4">Stok</th>
                <th class="text-right px-6 py-4">Aksi</th>
            </tr>
        </thead>
        <tbody id="ingredients-table-body" class="divide-y divide-gray-50">
            <!-- Ingredients will be loaded here -->
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    async function loadIngredients() {
        const response = await fetch('/admin/ingredients');
        const ingredients = await response.json();
        const tbody = document.getElementById('ingredients-table-body');
        tbody.innerHTML = '';
        ingredients.forEach(i => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-6 py-4 font-bold text-gray-800">${i.name}</td>
                <td class="px-6 py-4">
                    <span class="${i.stock === 0 ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'} px-2 py-1 rounded text-[10px] font-bold">
                        ${i.stock} ${i.unit}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <button onclick="deleteIngredient('${i.id}')" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    document.getElementById('add-ingredient-form').onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        await fetch('/admin/ingredients', {
            method: 'POST',
            body: formData,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        loadIngredients();
        e.target.reset();
    };

    async function deleteIngredient(id) {
        if (!confirm('Hapus bahan baku?')) return;
        await fetch(`/admin/ingredients/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        loadIngredients();
    }

    loadIngredients();
</script>
@endpush
