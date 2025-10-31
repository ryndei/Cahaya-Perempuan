<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\PermissionRegistrar; // <-- penting

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Buat permissions + roles + mapping (HARUS duluan)
        $this->call(RolesAndPermissionsSeeder::class);

        // 2) Reset cache Spatie supaya findByName() melihat role baru
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 3) Seed user & assign role (sekarang role SUDAH ada)
        $super = User::firstOrCreate(
            ['email' => 'superadmin@gmail.com'],
            ['name' => 'Super Admin','password' => Hash::make('edoanakrahim628983'),'email_verified_at' => now()]
        );
        $super->syncRoles(['super-admin']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Admin','password' => Hash::make('edoanakrahim628983'),'email_verified_at' => now()]
        );
        $admin->syncRoles(['admin']);

        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'User Biasa','password' => Hash::make('secret123'),'email_verified_at' => now()]
        );
        $user->syncRoles(['user']);
    }
}
