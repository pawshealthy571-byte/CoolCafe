@extends('layouts.app')

@section('title', 'Manajemen Voucher')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Manajemen Voucher Diskon</h2>
    <p class="text-xs text-gray-500 mt-1">Kelola voucher diskon yang dapat digunakan pelanggan.</p>
</div>

<div class="card bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
    <form id="voucher-form" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <input type="text" id="voucher-code" placeholder="Kode Voucher (e.g., COFFEE10)" required class="border rounded-xl px-4 py-2">
        <select id="voucher-type" class="border rounded-xl px-4 py-2">
            <option value="percentage">Persentase (%)</option>
            <option value="fixed">Nominal Tetap (Rp)</option>
        </select>
        <input type="number" id="voucher-value" placeholder="Nilai Diskon" required class="border rounded-xl px-4 py-2">
        <input type="number" id="min-purchase" placeholder="Minimal Pembelian (Rp)" required class="border rounded-xl px-4 py-2">
        <button type="submit" class="bg-coffee text-white rounded-xl py-2 font-bold">Tambah Voucher</button>
    </form>

    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-400 uppercase text-[10px] tracking-widest font-bold">
            <tr>
                <th class="text-left px-4 py-3">Kode</th>
                <th class="text-left px-4 py-3">Tipe</th>
                <th class="text-left px-4 py-3">Nilai</th>
                <th class="text-left px-4 py-3">Min. Pembelian</th>
            </tr>
        </thead>
        <tbody id="voucher-table-body" class="divide-y"></tbody>
    </table>
</div>

<script>
    async function loadVouchers() {
        const resp = await fetch('/admin/vouchers');
        const vouchers = await resp.json();
        const tbody = document.getElementById('voucher-table-body');
        tbody.innerHTML = vouchers.map(v => `
            <tr>
                <td class="px-4 py-3">${v.code}</td>
                <td class="px-4 py-3">${v.type}</td>
                <td class="px-4 py-3">${v.value}</td>
                <td class="px-4 py-3">Rp ${Number(v.min_purchase).toLocaleString()}</td>
            </tr>
        `).join('');
    }
    
    document.getElementById('voucher-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            code: document.getElementById('voucher-code').value,
            type: document.getElementById('voucher-type').value,
            value: document.getElementById('voucher-value').value,
            min_purchase: document.getElementById('min-purchase').value,
            is_active: true
        };
        
        await fetch('/admin/vouchers', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(data)
        });
        
        loadVouchers();
    });
    
    loadVouchers();
</script>
@endsection
