<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Permission sesuai modulmu
        $perms = [
            'complaint.create',   // user membuat pengaduan
            'complaint.view_own', // user melihat pengaduannya sendiri
            'complaint.manage',   // admin / petugas me-review & update
            'news.manage',        // kelola berita/kegiatan
            'user.manage',        // opsional: manajemen user
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // Roles sesuai proyekmu
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
        $user       = Role::firstOrCreate(['name' => 'user',        'guard_name' => 'web']);

        // Mapping permission → role
        $superAdmin->syncPermissions(Permission::all());
        $admin->syncPermissions(['complaint.manage','news.manage']);
        $user->syncPermissions(['complaint.create','complaint.view_own']);
    }
}
