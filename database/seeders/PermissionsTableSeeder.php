<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;


class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //permission for roles
        Permission::create(['name' => 'roles.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'roles.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'roles.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'api']);

        //permission for permissions
        Permission::create(['name' => 'permissions.index', 'guard_name' => 'api']);

        //permission for users
        Permission::create(['name' => 'users.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'users.delete', 'guard_name' => 'api']);

        //permission for mahasiswa
        Permission::create(['name' => 'mahasiswa.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'mahasiswa.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'mahasiswa.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'mahasiswa.delete', 'guard_name' => 'api']);

        //permission for akademiks
        Permission::create(['name' => 'akademiks.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'akademiks.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'akademiks.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'akademiks.delete', 'guard_name' => 'api']);

        //permission for non akademiks
        Permission::create(['name' => 'nonakademiks.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'nonakademiks.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'nonakademiks.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'nonakademiks.delete', 'guard_name' => 'api']);
    }
}
