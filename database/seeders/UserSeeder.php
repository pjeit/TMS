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

        // $permission = Permission::create(['name' => 'create JO']);
        // $permission = Permission::create(['name' => 'read JO']);
        // $permission = Permission::create(['name' => 'update JO']);
        // $permission = Permission::create(['name' => 'delete JO']);

        $role_super_admin = Role::where('name', 'SUPER ADMIN')->first();
        $role_admin = Role::where('name', 'ADMIN')->first();
        // $role_admin->givePermissionTo(['create grup', 'read grup', 'edit grup', 'delete grup']);
        // $role_admin->givePermissionTo(['create marketing', 'read marketing', 'edit marketing', 'delete marketing']);
        // $role_admin->givePermissionTo(['create customer', 'read customer', 'edit customer', 'delete customer']);
        // $role_admin->givePermissionTo(['create grup tujuan', 'read grup tujuan', 'edit grup tujuan', 'delete grup tujuan']);

        // $role_super_user->givePermissionTo('create JO', 'web');
        // $role_super_user->givePermissionTo('read JO', 'web');
        // $role_super_user->givePermissionTo('update JO', 'web');
        // $role_super_user->givePermissionTo('delete JO', 'web');

        // $permission = Permission::create(['name' => 'create grup']);
        // $permission = Permission::create(['name' => 'read grup']);
        // $permission = Permission::create(['name' => 'edit grup']);
        // $permission = Permission::create(['name' => 'delete grup']);
        // $permission = Permission::create(['name' => 'create marketing']);
        // $permission = Permission::create(['name' => 'read marketing']);
        // $permission = Permission::create(['name' => 'edit marketing']);
        // $permission = Permission::create(['name' => 'delete marketing']);
        // $permission = Permission::create(['name' => 'create customer']);
        // $permission = Permission::create(['name' => 'read customer']);
        // $permission = Permission::create(['name' => 'edit customer']);
        // $permission = Permission::create(['name' => 'delete customer']);
        // $permission = Permission::create(['name' => 'create grup tujuan']);
        // $permission = Permission::create(['name' => 'read grup tujuan']);
        // $permission = Permission::create(['name' => 'edit grup tujuan']);
        // $permission = Permission::create(['name' => 'delete grup tujuan']);
        // $permission = Permission::create(['name' => 'create head']);
        // $permission = Permission::create(['name' => 'read head']);
        // $permission = Permission::create(['name' => 'edit head']);
        // $permission = Permission::create(['name' => 'delete head']);
        // $permission = Permission::create(['name' => 'create chassis']);
        // $permission = Permission::create(['name' => 'read chassis']);
        // $permission = Permission::create(['name' => 'edit chassis']);
        // $permission = Permission::create(['name' => 'delete chassis']);
        // $permission = Permission::create(['name' => 'create pair kendaraan']);
        // $permission = Permission::create(['name' => 'read pair kendaraan']);
        // $permission = Permission::create(['name' => 'edit pair kendaraan']);
        // $permission = Permission::create(['name' => 'delete pair kendaraan']);
        // $permission = Permission::create(['name' => 'create mutasi kendaraan']);
        // $permission = Permission::create(['name' => 'read mutasi kendaraan']);
        // $permission = Permission::create(['name' => 'edit mutasi kendaraan']);
        // $permission = Permission::create(['name' => 'delete mutasi kendaraan']);
        // $permission = Permission::create(['name' => 'create supplier']);
        // $permission = Permission::create(['name' => 'read supplier']);
        // $permission = Permission::create(['name' => 'edit supplier']);
        // $permission = Permission::create(['name' => 'delete supplier']);
        // $permission = Permission::create(['name' => 'create karyawan']);
        // $permission = Permission::create(['name' => 'read karyawan']);
        // $permission = Permission::create(['name' => 'edit karyawan']);
        // $permission = Permission::create(['name' => 'delete karyawan']);
        // $permission = Permission::create(['name' => 'create coa']);
        // $permission = Permission::create(['name' => 'read coa']);
        // $permission = Permission::create(['name' => 'edit coa']);
        // $permission = Permission::create(['name' => 'delete coa']);
        // $permission = Permission::create(['name' => 'create kasbank']);
        // $permission = Permission::create(['name' => 'read kasbank']);
        // $permission = Permission::create(['name' => 'edit kasbank']);
        // $permission = Permission::create(['name' => 'delete kasbank']);
        // $permission = Permission::create(['name' => 'create role']);
        // $permission = Permission::create(['name' => 'read role']);
        // $permission = Permission::create(['name' => 'edit role']);
        // $permission = Permission::create(['name' => 'delete role']);
        // $permission = Permission::create(['name' => 'create user']);
        // $permission = Permission::create(['name' => 'read user']);
        // $permission = Permission::create(['name' => 'edit user']);
        // $permission = Permission::create(['name' => 'delete user']);
        // $permission = Permission::create(['name' => 'read pengaturan keuangan']);
        // $permission = Permission::create(['name' => 'edit pengaturan keuangan']);
        // $permission = Permission::create(['name' => 'create jo']);
        // $permission = Permission::create(['name' => 'read jo']);
        // $permission = Permission::create(['name' => 'edit jo']);
        // $permission = Permission::create(['name' => 'delete jo']);
        // $permission = Permission::create(['name' => 'create sdt']);
        // $permission = Permission::create(['name' => 'read sdt']);
        // $permission = Permission::create(['name' => 'edit sdt']);
        // $permission = Permission::create(['name' => 'delete sdt']);
        // $permission = Permission::create(['name' => 'create pengembalian jaminan']);
        // $permission = Permission::create(['name' => 'read pengembalian jaminan']);
        // $permission = Permission::create(['name' => 'edit pengembalian jaminan']);
        // $permission = Permission::create(['name' => 'delete pengembalian jaminan']);
        // $permission = Permission::create(['name' => 'create karantina']);
        // $permission = Permission::create(['name' => 'read karantina']);
        // $permission = Permission::create(['name' => 'edit karantina']);
        // $permission = Permission::create(['name' => 'delete karantina']);
        // $permission = Permission::create(['name' => 'create booking']);
        // $permission = Permission::create(['name' => 'read booking']);
        // $permission = Permission::create(['name' => 'edit booking']);
        // $permission = Permission::create(['name' => 'delete booking']);
        // $permission = Permission::create(['name' => 'create order']);
        // $permission = Permission::create(['name' => 'read order']);
        // $permission = Permission::create(['name' => 'edit order']);
        // $permission = Permission::create(['name' => 'delete order']);
        // $permission = Permission::create(['name' => 'create status kendaraan']);
        // $permission = Permission::create(['name' => 'read status kendaraan']);
        // $permission = Permission::create(['name' => 'edit status kendaraan']);
        // $permission = Permission::create(['name' => 'delete status kendaraan']);
        // $permission = Permission::create(['name' => 'create dalam perjalanan']);
        // $permission = Permission::create(['name' => 'read dalam perjalanan']);
        // $permission = Permission::create(['name' => 'edit dalam perjalanan']);
        // $permission = Permission::create(['name' => 'delete dalam perjalanan']);
        // $permission = Permission::create(['name' => 'create biaya operasional']);
        // $permission = Permission::create(['name' => 'read biaya operasional']);
        // $permission = Permission::create(['name' => 'edit biaya operasional']);
        // $permission = Permission::create(['name' => 'delete biaya operasional']);
        // $permission = Permission::create(['name' => 'create klaim supir']);
        // $permission = Permission::create(['name' => 'read klaim supir']);
        // $permission = Permission::create(['name' => 'edit klaim supir']);
        // $permission = Permission::create(['name' => 'delete klaim supir']);
        // $permission = Permission::create(['name' => 'create pembayaran jo']);
        // $permission = Permission::create(['name' => 'read pembayaran jo']);
        // $permission = Permission::create(['name' => 'edit pembayaran jo']);
        // $permission = Permission::create(['name' => 'delete pembayaran jo']);
        // $permission = Permission::create(['name' => 'create pembayaran sdt']);
        // $permission = Permission::create(['name' => 'read pembayaran sdt']);
        // $permission = Permission::create(['name' => 'edit pembayaran sdt']);
        // $permission = Permission::create(['name' => 'delete pembayaran sdt']);
        // $permission = Permission::create(['name' => 'create pembayaran gaji']);
        // $permission = Permission::create(['name' => 'read pembayaran gaji']);
        // $permission = Permission::create(['name' => 'edit pembayaran gaji']);
        // $permission = Permission::create(['name' => 'delete pembayaran gaji']);
        // $permission = Permission::create(['name' => 'create pencairan uj ftl']);
        // $permission = Permission::create(['name' => 'read pencairan uj ftl']);
        // $permission = Permission::create(['name' => 'edit pencairan uj ftl']);
        // $permission = Permission::create(['name' => 'delete pencairan uj ftl']);
        // $permission = Permission::create(['name' => 'create pencairan uj ltl']);
        // $permission = Permission::create(['name' => 'read pencairan uj ltl']);
        // $permission = Permission::create(['name' => 'edit pencairan uj ltl']);
        // $permission = Permission::create(['name' => 'delete pencairan uj ltl']);
        // $permission = Permission::create(['name' => 'create cetak uj']);
        // $permission = Permission::create(['name' => 'read cetak uj']);
        // $permission = Permission::create(['name' => 'edit cetak uj']);
        // $permission = Permission::create(['name' => 'delete cetak uj']);
        // $permission = Permission::create(['name' => 'create pencairan komisi customer']);
        // $permission = Permission::create(['name' => 'read pencairan komisi customer']);
        // $permission = Permission::create(['name' => 'edit pencairan komisi customer']);
        // $permission = Permission::create(['name' => 'delete pencairan komisi customer']);

    }
}
