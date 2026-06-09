<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

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

function attemptRoleLogin(Request $request, array $roles, string $redirectTo)
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($credentials, $request->boolean('remember'))) {
        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    if (! in_array(Auth::user()->role, $roles, true)) {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()
            ->withErrors(['email' => 'Akun ini tidak memiliki akses ke portal ini.'])
            ->onlyInput('email');
    }

    $request->session()->regenerate();

    return redirect()->intended($redirectTo);
}

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
    return view('estimation', [
        'table' => $request->query('table', '-')
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

    return response()->json(['ok' => true]);
});

Route::post('/orders/{id}/ready', function ($id) {
    if (! userHasRole(['chef', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $orders = readCoolCafeOrders();
    $found = false;
    foreach ($orders as &$order) {
        if ((string) ($order['id'] ?? '') === (string) $id) {
            $order['status'] = 'ready';
            $found = true;
            break;
        }
    }

    if (! $found) {
        return response()->json(['ok' => false, 'message' => 'Order tidak ditemukan'], 404);
    }

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

Route::delete('/orders/{id}', function ($id) {
    if (! userHasRole(['cashier', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    $orders = collect(readCoolCafeOrders())
        ->reject(fn ($order) => (string) ($order['id'] ?? '') === (string) $id)
        ->values()
        ->all();

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

Route::delete('/orders', function () {
    if (! userHasRole(['cashier', 'admin', 'superadmin', 'manager'])) {
        return response()->json(['message' => 'Forbidden.'], 403);
    }

    Storage::disk('local')->put('coolcafe_orders.json', json_encode([], JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
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

function readCoolCafeOrders(): array
{
    if (! Storage::disk('local')->exists('coolcafe_orders.json')) {
        return [];
    }

    $orders = json_decode(Storage::disk('local')->get('coolcafe_orders.json'), true);

    return is_array($orders) ? $orders : [];
}

function storeMenuImage(Request $request, array &$data, ?App\Models\Menu $menu = null): ?string
{
    $uploadedImage = $request->file('image_file');

    if (! $uploadedImage) {
        return null;
    }

    if (! $uploadedImage->isValid()) {
        return 'Upload gambar gagal: '.$uploadedImage->getErrorMessage();
    }

    if ($menu?->image && ! str_starts_with($menu->image, 'http')) {
        Storage::disk('public')->delete($menu->image);
    }

    $data['image'] = $uploadedImage->store('menus', 'public');

    return null;
}

function userHasRole(array $roles): bool
{
    return Auth::check() && in_array(Auth::user()->role, $roles, true);
}

require __DIR__.'/voucher.php';

function readCoolCafeSales(): array
{
    if (! Storage::disk('local')->exists('coolcafe_sales.json')) {
        return [];
    }

    $sales = json_decode(Storage::disk('local')->get('coolcafe_sales.json'), true);

    return is_array($sales) ? $sales : [];
}

function readCoolCafeVouchers(): array
{
    if (! Storage::disk('local')->exists('coolcafe_vouchers.json')) {
        return [];
    }

    $vouchers = json_decode(Storage::disk('local')->get('coolcafe_vouchers.json'), true);

    return is_array($vouchers) ? $vouchers : [];
}

function writeCoolCafeVouchers(array $vouchers): void
{
    Storage::disk('local')->put('coolcafe_vouchers.json', json_encode($vouchers, JSON_PRETTY_PRINT));
}

function readCoolCafeIngredients(): array
{
    if (! Storage::disk('local')->exists('coolcafe_ingredients.json')) {
        return [];
    }

    $ingredients = json_decode(Storage::disk('local')->get('coolcafe_ingredients.json'), true);

    return is_array($ingredients) ? $ingredients : [];
}

function readCoolCafeExpenses(): array
{
    if (! Storage::disk('local')->exists('coolcafe_expenses.json')) {
        return [];
    }

    $expenses = json_decode(Storage::disk('local')->get('coolcafe_expenses.json'), true);

    return is_array($expenses) ? $expenses : [];
}


