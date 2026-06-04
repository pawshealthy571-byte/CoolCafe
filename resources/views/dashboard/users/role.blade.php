@extends('layouts.app')

@section('title', 'Manajemen User ' . ucfirst($role))

@section('content')
<div class="mb-8">
    <h2 class="text-2xl font-bold text-gray-800">Manajemen User {{ ucfirst($role) }}</h2>
    <p class="text-gray-500 text-sm">Kelola data akun untuk role {{ ucfirst($role) }}.</p>
</div>

<div class="card p-6 mb-8">
    <p class="text-sm text-gray-600">Interface manajemen untuk role <strong>{{ ucfirst($role) }}</strong> akan segera hadir.</p>
</div>
@endsection
