@extends('layouts.app')

@section('title', 'Riwayat Aktivitas')

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Riwayat Aktivitas Sistem</h2>
    <p class="text-gray-500 text-sm">Pantau semua perubahan dan aktivitas yang dilakukan oleh admin atau kasir.</p>
</div>

<div class="card !p-0 overflow-hidden">
<div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50 flex-wrap gap-4">
        <h3 class="font-bold text-gray-800 flex items-center gap-2">
            <i class="fas fa-list-ul text-coffee"></i> Detail Aktivitas
        </h3>
        <input type="text" id="log-filter" placeholder="Cari aksi atau pengguna..." 
               class="bg-white border border-gray-200 rounded-xl px-4 py-2 text-xs outline-none focus:border-coffee/30 w-full md:w-64">
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100/50 text-gray-500 uppercase text-[10px] tracking-widest font-bold">
                <tr>
                    <th class="px-6 py-4">Waktu</th>
                    <th class="px-6 py-4">Pengguna</th>
                    <th class="px-6 py-4">Aksi</th>
                    <th class="px-6 py-4">Deskripsi</th>
                    <th class="px-6 py-4">IP Address</th>
                </tr>
            </thead>
            <tbody id="logs-table" class="divide-y divide-gray-50">
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Memuat data...</td></tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    let allLogs = [];

    async function loadLogs() {
        try {
            const res = await fetch('/admin/activity-logs');
            allLogs = await res.json();
            renderLogs(allLogs);
        } catch (e) {
            console.error(e);
            document.getElementById('logs-table').innerHTML = '<tr><td colspan="5" class="text-center py-8 text-red-400">Gagal memuat data.</td></tr>';
        }
    }
    
    function renderLogs(logs) {
        const tbody = document.getElementById('logs-table');
        if (logs.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada aktivitas.</td></tr>';
            return;
        }
        
        tbody.innerHTML = logs.map(log => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-xs">${new Date(log.created_at).toLocaleString('id-ID')}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-coffee/10 flex items-center justify-center text-coffee text-[10px] font-bold">
                            ${log.user ? log.user.name.charAt(0).toUpperCase() : 'S'}
                        </div>
                        <span class="font-bold text-gray-800 text-xs">${log.user ? log.user.name : 'Sistem'}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-indigo-600">${log.action}</td>
                <td class="px-6 py-4 text-xs text-gray-600">${log.description}</td>
                <td class="px-6 py-4 whitespace-nowrap text-xs font-mono text-gray-400">${log.ip_address || '-'}</td>
            </tr>
        `).join('');
    }

    document.getElementById('log-filter').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filtered = allLogs.filter(log => 
            log.action.toLowerCase().includes(term) || 
            (log.user && log.user.name.toLowerCase().includes(term)) ||
            log.description.toLowerCase().includes(term)
        );
        renderLogs(filtered);
    });
    
    loadLogs();
</script>
@endpush
@endsection
