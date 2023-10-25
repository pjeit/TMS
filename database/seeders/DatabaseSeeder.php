<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */

    public function run()
    {
        // \App\Models\User::factory(10)->create();
        // \App\Models\KasBank::factory(5000)->create();
        // factory(YourModel::class, 1000)->create();
        // $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
