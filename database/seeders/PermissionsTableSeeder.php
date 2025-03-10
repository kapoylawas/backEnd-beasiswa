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
        Permission::create(['name' => 'dashboard.index', 'guard_name' => 'api']);

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

        //permission for kesra
        Permission::create(['name' => 'kesra.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'kesra.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'kesra.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'kesra.delete', 'guard_name' => 'api']);

        //permission for dinsos
        Permission::create(['name' => 'dinsos.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'dinsos.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'dinsos.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'dinsos.delete', 'guard_name' => 'api']);
        
        //permission for luar negeri
        Permission::create(['name' => 'luarnegeri.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'luarnegeri.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'luarnegeri.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'luarnegeri.delete', 'guard_name' => 'api']);

        //permission for admin dispora
        Permission::create(['name' => 'dispora.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'dispora.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'dispora.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'dispora.delete', 'guard_name' => 'api']);

        //permission for admin kesra
        Permission::create(['name' => 'adminkesra.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'adminkesra.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'adminkesra.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'adminkesra.delete', 'guard_name' => 'api']);

        //permission for admin dinsos
        Permission::create(['name' => 'admindinsos.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'admindinsos.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'admindinsos.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'admindinsos.delete', 'guard_name' => 'api']);

        //permission for admin dispenduk
        Permission::create(['name' => 'dispenduk.index', 'guard_name' => 'api']);
        Permission::create(['name' => 'dispenduk.create', 'guard_name' => 'api']);
        Permission::create(['name' => 'dispenduk.edit', 'guard_name' => 'api']);
        Permission::create(['name' => 'dispenduk.delete', 'guard_name' => 'api']);
    }
}
