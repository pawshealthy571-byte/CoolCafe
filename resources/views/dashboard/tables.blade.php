@extends('layouts.app')

@section('title', 'Manajemen Meja & QR')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Manajemen Meja & QR</h2>
    <p class="text-gray-500 text-sm">Kelola daftar meja dan cetak QR Code untuk setiap meja.</p>
</div>

<div class="card p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-gray-800">Daftar Meja</h3>
        <button onclick="addTable()" class="bg-coffee text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm hover:bg-coffee-dark transition-all">
            Tambah Meja
        </button>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="table-list">
        <!-- Tables will be loaded here -->
    </div>
</div>

<!-- QR Modal -->
<div id="qr-modal" class="fixed inset-0 bg-gray-900/50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white p-6 rounded-2xl w-full max-w-sm text-center">
        <h3 id="qr-title" class="font-bold text-lg mb-4">QR Code Meja</h3>
        <div id="qr-code-container" class="mb-4 flex justify-center">
            <!-- QR code image will be generated here -->
        </div>
        <button onclick="closeQrModal()" class="w-full bg-gray-100 text-gray-700 py-2 rounded-xl font-bold">Tutup</button>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
    let tables = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // Initial mock data

    function renderTables() {
        const container = document.getElementById('table-list');
        container.innerHTML = tables.map(table => `
            <div class="border border-coffee-100 rounded-xl p-4 text-center hover:shadow-md transition-shadow">
                <p class="text-3xl font-bold text-coffee mb-2">#${table}</p>
                <button onclick="showQr(${table})" class="text-xs bg-coffee-50 text-coffee px-3 py-1.5 rounded-lg font-bold">Lihat QR</button>
            </div>
        `).join('');
    }

    function addTable() {
        const newTable = tables.length + 1;
        tables.push(newTable);
        renderTables();
    }

    async function showQr(table) {
        const modal = document.getElementById('qr-modal');
        const container = document.getElementById('qr-code-container');
        document.getElementById('qr-title').innerText = 'QR Code Meja #' + table;
        container.innerHTML = '';
        
        const canvas = document.createElement('canvas');
        container.appendChild(canvas);
        
        const url = window.location.origin + '/menu?table=' + table;
        await QRCode.toCanvas(canvas, url, { width: 200 });
        
        modal.classList.remove('hidden');
    }

    function closeQrModal() {
        document.getElementById('qr-modal').classList.add('hidden');
    }

    renderTables();
</script>
@endpush
@endsection
