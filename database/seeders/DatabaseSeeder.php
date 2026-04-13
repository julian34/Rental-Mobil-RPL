<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\CarSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CarSeeder::class,
        ]);
    }
}
