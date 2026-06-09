<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Tests\TestCase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($this->user);
        Storage::fake('public');
    }

    public function test_create_menu_with_barcode_image_file(): void
    {
        $file = UploadedFile::fake()->image('barcode.jpg');

        $response = $this->postJson('/admin/menus', [
            'name' => 'Test Menu with Barcode',
            'category' => 'Snack & Minuman',
            'price' => 10000,
            'barcode' => '123456789',
            'barcode_image_file' => $file,
            'is_available' => true,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('menus', ['barcode' => '123456789']);
        $menu = \App\Models\Menu::where('barcode', '123456789')->first();
        $this->assertNotNull($menu->barcode_image);
    }
}
