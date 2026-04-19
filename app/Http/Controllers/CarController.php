<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all(); 
        return response()->json(['cars' => $cars], 200);
    }

    public function show(Car $car)
    {
        return response()->json(['car' => $car], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_code'             => 'required|string|max:20|unique:cars,car_code',
            'license_plate'        => 'required|string|max:20|unique:cars,license_plate',
            'brand'                => 'required|string|max:50',
            'model'                => 'required|string|max:50',
            'year'                 => 'nullable|integer|min:1900|max:2099',
            'color'                => 'nullable|string|max:30',
            'rental_price_per_day' => 'required|numeric|min:0',
            'car_status'           => 'required|string|in:Available,Rented,Maintenance',
            'car_condition'        => 'nullable|string',
        ]);

        $car = Car::create($validated);

        return response()->json([
            'message' => 'Car created successfully',
            'car' => $car,
        ], 201);
    }

    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'car_code'             => 'required|string|max:20|unique:cars,car_code,' . $car->car_id . ',car_id',
            'license_plate'        => 'required|string|max:20|unique:cars,license_plate,' . $car->car_id . ',car_id',
            'brand'                => 'required|string|max:50',
            'model'                => 'required|string|max:50',
            'year'                 => 'nullable|integer|min:1900|max:2099',
            'color'                => 'nullable|string|max:30',
            'rental_price_per_day' => 'required|numeric|min:0',
            'car_status'           => 'required|string|in:Available,Rented,Maintenance',
            'car_condition'        => 'nullable|string',
        ]);

        $car->update($validated);

        return response()->json([
            'message' => 'Car updated successfully',
            'car' => $car,
        ], 200);
    }

    public function destroy(Car $car)
    {
        $car->delete();

        return response()->json([
            'message' => 'Car deleted successfully',
        ], 200);
    }
}
