<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->string('invoice_number', 30)->unique();

            // Relations
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')->references('car_id')->on('cars')->cascadeOnDelete();

            // Rental period
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('duration_days');

            // Pricing (snapshot at booking time)
            $table->decimal('rental_price_per_day', 12, 2);
            $table->decimal('total_price', 14, 2);

            // Late return penalty rate (per day, snapshot at booking time)
            $table->decimal('late_penalty_per_day', 12, 2);

            // Delivery
            $table->enum('delivery_method', ['delivery', 'pickup'])->default('pickup');
            $table->string('delivery_address', 500)->nullable();

            // Status lifecycle
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_transactions');
    }
};
