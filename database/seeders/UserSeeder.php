<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Contracts\Role as ContractsRole;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $superadmin = User::create([
        //     'username' => 'superedwin',
        //     'karyawan_id' => 1,
        //     'role_id' => 1,
        //     'password' => bcrypt('123')
        // ]);

        // $superadmin->assignRole('Super Admin');

        // $admin = User::create([
        //     'username' => 'supertim',
        //     'karyawan_id' => 2,
        //     'role_id' => 2,
        //     'password' => bcrypt('123')
        // ]);

        // $admin->assignRole('Admin');

        $permission = Permission::create(['name' => 'create JO']);
        $permission = Permission::create(['name' => 'read JO']);
        $permission = Permission::create(['name' => 'update JO']);
        $permission = Permission::create(['name' => 'delete JO']);

        $role_super_user = Role::where('name', 'Super Admin')->first();

        $role_super_user->givePermissionTo('create JO', 'web');
        $role_super_user->givePermissionTo('read JO', 'web');
        $role_super_user->givePermissionTo('update JO', 'web');
        $role_super_user->givePermissionTo('delete JO', 'web');
    }
}
