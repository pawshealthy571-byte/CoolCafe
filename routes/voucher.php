<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Menu;

// Rute manajemen voucher
Route::get('/admin/management/vouchers', function () {
    if (!userHasRole(['admin', 'superadmin'])) abort(403);
    return view('dashboard.vouchers');
});

Route::get('/admin/vouchers', function () {
    if (!userHasRole(['admin', 'superadmin', 'cashier'])) abort(403);
    return response()->json(readCoolCafeVouchers());
});

Route::post('/admin/vouchers', function (Request $request) {
    if (!userHasRole(['admin', 'superadmin'])) abort(403);
    $data = $request->validate([
        'code' => 'required|string|unique:vouchers,code', // Perlu penyesuaian jika tidak pakai DB
        'type' => 'required|in:percentage,fixed',
        'value' => 'required|numeric',
        'min_purchase' => 'required|numeric',
        'is_active' => 'boolean'
    ]);
    
    $vouchers = readCoolCafeVouchers();
    $data['id'] = now()->timestamp;
    $vouchers[] = $data;
    writeCoolCafeVouchers($vouchers);
    
    return response()->json(['ok' => true]);
});

// Tambahkan rute PUT dan DELETE voucher di sini jika perlu
