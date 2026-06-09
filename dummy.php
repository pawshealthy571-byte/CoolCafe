<?php

use App\Models\Menu;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 10 Users
$roles = ['cashier', 'chef', 'manager', 'admin'];
for($i = 1; $i <= 10; $i++) {
    User::create([
        'name' => "Pegawai Dummy $i",
        'email' => "dummy$i@coolcafe.com",
        'password' => Hash::make('password123'),
        'role' => $roles[array_rand($roles)]
    ]);
}

// 10 Menus
$menuNames = ['Nasi Goreng Spesial', 'Mie Tek-Tek', 'Ayam Bakar Madu', 'Sate Ayam', 'Soto Betawi', 'Es Kopi Gula Aren', 'Matcha Latte', 'Red Velvet', 'Kentang Goreng', 'Roti Bakar Coklat Keju'];
$categories = ['Makanan', 'Makanan', 'Makanan', 'Makanan', 'Makanan', 'Minuman', 'Minuman', 'Minuman', 'Cemilan', 'Cemilan'];
for($i = 0; $i < 10; $i++) {
    Menu::create([
        'name' => $menuNames[$i],
        'category' => $categories[$i],
        'price' => rand(15, 45) * 1000,
        'description' => "Deskripsi yang sangat lezat untuk " . $menuNames[$i],
        'is_available' => true,
    ]);
}

// 10 Ingredients
$ingredients = [];
$ingNames = ['Beras', 'Kopi Arabika', 'Susu Segar', 'Gula Aren', 'Ayam', 'Minyak Goreng', 'Telur', 'Tepung Terigu', 'Teh Hitam', 'Matcha Powder'];
$ingUnits = ['Kg', 'Kg', 'Liter', 'Kg', 'Ekor', 'Liter', 'Papan', 'Kg', 'Kotak', 'Kg'];
for($i=0; $i<10; $i++) {
    $ingredients[] = [
        'id' => now()->timestamp + $i,
        'name' => $ingNames[$i],
        'stock' => rand(5, 50),
        'unit' => $ingUnits[$i]
    ];
}
Storage::disk('local')->put('coolcafe_ingredients.json', json_encode($ingredients, JSON_PRETTY_PRINT));

// 10 Vouchers
$vouchers = [];
for($i=1; $i<=10; $i++) {
    $vouchers[] = [
        'id' => now()->timestamp + $i,
        'code' => 'PROMO' . strtoupper(Str::random(5)),
        'discount' => rand(1, 5) * 10000,
        'expires_at' => now()->addDays(rand(1, 30))->toDateString(),
        'is_active' => true
    ];
}
Storage::disk('local')->put('coolcafe_vouchers.json', json_encode($vouchers, JSON_PRETTY_PRINT));

// 10 Sales (Riwayat Penjualan)
$sales = [];
for($i=1; $i<=10; $i++) {
    $date = now()->subDays(rand(0, 7));
    $total = rand(5, 20) * 10000;
    $sales[] = [
        'id' => time() + $i,
        'table' => rand(1, 15),
        'payment' => ['CASH', 'QRIS'][rand(0,1)],
        'items' => [
            ['name' => 'Menu Random', 'price' => $total, 'quantity' => 1]
        ],
        'subtotal' => $total,
        'tax' => 0,
        'total' => $total,
        'date' => $date->toDateString(),
        'time' => $date->format('H:i:s'),
        'completedDate' => $date->toDateString(),
        'completedTime' => $date->format('H:i:s'),
    ];
}
// Merge with existing
if (Storage::disk('local')->exists('coolcafe_sales.json')) {
    $existing = json_decode(Storage::disk('local')->get('coolcafe_sales.json'), true) ?? [];
    $sales = array_merge($existing, $sales);
}
Storage::disk('local')->put('coolcafe_sales.json', json_encode($sales, JSON_PRETTY_PRINT));

// 10 Activity Logs
for($i=1; $i<=10; $i++) {
    \App\Models\ActivityLog::create([
        'user_id' => User::inRandomOrder()->first()->id ?? 1,
        'action' => 'Simulasi Aktivitas',
        'description' => 'Melakukan tindakan acak pada sistem dari generate script.',
        'ip_address' => '127.0.0.1'
    ]);
}

echo "Dummy data generated successfully!";
