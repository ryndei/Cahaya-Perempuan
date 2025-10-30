<?php
$permissions = [
    
    'news.manage',
];

foreach ($permissions as $p) {
    \Spatie\Permission\Models\Permission::findOrCreate($p, 'web');
}

// misal role 'admin':
$admin = \Spatie\Permission\Models\Role::firstOrCreate(['name'=>'admin','guard_name'=>'web']);
$admin->givePermissionTo('news.manage');
