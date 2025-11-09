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
        $this->call(RolesAndPermissionsSeeder::class);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

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
