<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['user', 'admin', 'super-admin'] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web', 
            ]);
        }
    }
}
