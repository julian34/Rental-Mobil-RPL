<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id('car_id');
            $table->string('car_code', 20)->unique();
            $table->string('license_plate', 20)->unique();
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->year('year')->nullable();
            $table->string('color', 30)->nullable();
            $table->decimal('rental_price_per_day', 12, 2);
            $table->enum('car_status', ['Available', 'Rented', 'Maintenance'])->default('Available');
            $table->text('car_condition')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
