<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $primaryKey = 'car_id';

    protected $fillable = [
        'car_code',
        'license_plate',
        'brand',
        'model',
        'year',
        'color',
        'rental_price_per_day',
        'car_status',
        'car_condition',
    ];

    protected $casts = [
        'rental_price_per_day' => 'decimal:2',
        'year' => 'integer',
    ];
}
