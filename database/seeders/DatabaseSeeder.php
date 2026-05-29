<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@coolcafe.test', 'role' => 'superadmin'],
            ['name' => 'Admin CoolCafe', 'email' => 'admin@coolcafe.test', 'role' => 'admin'],
            ['name' => 'Manager CoolCafe', 'email' => 'manager@coolcafe.test', 'role' => 'manager'],
            ['name' => 'Kasir Pagi', 'email' => 'kasir@coolcafe.test', 'role' => 'cashier'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'password' => Hash::make('password'),
                ]
            );
        }
    }
}
