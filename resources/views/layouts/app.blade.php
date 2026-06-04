<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CoolCafe') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Scripts / Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FDFDFC;
        }
        .bg-coffee { background-color: #634832; }
        .text-coffee { color: #634832; }
        .border-coffee { border-color: #634832; }
        .btn-primary {
            background-color: #634832;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: #4f3928;
        }
        .card {
            background-color: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #f3f4f6;
        }

        /* Toast Styles */
        .toast-container {
            position: fixed;
            top: 24px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            width: calc(100% - 40px);
            max-width: 400px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        }
        .toast-item {
            background: white;
            padding: 16px;
            border-radius: 20px;
            box-shadow: 0 15px 35px -5px rgba(0,0,0,0.1), 0 5px 15px -5px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 14px;
            border-left: 5px solid #634832;
            transform: translateY(-20px);
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            pointer-events: auto;
        }
        .toast-item.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .toast-success { border-left-color: #22c55e; }
        .toast-success .toast-icon { background: #f0fdf4; color: #22c55e; }
        .toast-error { border-left-color: #ef4444; }
        .toast-error .toast-icon { background: #fef2f2; color: #ef4444; }
        .toast-info { border-left-color: #3b82f6; }
        .toast-info .toast-icon { background: #eff6ff; color: #3b82f6; }
    </style>
    @stack('styles')
</head>
<body class="bg-[#FDFDFC] min-h-screen">
    <div id="toast-container" class="toast-container"></div>

    <!-- Custom Confirm Modal -->
    <div id="confirm-modal" class="fixed inset-0 bg-black/50 z-[9999] hidden flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-8 text-center">
                <div class="w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-trash-can text-3xl"></i>
                </div>
                <h3 id="confirm-title" class="text-xl font-bold text-gray-800 mb-2">Hapus Item?</h3>
                <p id="confirm-message" class="text-sm text-gray-500 leading-relaxed">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="p-4 bg-gray-50 flex gap-3">
                <button id="confirm-cancel-btn" class="flex-1 bg-white text-gray-700 py-4 rounded-2xl font-bold text-sm border border-gray-200 hover:bg-gray-100 transition-all">
                    BATAL
                </button>
                <button id="confirm-ok-btn" class="flex-1 bg-red-600 text-white py-4 rounded-2xl font-bold text-sm shadow-lg shadow-red-200 hover:bg-red-700 transition-all">
                    YA, HAPUS
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col md:flex-row h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-coffee text-white flex-shrink-0 shadow-xl z-50 overflow-y-auto">
            <div class="flex flex-col min-h-full">
                <!-- Logo area -->
                <div class="p-6 flex items-center gap-3 border-b border-white/10 sticky top-0 bg-coffee z-10">
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-coffee text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">CoolCafe</h1>
                        <p class="text-[10px] text-white/70 uppercase tracking-widest font-semibold">System Panel</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 p-4 space-y-2">
                    @auth
                        <div class="mb-4 px-2">
                            <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Utama</p>
                            @if(in_array(Auth::user()->role, ['cashier', 'admin', 'superadmin', 'chef', 'manager']))
                                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('dashboard*') && !Request::is('admin/management/menus') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
                                    <i class="fas fa-chart-pie w-5"></i>
                                    <span class="text-sm">Dashboard</span>
                                </a>
                            @endif
                            @if(!in_array(Auth::user()->role, ['admin', 'superadmin', 'cashier', 'chef']))
                                <a href="/menu" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('menu*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
                                    <i class="fas fa-utensils w-5"></i>
                                    <span class="text-sm">Lihat Menu</span>
                                </a>
                            @endif
                        </div>

                        @if(in_array(Auth::user()->role, ['admin', 'superadmin', 'manager']))
                            <div class="mb-4 px-2">
                                <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Manajemen</p>
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
                            </div>
                            <div class="mb-4 px-2">
                                <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Manajemen Akun</p>
                                <a href="/admin/management/users/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/users/admin') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
                                    <i class="fas fa-user-shield w-5"></i>
                                    <span class="text-sm">Admin</span>
                                </a>
                                <a href="/admin/management/users/chef" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/users/chef') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
                                    <i class="fas fa-hat-chef w-5"></i>
                                    <span class="text-sm">Chef</span>
                                </a>
                                <a href="/admin/management/users/cashier" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('admin/management/users/cashier') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
                                    <i class="fas fa-cash-register w-5"></i>
                                    <span class="text-sm">Kasir</span>
                                </a>
                            </div>
                        @endif
                    @endauth
                </nav>

                <!-- User Profile & Logout -->
                <div class="p-4 bg-black/10 border-t border-white/10 sticky bottom-0 bg-coffee/95 backdrop-blur-sm">
                    @auth
                        <div class="flex items-center gap-3 px-2 mb-4">
                            <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-xs font-bold truncate">{{ Auth::user()->name }}</p>
                                <p class="text-[9px] text-white/60 uppercase truncate">{{ Auth::user()->role }}</p>
                            </div>
                        </div>
                        <form method="POST" action="/logout">
                            @csrf
                            <button class="w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-red-500/80 hover:bg-red-500 transition-all text-sm font-bold">
                                <i class="fas fa-sign-out-alt w-5"></i>
                                <span>Logout</span>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-10">
            <div class="max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    <script>
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast-item toast-${type}`;
            
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-circle-exclamation' : 'fa-info-circle');
            
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="fas ${icon} text-lg"></i>
                </div>
                <div class="flex-1">
                    <p class="text-[13px] font-bold text-gray-800 leading-tight">${message}</p>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Force reflow
            toast.offsetHeight;
            toast.classList.add('show');
            
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }, 3500);
        };

        window.showConfirm = function(options = {}) {
            return new Promise((resolve) => {
                const modal = document.getElementById('confirm-modal');
                const title = document.getElementById('confirm-title');
                const message = document.getElementById('confirm-message');
                const okBtn = document.getElementById('confirm-ok-btn');
                const cancelBtn = document.getElementById('confirm-cancel-btn');

                title.innerText = options.title || 'Hapus Item?';
                message.innerText = options.message || 'Apakah Anda yakin ingin menghapus data ini?';
                okBtn.innerText = options.okText || 'YA, HAPUS';
                cancelBtn.innerText = options.cancelText || 'BATAL';

                if (options.type === 'warning') {
                    okBtn.className = 'flex-1 bg-amber-600 text-white py-4 rounded-2xl font-bold text-sm shadow-lg shadow-amber-200 hover:bg-amber-700 transition-all';
                    document.querySelector('#confirm-modal .bg-red-50').className = 'w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6';
                    document.querySelector('#confirm-modal i').className = 'fas fa-exclamation-triangle text-3xl';
                } else {
                    okBtn.className = 'flex-1 bg-red-600 text-white py-4 rounded-2xl font-bold text-sm shadow-lg shadow-red-200 hover:bg-red-700 transition-all';
                    document.querySelector('#confirm-modal .bg-red-50, #confirm-modal .bg-amber-50').className = 'w-20 h-20 bg-red-50 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6';
                    document.querySelector('#confirm-modal i').className = 'fas fa-trash-can text-3xl';
                }

                modal.classList.remove('hidden');

                const cleanup = (result) => {
                    modal.classList.add('hidden');
                    okBtn.removeEventListener('click', onOk);
                    cancelBtn.removeEventListener('click', onCancel);
                    resolve(result);
                };

                const onOk = () => cleanup(true);
                const onCancel = () => cleanup(false);

                okBtn.addEventListener('click', onOk);
                cancelBtn.addEventListener('click', onCancel);
            });
        };
    </script>
</body>
</html>
