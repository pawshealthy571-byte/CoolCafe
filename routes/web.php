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
    return view('menu');
});

Route::get('/cashier', function () {
    if (! Auth::check()) {
        return redirect('/login/kasir');
    }

    if (Auth::user()->role !== 'cashier') {
        abort(403);
    }

    return view()->file(resource_path('views/dashboard.cashier.blade.php'));
});

Route::get('/login', function () {
    return redirect('/login/admin');
});

Route::get('/login/kasir', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return view('login', [
        'mode' => 'kasir',
        'title' => 'Login Kasir',
        'subtitle' => 'Khusus akun kasir untuk membuka dashboard kasir.',
        'action' => '/login/kasir',
        'demoUsers' => [
            'kasir@coolcafe.test / password',
        ],
    ]);
});

Route::post('/login/kasir', function (Request $request) {
    return attemptRoleLogin($request, ['cashier'], '/cashier');
});

Route::get('/login/admin', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }

    return view('login', [
        'mode' => 'admin',
        'title' => 'Login Admin',
        'subtitle' => 'Untuk superadmin, admin, dan manager CoolCafe.',
        'action' => '/login/admin',
        'demoUsers' => [
            'superadmin@coolcafe.test / password',
            'admin@coolcafe.test / password',
            'manager@coolcafe.test / password',
        ],
    ]);
});

Route::post('/login/admin', function (Request $request) {
    return attemptRoleLogin($request, ['superadmin', 'admin', 'manager'], '/dashboard');
});

Route::post('/login', function (Request $request) {
    return attemptRoleLogin($request, ['superadmin', 'admin', 'manager', 'cashier'], '/dashboard');
});

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
            ->withErrors(['email' => 'Akun ini tidak punya akses ke halaman login tersebut.'])
            ->onlyInput('email');
    }

    $request->session()->regenerate();

    return redirect()->intended($redirectTo);
}

Route::post('/logout', function (Request $request) {
    $loginPath = Auth::user()?->role === 'cashier' ? '/login/kasir' : '/login/admin';

    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect($loginPath);
});

Route::get('/dashboard', function () {
    if (! Auth::check()) {
        return redirect('/login/admin');
    }

    return redirect('/dashboard/'.Auth::user()->role);
});

Route::get('/dashboard/{role}', function (string $role) {
    if (! Auth::check()) {
        return redirect('/login/admin');
    }

    $user = Auth::user();
    $allowedRoles = ['superadmin', 'admin', 'manager', 'cashier'];

    if ($role === 'cashier' && $user->role !== 'cashier') {
        abort(403);
    }

    $canOpenDashboard = $user->role === 'superadmin' || $user->role === $role;

    if (! in_array($role, $allowedRoles, true) || ! $canOpenDashboard) {
        abort(403);
    }

    $sales = collect(readCoolCafeSales());
    $orders = collect(readCoolCafeOrders());
    $users = User::orderBy('role')->orderBy('name')->get();
    $today = now()->timezone(config('app.timezone'))->toDateString();
    $todaySales = $sales->filter(fn ($sale) => ($sale['completedDate'] ?? $sale['date'] ?? '') === $today);

    if ($role === 'cashier') {
        return redirect('/cashier');
    }

    return view()->file(resource_path('views/dashboard.admin.blade.php'), [
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

Route::get('/orders', function () {
    if (! userHasRole(['cashier'])) {
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
        'total' => ['required', 'numeric', 'min:0'],
    ]);

    $orders = readCoolCafeOrders();
    $orders[] = [
        'id' => now()->timestamp . random_int(100, 999),
        'table' => $data['table'],
        'payment' => $data['payment'],
        'orderNote' => $data['orderNote'] ?? '',
        'items' => $data['items'],
        'total' => $data['total'],
        'date' => now()->timezone(config('app.timezone'))->toDateString(),
        'time' => now()->timezone(config('app.timezone'))->format('H:i:s'),
    ];

    Storage::disk('local')->put('coolcafe_orders.json', json_encode($orders, JSON_PRETTY_PRINT));

    return response()->json(['ok' => true]);
});

Route::post('/orders/{id}/complete', function ($id) {
    if (! userHasRole(['cashier'])) {
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

Route::delete('/orders/{id}', function ($id) {
    if (! userHasRole(['cashier'])) {
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
    if (! userHasRole(['cashier'])) {
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
