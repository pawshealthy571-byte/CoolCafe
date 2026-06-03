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
    </style>
    @stack('styles')
</head>
<body class="bg-[#FDFDFC] min-h-screen">
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

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto p-4 md:p-10">
            <div class="max-w-7xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
