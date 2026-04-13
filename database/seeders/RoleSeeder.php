<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'description' => 'Full system access with all permissions'
            ],
            [
                'name' => 'Owner',
                'description' => 'Business owner with business management access'
            ],
            [
                'name' => 'Cashier',
                'description' => 'Handle payment and transaction processing'
            ],
            [
                'name' => 'Staff',
                'description' => 'General staff with limited access'
            ],
            [
                'name' => 'Customer',
                'description' => 'Regular customer with booking access'
            ]
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
};
