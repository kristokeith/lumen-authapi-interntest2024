<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'superadmin']);
        Role::create(['name' => 'user']);

        Permission::create(['name' => 'permission-index']);
        Permission::create(['name' => 'permission-add']);
        Permission::create(['name' => 'permission-edit']);
        Permission::create(['name' => 'permission-delete']);

        Permission::create(['name' => 'user-index']);
        Permission::create(['name' => 'user-add']);
        Permission::create(['name' => 'user-edit']);
        Permission::create(['name' => 'user-delete']);

        Role::findByName('superadmin')->givePermissionTo([
            'permission-index',
            'permission-add',
            'permission-edit',
            'permission-delete',
            'user-index',
            'user-add',
            'user-edit',
            'user-delete',
        ]);
    }
}
