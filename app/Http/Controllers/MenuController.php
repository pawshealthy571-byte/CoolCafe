<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function store(Request $request)
    {
        if (! $this->userHasRole(['manager', 'admin', 'superadmin', 'cashier'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:menus'],
            'add_ons' => ['nullable'],
            'is_available' => ['required'],
        ]);

        if (isset($data['add_ons']) && is_string($data['add_ons'])) {
            $data['add_ons'] = json_decode($data['add_ons'], true);
        }

        $data['is_available'] = filter_var($request->input('is_available') ?? true, FILTER_VALIDATE_BOOLEAN);

        $imageError = $this->storeMenuImage($request, $data);
        if ($imageError) {
            return response()->json(['message' => $imageError], 422);
        }

        unset($data['image_file']);

        $menu = Menu::create($data);

        return response()->json($menu);
    }

    public function update(Request $request, $id)
    {
        if (! $this->userHasRole(['manager', 'admin', 'superadmin'])) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:menus,barcode,'.$id],
            'add_ons' => ['nullable'],
            'is_available' => ['nullable'],
        ]);

        if (isset($data['add_ons']) && is_string($data['add_ons'])) {
            $data['add_ons'] = json_decode($data['add_ons'], true);
        }

        $data['is_available'] = filter_var($request->input('is_available') ?? true, FILTER_VALIDATE_BOOLEAN);

        $imageError = $this->storeMenuImage($request, $data, $menu);
        if ($imageError) {
            return response()->json(['message' => $imageError], 422);
        }

        unset($data['image_file']);

        $menu->update($data);

        return response()->json($menu);
    }

    private function storeMenuImage(Request $request, array &$data, ?Menu $menu = null): ?string
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

    private function userHasRole(array $roles): bool
    {
        return \Illuminate\Support\Facades\Auth::check() && in_array(\Illuminate\Support\Facades\Auth::user()->role, $roles, true);
    }
}
