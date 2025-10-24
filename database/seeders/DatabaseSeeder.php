<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1) Pastikan role dibuat lebih dulu
        $this->call(RoleSeeder::class);

        // 2) Buat akun SUPER ADMIN
        $super = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('edoanakrahim628983'), // ganti sesuai kebutuhan
                'email_verified_at' => now(),
            ]
        );
        $super->syncRoles('super-admin');

        // 3) Buat akun ADMIN
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('edoanakrahim628983'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles('admin');

        // 4) Buat akun USER
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name'              => 'User Biasa',
                'password'          => Hash::make('secret123'),
                'email_verified_at' => now(),
            ]
        );
        $user->syncRoles('user');

    
    }
}
