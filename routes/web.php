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

    if (! in_array(Auth::user()->role, ['cashier', 'admin', 'superadmin'], true)) {
        abort(403);
    }

    return view('dashboard.cashier');
});

Route::get('/chef', function () {
    if (! Auth::check()) {
        return redirect('/login');
    }

    if (! in_array(Auth::user()->role, ['chef', 'admin', 'superadmin'], true)) {
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
        'demoUsers' => [
            'admin@coolcafe.test / password',
            'kasir@coolcafe.test / password',
            'chef@coolcafe.test / password',
        ],
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
    $users = User::orderBy('role')->orderBy('name')->get();
    $today = now()->timezone(config('app.timezone'))->toDateString();
    $todaySales = $sales->filter(fn ($sale) => ($sale['completedDate'] ?? $sale['date'] ?? '') === $today);

    return view('dashboard.admin', [
        'role' => $role,
        'user' => $user,
        'users' => $users,
        'activeOrders' => $orders->count(),
        'todayRevenue' => $todaySales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
        'todayTransactions' => $todaySales->count(),
        'totalRevenue' => $sales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
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
    if (! userHasRole(['cashier', 'chef', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
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
    if (! userHasRole(['cashier', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
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
    if (! userHasRole(['chef', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
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
    if (! userHasRole(['cashier', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $orders = collect(readCoolCafeOrders())
        ->reject(fn ($order) => (string) ($order['id'] ?? '') === (string) $id)
        ->values()
        ->all();

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

Route::delete('/orders', function () {
    if (! userHasRole(['cashier', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    Storage::disk('local')->put('coolcafe_orders.json', json_encode([], JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

Route::get('/sales-report', function (Request $request) {
    if (! userHasRole(['manager', 'cashier', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $date = $request->query('date', now()->timezone(config('app.timezone'))->toDateString());
    $sales = collect(readCoolCafeSales())
        ->filter(fn ($sale) => ($sale['completedDate'] ?? $sale['date'] ?? '') === $date)
        ->values();

    $paymentSummary = $sales
        ->groupBy(fn ($sale) => $sale['payment'] ?? 'Lainnya')
        ->map(fn ($rows) => [
            'count' => $rows->count(),
            'total' => $rows->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
        ])
        ->all();

    $itemSummary = $sales
        ->flatMap(fn ($sale) => collect($sale['items'] ?? [])->map(fn ($item) => [
            'name' => $item['name'] ?? 'Item',
            'quantity' => (int) ($item['quantity'] ?? 0),
            'total' => (float) ($item['price'] ?? 0) * (int) ($item['quantity'] ?? 0),
        ]))
        ->groupBy('name')
        ->map(fn ($rows, $name) => [
            'name' => $name,
            'quantity' => $rows->sum('quantity'),
            'total' => $rows->sum('total'),
        ])
        ->sortByDesc('quantity')
        ->values()
        ->take(5)
        ->all();

    return response()->json([
        'date' => $date,
        'transactions' => $sales->count(),
        'revenue' => $sales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)),
        'averageTransaction' => $sales->count() ? round($sales->sum(fn ($sale) => (float) ($sale['total'] ?? 0)) / $sales->count()) : 0,
        'paymentSummary' => $paymentSummary,
        'topItems' => $itemSummary,
        'sales' => $sales->sortByDesc(fn ($sale) => $sale['completedAt'] ?? $sale['time'] ?? '')->values()->all(),
    ]);
});

Route::get('/admin/management/menus', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        abort(403);
    }
    return view('dashboard.menus');
});

Route::get('/admin/menus', function () {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    return response()->json(App\Models\Menu::all());
});

Route::post('/admin/menus', function (Request $request) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'category' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'description' => ['nullable', 'string'],
        'add_ons' => ['nullable'],
        'is_available' => ['required'],
        'image_file' => ['nullable', 'image', 'max:5120'],
    ]);

    if (isset($data['add_ons']) && is_string($data['add_ons'])) {
        $data['add_ons'] = json_decode($data['add_ons'], true);
    }

    $data['is_available'] = filter_var($request->input('is_available') ?? true, FILTER_VALIDATE_BOOLEAN);

    $imageError = storeMenuImage($request, $data);
    if ($imageError) {
        return response()->json(['message' => $imageError], 422);
    }

    $menu = App\Models\Menu::create($data);

    return response()->json($menu);
});

Route::match(['post', 'put'], '/admin/menus/{id}', function (Request $request, $id) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    $menu = App\Models\Menu::findOrFail($id);

    $data = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'category' => ['required', 'string', 'max:255'],
        'price' => ['required', 'numeric', 'min:0'],
        'description' => ['nullable', 'string'],
        'add_ons' => ['nullable'],
        'is_available' => ['nullable'],
        'image_file' => ['nullable', 'image', 'max:5120'],
    ]);

    if (isset($data['add_ons']) && is_string($data['add_ons'])) {
        $data['add_ons'] = json_decode($data['add_ons'], true);
    }

    $data['is_available'] = filter_var($request->input('is_available') ?? true, FILTER_VALIDATE_BOOLEAN);

    $imageError = storeMenuImage($request, $data, $menu);
    if ($imageError) {
        return response()->json(['message' => $imageError], 422);
    }

    $menu->update($data);

    return response()->json($menu);
});

Route::delete('/admin/menus/{id}', function ($id) {
    if (! userHasRole(['manager', 'admin', 'superadmin'])) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
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

function readCoolCafeSales(): array
{
    if (! Storage::disk('local')->exists('coolcafe_sales.json')) {
        return [];
    }

    $sales = json_decode(Storage::disk('local')->get('coolcafe_sales.json'), true);

    return is_array($sales) ? $sales : [];
}
