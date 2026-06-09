@extends('layouts.app')

@section('title', 'Backup Data')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Backup Data Sistem</h2>
    <p class="text-gray-500 text-sm">Amankan data sistem dengan mengunduh salinan database ke perangkat Anda secara berkala.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Download Panel -->
    <div class="card !p-8 flex flex-col items-center justify-center text-center">
        <div class="w-20 h-20 bg-indigo-50 text-indigo-500 rounded-full flex items-center justify-center mb-6 shadow-inner">
            <i class="fas fa-cloud-download-alt text-4xl"></i>
        </div>
        <h3 class="font-bold text-gray-800 text-lg mb-2">Unduh Backup (Zip)</h3>
        <p class="text-xs text-gray-500 mb-8 max-w-sm">File zip akan berisi database SQLite (Menu, Pengguna, Voucher) serta data JSON (Penjualan, Pesanan, Bahan Baku).</p>
        
        <a href="/api/admin/backup/download" target="_blank" onclick="window.showToast('Mempersiapkan file backup untuk diunduh...')" class="bg-indigo-600 text-white px-8 py-3.5 rounded-2xl font-bold text-sm shadow-lg shadow-indigo-200 hover:scale-[1.02] active:scale-95 transition-all w-full md:w-auto flex items-center justify-center gap-3">
            <i class="fas fa-download"></i> Download File Zip
        </a>
    </div>

    <!-- Info Panel -->
    <div class="card bg-gray-50/50 border border-gray-100">
        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-coffee"></i> Informasi Penting
        </h3>
        <div class="space-y-4">
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                <p class="text-xs text-gray-600">Lakukan backup secara rutin (misalnya setiap akhir bulan) untuk menghindari kehilangan data yang fatal.</p>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                <p class="text-xs text-gray-600">Simpan file backup di tempat yang aman dan jangan membagikannya kepada orang yang tidak berkepentingan karena mengandung data finansial dan akun kasir.</p>
            </div>
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
                <p class="text-xs text-gray-600">Jika sistem mengalami error atau Anda butuh mengembalikan data ke versi sebelumnya, cukup ekstrak file Zip yang diunduh lalu hubungi tim IT/Programmer Anda untuk di-<i>restore</i> ke server secara manual.</p>
            </div>
        </div>
    </div>
</div>
@endsection


