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
            'nik'      => '1111111111111111',
            'nokk'      => '3515082407220003',
            'name'      => 'Dispenduk',
            'nohp'      => '085633517034',
            'email'     => 'dispenduk@gmail.com',
            'gender'     => 'L',
            'codepos'     => '61224',
            'rt'     => '10',
            'rw'     => '5',
            'alamat'     => 'testing sidoarjo',
            'status_terkirim'     => 'false',
            'status'     => 1,
            'password'  => bcrypt('dispendukpassword')
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
