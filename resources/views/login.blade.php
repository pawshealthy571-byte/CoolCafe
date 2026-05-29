<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CoolCafe - {{ $title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f5f2;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden">
        <div class="bg-[#634832] text-white p-6">
            <div class="flex items-center gap-3 mb-3">
                <i class="fas fa-mug-hot text-3xl"></i>
                <h1 class="text-2xl font-bold">CoolCafe {{ $mode === 'kasir' ? 'Kasir' : 'Admin' }}</h1>
            </div>
            <p class="text-sm text-white/75">{{ $subtitle }}</p>
        </div>

        <form method="POST" action="{{ $action }}" class="p-6 space-y-5">
            @csrf
            <div>
                <label class="text-sm font-bold text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus class="mt-2 w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm outline-none focus:border-[#634832]">
                @error('email')
                    <p class="text-xs text-red-500 mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="text-sm font-bold text-gray-700">Password</label>
                <input type="password" name="password" required class="mt-2 w-full bg-gray-50 border border-gray-200 rounded-2xl px-4 py-3 text-sm outline-none focus:border-[#634832]">
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-500">
                <input type="checkbox" name="remember" value="1" class="rounded border-gray-300">
                Ingat saya
            </label>

            <button class="w-full bg-[#634832] text-white py-3 rounded-2xl font-bold">
                Masuk
            </button>

            <div class="grid grid-cols-2 gap-3">
                <a href="/login/kasir" class="text-center py-3 rounded-2xl text-sm font-bold {{ $mode === 'kasir' ? 'bg-[#634832]/10 text-[#634832]' : 'bg-gray-100 text-gray-500' }}">
                    Login Kasir
                </a>
                <a href="/login/admin" class="text-center py-3 rounded-2xl text-sm font-bold {{ $mode === 'admin' ? 'bg-[#634832]/10 text-[#634832]' : 'bg-gray-100 text-gray-500' }}">
                    Login Admin
                </a>
            </div>

            <div class="bg-gray-50 rounded-2xl p-4 text-xs text-gray-500 space-y-1">
                <p class="font-bold text-gray-700">Akun demo</p>
                @foreach ($demoUsers as $demoUser)
                    <p>{{ $demoUser }}</p>
                @endforeach
            </div>
        </form>
    </main>
</body>
</html>
