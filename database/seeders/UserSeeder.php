<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

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
        //     'password' => bcrypt('123123123')
        // ]);

        // $superadmin->assignRole('SUPER ADMIN');
        
        $admin = User::create([
            'username' => 'antok',
            'karyawan_id' => 29,
            'password' => bcrypt('123123123')
        ]);

        $admin->assignRole('ADMIN');
    }
}
