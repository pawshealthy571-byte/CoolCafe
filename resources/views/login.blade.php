<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - {{ $title }}</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 bg-coffee-50 antialiased">
    <main class="w-full max-w-md bg-white rounded-[2.5rem] shadow-xl border border-coffee-950/5 overflow-hidden flex flex-col">
        <!-- Banner Header -->
        <div class="bg-coffee-900 text-white p-8 text-center relative overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
            <div class="absolute -left-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
            
            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fas fa-coffee text-2xl text-coffee-100 animate-pulse"></i>
            </div>
            <h1 class="font-serif text-2xl font-bold">CoolCafe</h1>
            <p class="text-[9px] uppercase tracking-widest text-coffee-200/80 font-bold mt-0.5">Portal Masuk Pegawai</p>
        </div>

        <form method="POST" action="{{ $action }}" class="p-8 space-y-6">
            @csrf
            
            <div class="text-center">
                <h2 class="font-serif text-lg font-bold text-coffee-950">{{ $title }}</h2>
                <p class="text-xs text-coffee-600 leading-relaxed mt-1">{{ $subtitle }}</p>
            </div>

            <!-- Email Input -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-coffee-600 uppercase tracking-widest ml-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-coffee-400">
                        <i class="fas fa-envelope text-xs"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                        class="w-full bg-coffee-50/20 border border-coffee-100 rounded-2xl pl-11 pr-4 py-3.5 text-xs font-semibold text-coffee-950 outline-none focus:border-coffee-400 focus:bg-white transition-all shadow-sm placeholder:text-coffee-300"
                        placeholder="your@email.com">
                </div>
                @error('email')
                    <p class="text-[10px] text-red-500 mt-2 ml-1 font-bold"><i class="fas fa-circle-exclamation mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Input -->
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-coffee-600 uppercase tracking-widest ml-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-coffee-400">
                        <i class="fas fa-lock text-xs"></i>
                    </span>
                    <input type="password" name="password" required 
                        class="w-full bg-coffee-50/20 border border-coffee-100 rounded-2xl pl-11 pr-4 py-3.5 text-xs font-semibold text-coffee-950 outline-none focus:border-coffee-400 focus:bg-white transition-all shadow-sm placeholder:text-coffee-300"
                        placeholder="••••••••">
                </div>
            </div>

            <!-- Options -->
            <div class="flex items-center justify-between ml-1 text-[10px] font-bold text-coffee-600 uppercase">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="remember" value="1" class="rounded border-coffee-200 text-coffee focus:ring-coffee w-3.5 h-3.5">
                    <span>Ingat saya</span>
                </label>
                <a href="#" class="tracking-widest hover:text-coffee-950 transition-colors">Lupa Password?</a>
            </div>

            <!-- Submit -->
            <button class="w-full bg-coffee text-white hover:bg-coffee-dark py-4 rounded-2xl font-bold text-xs shadow-lg shadow-coffee-700/10 hover:scale-[1.01] active:scale-[0.99] transition-all cursor-pointer">
                MASUK SEKARANG
            </button>
        </form>
    </main>
</body>
</html>
