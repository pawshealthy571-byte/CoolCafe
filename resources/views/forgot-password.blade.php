@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Lupa Password</h2>
            <p class="text-gray-500 text-sm mt-2">Masukkan email akun Anda untuk mendapatkan instruksi pemulihan.</p>
        </div>
        <form action="/forgot-password" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest ml-1">Email</label>
                <input type="email" name="email" required class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm outline-none focus:border-coffee/30">
            </div>
            <button type="submit" class="w-full bg-coffee text-white py-3 rounded-xl font-bold text-sm hover:bg-coffee-dark transition-all">
                Kirim Instruksi
            </button>
        </form>
        <div class="mt-6 text-center">
            <a href="/login" class="text-xs text-coffee font-bold hover:underline">Kembali ke Login</a>
        </div>
    </div>
</div>
@endsection
