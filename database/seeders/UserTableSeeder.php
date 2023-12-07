<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create data user
        User::create([
            'nik'      => '3515082407920001',
            'name'      => 'Administrator',
            'nohp'      => '085633517033',
            'email'     => 'admin@gmail.com',
            'gender'     => 'L',
            'kecamatan'     => 1,
            'codepos'     => '61224',
            'rt'     => '10',
            'rw'     => '5',
            'alamat'     => 'testing sidoarjo',
            'password'  => bcrypt('password')
        ]);

        //assign permission to role
        $role = Role::find(1);
        $permissions = Permission::all();

        $role->syncPermissions($permissions);

        //assign role with permission to user
        $user = User::find(1);
        $user->assignRole($role->name);
    }
}
