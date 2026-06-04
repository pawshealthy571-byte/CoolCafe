<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - {{ $title }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FDFDFC;
            background-image: radial-gradient(#63483210 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .bg-coffee { background-color: #634832; }
        .text-coffee { color: #634832; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-md bg-white rounded-[2rem] shadow-2xl shadow-coffee/10 overflow-hidden border border-gray-100">
        <div class="bg-coffee text-white p-8 text-center relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute -left-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner">
                <i class="fas fa-coffee text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold mb-1">CoolCafe</h1>
            <p class="text-xs text-white/70 uppercase tracking-widest font-semibold">Portal Masuk Staff</p>
        </div>

        <form method="POST" action="{{ $action }}" class="p-8 space-y-6">
            @csrf
            <div class="text-center mb-2">
                <p class="text-sm text-gray-500">{{ $subtitle }}</p>
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Email Address</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus 
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl pl-11 pr-4 py-3.5 text-sm outline-none focus:border-coffee/30 focus:bg-white transition-all shadow-sm"
                        placeholder="your@email.com">
                </div>
                @error('email')
                    <p class="text-[10px] text-red-500 mt-2 ml-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Password</label>
                <div class="relative mt-2">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" required 
                        class="w-full bg-gray-50 border border-gray-100 rounded-2xl pl-11 pr-4 py-3.5 text-sm outline-none focus:border-coffee/30 focus:bg-white transition-all shadow-sm"
                        placeholder="••••••••">
                </div>
            </div>

            <div class="flex items-center justify-between ml-1">
                <label class="flex items-center gap-2 text-[10px] font-bold text-gray-400 uppercase cursor-pointer">
                    <input type="checkbox" name="remember" value="1" class="rounded border-gray-300 text-coffee focus:ring-coffee">
                    Ingat saya
                </label>
                <a href="#" class="text-[10px] font-bold text-coffee uppercase tracking-widest">Lupa?</a>
            </div>

            <button class="w-full bg-coffee text-white py-4 rounded-2xl font-bold text-sm shadow-lg shadow-coffee/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                MASUK SEKARANG
            </button>

        </form>
    </main>
</body>
</html>
