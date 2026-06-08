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

    public function test_create_menu_with_image_file(): void
    {
        $file = UploadedFile::fake()->create('menu.txt', 100);

        $response = $this->postJson('/admin/menus', [
            'name' => 'Test Menu',
            'category' => 'Food',
            'price' => 10000,
            'image_file' => $file,
            'is_available' => true,
        ]);

        $response->assertStatus(200);
    }
}
