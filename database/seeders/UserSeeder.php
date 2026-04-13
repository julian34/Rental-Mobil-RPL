<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all();

        // Create Administrator
        $admin = User::firstOrCreate(
            ['email' => 'admin@rental-mobil.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password123')
            ]
        );
        $admin->roles()->sync($roles->where('name', 'Administrator')->first()->id);

        // Create Owner
        $owner = User::firstOrCreate(
            ['email' => 'owner@rental-mobil.test'],
            [
                'name' => 'Business Owner',
                'password' => Hash::make('password123')
            ]
        );
        $owner->roles()->sync($roles->where('name', 'Owner')->first()->id);

        // Create Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@rental-mobil.test'],
            [
                'name' => 'Cashier Staff',
                'password' => Hash::make('password123')
            ]
        );
        $cashier->roles()->sync($roles->where('name', 'Cashier')->first()->id);

        // Create Staff
        $staff = User::firstOrCreate(
            ['email' => 'staff@rental-mobil.test'],
            [
                'name' => 'General Staff',
                'password' => Hash::make('password123')
            ]
        );
        $staff->roles()->sync($roles->where('name', 'Staff')->first()->id);

        // Create Customer
        $customer = User::firstOrCreate(
            ['email' => 'customer@rental-mobil.test'],
            [
                'name' => 'John Doe Customer',
                'password' => Hash::make('password123')
            ]
        );
        $customer->roles()->sync($roles->where('name', 'Customer')->first()->id);
    }
}
