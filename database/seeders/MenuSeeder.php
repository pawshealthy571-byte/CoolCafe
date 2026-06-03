<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'name' => 'Butter Croissant',
                'category' => 'Bakery',
                'price' => 22000,
                'description' => 'Roti klasik Prancis dengan tekstur renyah.',
                'image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Signature Cafe Latte',
                'category' => 'Beverages',
                'price' => 32000,
                'description' => 'Espresso dengan susu steam halus.',
                'image' => 'https://images.unsplash.com/photo-1536939459926-301728717817?w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Healthy Chicken Bowl',
                'category' => 'Main Course',
                'price' => 45000,
                'description' => 'Nasi coklat, ayam panggang, dan sayuran.',
                'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=200&h=200&fit=crop',
            ],
            [
                'name' => 'Paket Ngopi Hemat',
                'category' => 'Paket',
                'price' => 55000,
                'description' => 'Paket kopi susu dan butter croissant untuk teman santai.',
                'image' => 'https://images.unsplash.com/photo-1511920170033-f8396924c348?w=200&h=200&fit=crop',
                'add_ons' => [
                    ['name' => 'Extra Espresso Shot', 'price' => 7000],
                    ['name' => 'French Fries', 'price' => 12000],
                    ['name' => 'Upgrade Large Drink', 'price' => 9000],
                ],
            ],
        ];

        foreach ($menus as $menu) {
            Menu::updateOrCreate(
                ['name' => $menu['name']],
                $menu
            );
        }
    }
}
