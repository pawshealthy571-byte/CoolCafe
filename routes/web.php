<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Hash;

function logActivity(string $action, string $description) {
    if (Auth::check()) {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}

Route::get('/', function () {
    return view('welcome');
});

Route::get('/menu', function () {
    $menus = App\Models\Menu::where('is_available', true)->get();
    return view('menu', ['menus' => $menus]);
});

Route::get('/cashier', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    if (! in_array(Auth::user()->role, ['cashier', 'admin', 'superadmin', 'manager'], true)) {
        abort(403);
    }

    return view('dashboard.cashier');
});

Route::get('/chef', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    if (! in_array(Auth::user()->role, ['chef', 'admin', 'superadmin', 'manager'], true)) {
        abort(403);
    }

    return view('dashboard.chef');
});

Route::get('/forgot-password', function () {
    return view('forgot-password');
});

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    // In a real app, send reset link email here.
    // For now, we simulate success.
    return back()->with('status', 'Instruksi pemulihan telah dikirim ke email Anda.');
});

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return view('login', [
        'mode' => 'unified',
        'title' => 'Portal Masuk',
        'subtitle' => 'Masuk ke sistem CoolCafe menggunakan akun Anda.',
        'action' => '/login',
    ]);
});

Route::post('/login', function (Request $request) {
    return attemptRoleLogin($request, ['superadmin', 'admin', 'manager', 'cashier', 'chef'], '/dashboard');
});

// Redirect old login paths to the new unified login
Route::get('/login/pegawai', fn() => redirect('/login'));
Route::get('/login/admin', fn() => redirect('/login'));
Route::get('/login/kasir', fn() => redirect('/login'));
Route::get('/login/chef', fn() => redirect('/login'));

Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
});

Route::get('/admin', fn () => redirect('/dashboard/admin'));
Route::get('/manager', fn () => redirect('/dashboard/manager'));
Route::get('/superadmin', fn () => redirect('/dashboard/superadmin'));

Route::post('/admin/upload-image', function (Request $request) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $request->validate([
        'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
    ]);

    $path = $request->file('image')->store('menu-images', 'public');
    
    return response()->json([
        'path' => Storage::url($path),
    ]);
});

Route::get('/sales-report', function (Request $request) {
    if (! userHasRole(['manager', 'admin', 'superadmin', 'cashier'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $allSales = readCoolCafeSales();
    
    if ($request->has('all')) {
        return response()->json($allSales);
    }

    $date = $request->query('date', now()->timezone(config('app.timezone'))->toDateString());
    $sales = collect($allSales)->filter(fn ($sale) => ($sale['completedDate'] ?? $sale['date'] ?? '') === $date)->values();
    
    return response()->json([
        'date' => $date,
        'transactions' => $sales->count(),
        'revenue' => $sales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
        'sales' => $sales->sortByDesc(fn ($sale) => $sale['completedAt'] ?? $sale['time'] ?? '')->values()->all(),
    ]);
});

Route::get('/dashboard', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    $role = Auth::user()->role;
    if ($role === 'cashier') return redirect('/cashier');
    if ($role === 'chef') return redirect('/chef');
    
    return redirect('/dashboard/'.$role);
});

Route::get('/dashboard/{role}', function (string $role) {
    if (! Auth::check()) {
        return redirect('/login');
    }

    $user = Auth::user();
    
    // Automatic redirection logic
    if ($user->role === 'cashier') return redirect('/cashier');
    if ($user->role === 'chef') return redirect('/chef');
    
    // If Admin/Manager tries to access someone else's dashboard or invalid role
    if (!in_array($user->role, ['superadmin', 'admin', 'manager'], true)) {
        abort(403);
    }

    $allowedRoles = ['superadmin', 'admin', 'manager'];
    if (! in_array($role, $allowedRoles, true)) {
        return redirect('/dashboard/'.$user->role);
    }

    $sales = collect(readCoolCafeSales());
    $orders = collect(readCoolCafeOrders());
    $expenses = collect(readCoolCafeExpenses());
    $users = User::orderBy('role')->orderBy('name')->get();
    $today = now()->timezone(config('app.timezone'))->toDateString();
    $todaySales = $sales->filter(fn ($sale) => ($sale['completedDate'] ?? $sale['date'] ?? '') === $today);
    $todayExpenses = $expenses->filter(fn ($expense) => ($expense['date'] ?? '') === $today);

    return view('dashboard.admin', [
        'role' => $role,
        'user' => $user,
        'users' => $users,
        'activeOrders' => $orders->count(),
        'todayRevenue' => $todaySales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
        'todayTransactions' => $todaySales->count(),
        'totalRevenue' => $sales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
        'todayExpenses' => $todayExpenses->sum(fn ($expense) => (float) ($expense['amount'] ?? 0)),
        'totalExpenses' => $expenses->sum(fn ($expense) => (float) ($expense['amount'] ?? 0)),
    ]);
});

Route::get('/qris', function (Request $request) {
    return view('qris', [
        'table' => $request->query('table', '-'),
        'total' => (float) $request->query('total', 0),
    ]);
});

Route::get('/estimation', function (Request $request) {
    $ordersCount = count(readCoolCafeOrders());
    // Estimate: 5 minutes per order, minimum 5-10 mins, maximum added per order
    $minMinutes = 5 + ($ordersCount * 2);
    $maxMinutes = 10 + ($ordersCount * 3);
    
    return view('estimation', [
        'table' => $request->query('table', '-'),
        'estimation' => "{$minMinutes} - {$maxMinutes} Menit"
    ]);
});

Route::get('/orders', function () {
    if (! userHasRole(['cashier', 'chef', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    return response()->json(readCoolCafeOrders());
});

Route::post('/orders', function (Request $request) {
    $data = $request->validate([
        'table' => ['required', 'string', 'max:20'],
        'payment' => ['required', 'string', 'max:20'],
        'orderNote' => ['nullable', 'string', 'max:1000'],
        'items' => ['required', 'array', 'min:1'],
        'items.*.name' => ['required', 'string', 'max:120'],
        'items.*.price' => ['required', 'numeric', 'min:0'],
        'items.*.quantity' => ['required', 'integer', 'min:1'],
        'items.*.options' => ['nullable', 'array'],
        'subtotal' => ['required', 'numeric', 'min:0'],
        'tax' => ['required', 'numeric', 'min:0'],
        'total' => ['required', 'numeric', 'min:0'],
    ]);

    $orders = readCoolCafeOrders();
    $orders[] = [
        'id' => now()->timestamp . random_int(100, 999),
        'table' => $data['table'],
        'payment' => $data['payment'],
        'orderNote' => $data['orderNote'] ?? '',
        'items' => $data['items'],
        'subtotal' => $data['subtotal'],
        'tax' => $data['tax'],
        'total' => $data['total'],
        'date' => now()->timezone(config('app.timezone'))->toDateString(),
        'time' => now()->timezone(config('app.timezone'))->format('H:i:s'),
        'status' => 'pending'
    ];

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    logActivity('Pesanan Baru', "Kasir membuat pesanan untuk Meja {$data['table']}");

    return response()->json(['ok' => true]);
});

Route::post('/orders/{id}/complete', function ($id) {
    if (! userHasRole(['cashier', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $orders = readCoolCafeOrders();
    $order = collect($orders)->first(fn ($order) => (string) ($order['id'] ?? '') === (string) $id);

    if (! $order) {
        return response()->json(['ok' => false, 'message' => 'Order tidak ditemukan'], 404);
    }

    $sales = readCoolCafeSales();
    $sales[] = array_merge($order, [
        'completedAt' => now()->timezone(config('app.timezone'))->toDateTimeString(),
        'completedDate' => now()->timezone(config('app.timezone'))->toDateString(),
        'completedTime' => now()->timezone(config('app.timezone'))->format('H:i:s'),
    ]);

    $orders = collect($orders)
        ->reject(fn ($order) => (string) ($order['id'] ?? '') === (string) $id)
        ->values()
        ->all();

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    Storage::disk('local')->put('coolcafe_sales.json', json_encode($sales, JSON_PRETTY_PRINT));
    logActivity('Selesaikan Pesanan', "Kasir menyelesaikan pesanan untuk Meja {$order['table']}");

    return response()->json(['ok' => true]);
});

Route::post('/orders/{id}/ready', function ($id) {
    if (! userHasRole(['chef', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $orders = readCoolCafeOrders();
    $foundOrder = null;
    foreach ($orders as &$order) {
        if ((string) ($order['id'] ?? '') === (string) $id) {
            $order['status'] = 'ready';
            $foundOrder = $order;
            break;
        }
    }

    if (! $foundOrder) {
        return response()->json(['ok' => false, 'message' => 'Order tidak ditemukan'], 404);
    }

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    logActivity('Pesanan Siap', "Chef menandai pesanan Meja {$foundOrder['table']} sebagai siap");

    return response()->json(['ok' => true]);
});

Route::delete('/orders/{id}', function ($id) {
    if (! userHasRole(['cashier', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $orders = readCoolCafeOrders();
    $order = collect($orders)->first(fn ($order) => (string) ($order['id'] ?? '') === (string) $id);

    $orders = collect($orders)
        ->reject(fn ($order) => (string) ($order['id'] ?? '') === (string) $id)
        ->values()
        ->all();

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));
    if ($order) {
        logActivity('Batalkan Pesanan', "Kasir membatalkan pesanan untuk Meja {$order['table']}");
    }

    return response()->json(['ok' => true]);
});

Route::delete('/orders', function () {
    if (! userHasRole(['cashier', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    Storage::disk('local')->put('coolcafe_orders.json', json_encode([], JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

Route::get('/admin/management/tables', function () {
    if (! userHasRole(['admin', 'superadmin'])) abort(403);
    return view('dashboard.tables');
});

Route::get('/admin/management/users', function () {
    if (! userHasRole(['admin', 'superadmin'])) abort(403);
    return view('dashboard.users.index');
});

Route::get('/admin/management/ingredients', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        abort(403);
    }
    return view('dashboard.ingredients');
});

Route::get('/admin/management/menus', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        abort(403);
    }
    return view('dashboard.menus');
});

Route::get('/admin/management/reports', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        abort(403);
    }
    return view('dashboard.reports');
});

Route::get('/admin/users', function (Request $request) {
    if (! userHasRole(['admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $role = $request->query('role');
    $query = App\Models\User::orderBy('name');
    if ($role) {
        $query->where('role', $role);
    }
    return response()->json($query->get());
});

Route::post('/admin/users', function (Request $request) {
    if (! userHasRole(['admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'min:8'],
        'role' => ['required', 'string', 'in:admin,cashier,chef,manager,superadmin'],
    ]);
    $data['password'] = Hash::make($data['password']);
    $user = App\Models\User::create($data);
    logActivity('Buat Pengguna', "mendaftarkan pengguna baru: {$user->name}");
    return response()->json($user);
});

Route::put('/admin/users/{id}', function (Request $request, $id) {
    if (! userHasRole(['admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $user = App\Models\User::findOrFail($id);
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
        'password' => ['nullable', 'string', 'min:8'],
        'role' => ['required', 'string', 'in:admin,cashier,chef,manager,superadmin'],
    ]);
    if (!empty($data['password'])) {
        $data['password'] = Hash::make($data['password']);
    } else {
        unset($data['password']);
    }
    $user->update($data);
    return response()->json($user);
});

Route::delete('/admin/users/{id}', function ($id) {
    if (! userHasRole(['admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $user = App\Models\User::findOrFail($id);
    logActivity('Hapus Pengguna', "menghapus akun pengguna: {$user->name}");
    $user->delete();
    return response()->json(['ok' => true]);
});

Route::get('/admin/expenses', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    return response()->json(readCoolCafeExpenses());
});

Route::get('/admin/ingredients', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin', 'chef'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    return response()->json(readCoolCafeIngredients());
});

Route::post('/admin/ingredients', function (Request $request) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'stock' => ['required', 'integer', 'min:0'],
        'unit' => ['required', 'string', 'max:50'],
    ]);
    
    $ingredients = readCoolCafeIngredients();
    $data['id'] = now()->timestamp;
    $ingredients[] = $data;
    Storage::disk('local')->put('coolcafe_ingredients.json', json_encode($ingredients, JSON_PRETTY_PRINT));
    
    return response()->json($data);
});

Route::put('/admin/ingredients/{id}', function (Request $request, $id) {
    if (! userHasRole(['manager', 'admin', 'superadmin', 'chef'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $ingredients = readCoolCafeIngredients();
    $found = false;
    foreach ($ingredients as &$ingredient) {
        if ((string)($ingredient['id'] ?? '') === (string)$id) {
            $ingredient['stock'] = $request->input('stock');
            $found = true;
            break;
        }
    }
    if (!$found) return response()->json(['message' => 'Not found'], 404);
    Storage::disk('local')->put('coolcafe_ingredients.json', json_encode($ingredients, JSON_PRETTY_PRINT));
    return response()->json(['ok' => true]);
});

Route::delete('/admin/ingredients/{id}', function ($id) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    $ingredients = collect(readCoolCafeIngredients())->reject(fn($i) => (string)($i['id'] ?? '') === (string)$id)->values()->all();
    Storage::disk('local')->put('coolcafe_ingredients.json', json_encode($ingredients, JSON_PRETTY_PRINT));
    return response()->json(['ok' => true]);
});

Route::get('/admin/menus', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin', 'cashier'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    return response()->json(App\Models\Menu::all());
});

Route::post('/admin/menus', [\App\Http\Controllers\MenuController::class, 'store']);

Route::match(['post', 'put'], '/admin/menus/{id}', [\App\Http\Controllers\MenuController::class, 'update']);

Route::delete('/admin/menus/{id}', function ($id) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $menu = App\Models\Menu::findOrFail($id);
    logActivity('Hapus Menu', "menghapus menu: {$menu->name}");
    $menu->delete();

    return response()->json(['ok' => true]);
});

Route::delete('/sales-report', function () {
    if (! userHasRole(['admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    Storage::disk('local')->put('coolcafe_sales.json', json_encode([], JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

require __DIR__.'/voucher.php';

Route::get('/admin/activity-logs', function () {
    if (! userHasRole(['admin', 'superadmin'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }
    return response()->json(ActivityLog::with('user')->latest()->take(50)->get());
});

Route::get('/admin/management/activity-logs', function () {
    if (! userHasRole(['admin', 'superadmin'])) abort(403);
    return view('dashboard.activity_logs');
});

Route::get('/admin/management/trash', function () {
    if (! userHasRole(['admin', 'superadmin'])) abort(403);
    return view('dashboard.trash');
});

Route::get('/admin/management/backup', function () {
    if (! userHasRole(['admin', 'superadmin'])) abort(403);
    return view('dashboard.backup');
});

Route::get('/api/admin/backup/download', function () {
    if (! userHasRole(['admin', 'superadmin'])) return response()->json(['message' => 'Forbidden.'], 403);

    $zipFile = storage_path('app/backup_coolcafe_' . now()->format('Y_m_d_His') . '.zip');
    $zip = new \ZipArchive();
    
    if ($zip->open($zipFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === TRUE) {
        $dbPath = database_path('database.sqlite');
        if (file_exists($dbPath)) {
            $zip->addFile($dbPath, 'database.sqlite');
        }
        
        $jsonFiles = ['coolcafe_orders.json', 'coolcafe_sales.json', 'coolcafe_ingredients.json'];
        foreach ($jsonFiles as $file) {
            $path = storage_path('app/' . $file);
            if (file_exists($path)) {
                $zip->addFile($path, $file);
            }
        }
        
        $zip->close();
    }
    
    logActivity('Backup Database', 'mengunduh salinan backup database sistem');
    
    return response()->download($zipFile)->deleteFileAfterSend(true);
});

Route::get('/api/admin/trash', function () {
    if (! userHasRole(['admin', 'superadmin'])) return response()->json(['message' => 'Forbidden.'], 403);
    return response()->json([
        'menus' => App\Models\Menu::onlyTrashed()->get(),
        'users' => App\Models\User::onlyTrashed()->where('id', '!=', Auth::id())->get()
    ]);
});

Route::post('/api/admin/trash/restore', function (Illuminate\Http\Request $request) {
    if (! userHasRole(['admin', 'superadmin'])) return response()->json(['message' => 'Forbidden.'], 403);
    $type = $request->input('type');
    $id = $request->input('id');
    if ($type === 'menu') {
        $menu = App\Models\Menu::onlyTrashed()->findOrFail($id);
        $menu->restore();
        logActivity('Restore Menu', "memulihkan menu: {$menu->name}");
    } else if ($type === 'user') {
        $user = App\Models\User::onlyTrashed()->findOrFail($id);
        $user->restore();
        logActivity('Restore Pengguna', "memulihkan akun pengguna: {$user->name}");
    }
    return response()->json(['ok' => true]);
});

Route::delete('/api/admin/trash/force-delete', function (Illuminate\Http\Request $request) {
    if (! userHasRole(['admin', 'superadmin'])) return response()->json(['message' => 'Forbidden.'], 403);
    $type = $request->input('type');
    $id = $request->input('id');
    if ($type === 'menu') {
        $menu = App\Models\Menu::onlyTrashed()->findOrFail($id);
        $name = $menu->name;
        $menu->forceDelete();
        logActivity('Hapus Permanen Menu', "menghapus permanen menu: {$name}");
    } else if ($type === 'user') {
        $user = App\Models\User::onlyTrashed()->findOrFail($id);
        $name = $user->name;
        $user->forceDelete();
        logActivity('Hapus Permanen Pengguna', "menghapus permanen pengguna: {$name}");
    }
    return response()->json(['ok' => true]);
});

Route::post('/api/chef/notify-restock', function (Illuminate\Http\Request $request) {
    if (! userHasRole(['chef', 'admin', 'superadmin', 'manager'])) return response()->json(['message' => 'Forbidden.'], 403);
    
    $items = $request->input('items', 'beberapa bahan baku');
    logActivity('Permintaan Restock', "Mendesak: Chef meminta pembelian ulang untuk bahan baku berikut: {$items}.");
    
    return response()->json(['ok' => true]);
});
