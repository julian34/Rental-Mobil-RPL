<?php

namespace Database\Seeders;

use App\Models\Car;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    public function run(): void
    {
        $cars = [
            ['car_code' => 'CAR-001', 'license_plate' => 'B 1234 ABC', 'brand' => 'Toyota',     'model' => 'Avanza',   'year' => 2023, 'color' => 'White',  'rental_price_per_day' => 350000, 'car_status' => 'Available',   'car_condition' => 'Good condition'],
            ['car_code' => 'CAR-002', 'license_plate' => 'D 5678 DEF', 'brand' => 'Honda',      'model' => 'Jazz',     'year' => 2022, 'color' => 'Red',    'rental_price_per_day' => 280000, 'car_status' => 'Rented',      'car_condition' => 'Good condition'],
            ['car_code' => 'CAR-003', 'license_plate' => 'B 9012 GHI', 'brand' => 'Mitsubishi', 'model' => 'Pajero',   'year' => 2024, 'color' => 'Black',  'rental_price_per_day' => 850000, 'car_status' => 'Maintenance', 'car_condition' => 'Scheduled maintenance'],
            ['car_code' => 'CAR-004', 'license_plate' => 'F 3456 JKL', 'brand' => 'Toyota',     'model' => 'Innova',   'year' => 2023, 'color' => 'Silver', 'rental_price_per_day' => 550000, 'car_status' => 'Available',   'car_condition' => 'Good condition'],
            ['car_code' => 'CAR-005', 'license_plate' => 'B 7890 MNO', 'brand' => 'Daihatsu',   'model' => 'Xenia',    'year' => 2021, 'color' => 'Blue',   'rental_price_per_day' => 300000, 'car_status' => 'Rented',      'car_condition' => 'Minor scratch on bumper'],
            ['car_code' => 'CAR-006', 'license_plate' => 'D 2345 PQR', 'brand' => 'Honda',      'model' => 'Brio',     'year' => 2023, 'color' => 'Yellow', 'rental_price_per_day' => 220000, 'car_status' => 'Available',   'car_condition' => 'Good condition'],
            ['car_code' => 'CAR-007', 'license_plate' => 'B 6789 STU', 'brand' => 'Toyota',     'model' => 'Fortuner', 'year' => 2024, 'color' => 'White',  'rental_price_per_day' => 950000, 'car_status' => 'Available',   'car_condition' => 'Excellent condition'],
        ];

        foreach ($cars as $car) {
            Car::create($car);
        }
    }
}
