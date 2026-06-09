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

    <style>
        body {
            background-color: #FDFDFC;
        }
        .confirm-backdrop {
            position: fixed;
            inset: 0;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(17, 24, 39, 0.55);
            backdrop-filter: blur(4px);
        }
        .confirm-backdrop.show {
            display: flex;
        }
        .confirm-box {
            width: 100%;
            max-width: 420px;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 25px 55px rgba(17, 24, 39, 0.28);
            transform: translateY(10px) scale(0.98);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#FDFDFC] min-h-screen">
    <div class="flex flex-col md:flex-row h-screen overflow-hidden">
        <!-- Sidebar - Hidden for specific pages -->
        @if (!Request::is('forgot-password'))
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
                            @if(in_array(Auth::user()->role, ['admin', 'superadmin', 'manager']))
                                @include('layouts.partials.sidebar-admin')
                            @elseif(Auth::user()->role === 'cashier')
                                @include('layouts.partials.sidebar-cashier')
                            @elseif(Auth::user()->role === 'chef')
                                @include('layouts.partials.sidebar-chef')
                            @else
                                <div class="mb-4 px-2">
                                    <p class="text-[10px] text-white/40 uppercase tracking-widest font-bold mb-2">Utama</p>
                                    <a href="/menu" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/10 transition-all {{ Request::is('menu*') ? 'bg-white/10 font-bold' : 'text-white/70' }}">
                                        <i class="fas fa-utensils w-5"></i>
                                        <span class="text-sm">Lihat Menu</span>
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
        @endif

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-10">
            <div class="max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Global Toast Component -->
    <div id="toast" class="toast">
        <div class="flex items-start gap-3">
            <i id="toast-icon" class="fas fa-check-circle text-coffee mt-1"></i>
            <p id="toast-message" class="text-sm font-semibold"></p>
        </div>
    </div>

    <!-- Global Confirm Modal -->
    <div id="confirm-modal-global" class="confirm-backdrop">
        <div class="confirm-box p-6">
            <div class="w-12 h-12 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center mb-4">
                <i class="fas fa-triangle-exclamation text-xl"></i>
            </div>
            <h3 id="confirm-title-global" class="text-lg font-bold text-gray-800 mb-2">Konfirmasi</h3>
            <p id="confirm-message-global" class="text-sm text-gray-500 mb-5"></p>
            <div class="grid grid-cols-2 gap-3">
                <button id="confirm-cancel-global" type="button" class="bg-gray-100 text-gray-700 py-3 rounded-2xl font-bold text-sm">
                    Batal
                </button>
                <button id="confirm-ok-global" type="button" class="bg-red-500 text-white py-3 rounded-2xl font-bold text-sm hover:bg-red-600 transition-all">
                    YA, HAPUS
                </button>
            </div>
        </div>
    </div>

    <script>
        let toastTimer = null;
        window.showToast = function(message, type = 'success') {
            const toast = document.getElementById('toast');
            const icon = document.getElementById('toast-icon');
            const msgEl = document.getElementById('toast-message');
            if (!toast || !icon || !msgEl) return;
            
            msgEl.innerText = message;
            toast.classList.toggle('error', type === 'error');
            
            icon.className = type === 'error'
                ? 'fas fa-exclamation-circle text-red-500 mt-1'
                : 'fas fa-check-circle text-coffee mt-1';

            clearTimeout(toastTimer);
            toast.classList.add('show');
            toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
        };

        window.showConfirm = function({ title = 'Konfirmasi', message = '', okText = 'YA, HAPUS', cancelText = 'Batal' }) {
            return new Promise((resolve) => {
                const modal = document.getElementById('confirm-modal-global');
                const titleEl = document.getElementById('confirm-title-global');
                const msgEl = document.getElementById('confirm-message-global');
                const okBtn = document.getElementById('confirm-ok-global');
                const cancelBtn = document.getElementById('confirm-cancel-global');
                
                if (!modal || !titleEl || !msgEl || !okBtn || !cancelBtn) {
                    resolve(confirm(message)); // Fallback
                    return;
                }
                
                titleEl.innerText = title;
                msgEl.innerText = message;
                okBtn.innerText = okText;
                cancelBtn.innerText = cancelText;
                
                modal.classList.add('show');
                
                const cleanup = (value) => {
                    modal.classList.remove('show');
                    okBtn.removeEventListener('click', onOk);
                    cancelBtn.removeEventListener('click', onCancel);
                    resolve(value);
                };
                
                function onOk() { cleanup(true); }
                function onCancel() { cleanup(false); }
                
                okBtn.addEventListener('click', onOk, { once: true });
                cancelBtn.addEventListener('click', onCancel, { once: true });
            });
        };
    </script>

    @stack('scripts')
</body>
</html>
