<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Menu;

if (!function_exists('attemptRoleLogin')) {
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
}

if (!function_exists('readCoolCafeOrders')) {
    function readCoolCafeOrders(): array
    {
        if (! Storage::disk('local')->exists('coolcafe_orders.json')) {
            return [];
        }

        $orders = json_decode(Storage::disk('local')->get('coolcafe_orders.json'), true);

        return is_array($orders) ? $orders : [];
    }
}

if (!function_exists('storeMenuImage')) {
    function storeMenuImage(Request $request, array &$data, ?Menu $menu = null): ?string
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
}

if (!function_exists('userHasRole')) {
    function userHasRole(array $roles): bool
    {
        return Auth::check() && in_array(Auth::user()->role, $roles, true);
    }
}

if (!function_exists('readCoolCafeSales')) {
    function readCoolCafeSales(): array
    {
        if (! Storage::disk('local')->exists('coolcafe_sales.json')) {
            return [];
        }

        $sales = json_decode(Storage::disk('local')->get('coolcafe_sales.json'), true);

        return is_array($sales) ? $sales : [];
    }
}

if (!function_exists('readCoolCafeVouchers')) {
    function readCoolCafeVouchers(): array
    {
        if (! Storage::disk('local')->exists('coolcafe_vouchers.json')) {
            return [];
        }

        $vouchers = json_decode(Storage::disk('local')->get('coolcafe_vouchers.json'), true);

        return is_array($vouchers) ? $vouchers : [];
    }
}

if (!function_exists('writeCoolCafeVouchers')) {
    function writeCoolCafeVouchers(array $vouchers): void
    {
        Storage::disk('local')->put('coolcafe_vouchers.json', json_encode($vouchers, JSON_PRETTY_PRINT));
    }
}

if (!function_exists('readCoolCafeIngredients')) {
    function readCoolCafeIngredients(): array
    {
        if (! Storage::disk('local')->exists('coolcafe_ingredients.json')) {
            return [];
        }

        $ingredients = json_decode(Storage::disk('local')->get('coolcafe_ingredients.json'), true);

        return is_array($ingredients) ? $ingredients : [];
    }
}

if (!function_exists('readCoolCafeExpenses')) {
    function readCoolCafeExpenses(): array
    {
        if (! Storage::disk('local')->exists('coolcafe_expenses.json')) {
            return [];
        }

        $expenses = json_decode(Storage::disk('local')->get('coolcafe_expenses.json'), true);

        return is_array($expenses) ? $expenses : [];
    }
}
