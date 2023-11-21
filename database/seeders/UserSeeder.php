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

        // $permission = Permission::create(['name' => 'create jo']);
        // $permission = Permission::create(['name' => 'read jo']);
        // $permission = Permission::create(['name' => 'edit jo']);
        // $permission = Permission::create(['name' => 'delete jo']);

        $role_super_admin = Role::where('name', 'SUPER ADMIN')->first();
        
        $role_super_admin->givePermissionTo('create jo', 'web');
        $role_super_admin->givePermissionTo('read jo', 'web');
        $role_super_admin->givePermissionTo('edit jo', 'web');
        $role_super_admin->givePermissionTo('delete jo', 'web');

        $role_admin = Role::where('name', 'ADMIN')->first();
        $role_admin->givePermissionTo('create jo', 'web');
        $role_admin->givePermissionTo('read jo', 'web');
        $role_admin->givePermissionTo('edit jo', 'web');

        // $role_admin->givePermissionTo('create sdt', 'web');
        // $role_admin->givePermissionTo('read sdt', 'web');
        // $role_admin->givePermissionTo('edit sdt', 'web');
    }
}
