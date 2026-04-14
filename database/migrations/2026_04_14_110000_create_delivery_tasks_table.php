<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_tasks', function (Blueprint $table) {
            $table->id();
            // Type: workshop_delivery = antar mobil ke bengkel, customer_delivery = antar mobil ke pelanggan
            $table->enum('type', ['workshop_delivery', 'customer_delivery']);

            $table->unsignedBigInteger('car_id');
            $table->foreign('car_id')->references('car_id')->on('cars')->cascadeOnDelete();

            // For workshop deliveries, links to maintenance_records
            $table->unsignedBigInteger('maintenance_record_id')->nullable();
            $table->foreign('maintenance_record_id')->references('id')->on('maintenance_records')->nullOnDelete();

            // For customer deliveries, links to rental_transactions
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->foreign('transaction_id')->references('transaction_id')->on('rental_transactions')->nullOnDelete();

            // Staff assigned to deliver
            $table->foreignId('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();

            // Destination info
            $table->string('destination', 500);
            $table->text('notes')->nullable();

            // Status lifecycle
            $table->enum('status', ['pending', 'on_the_way', 'delivered'])->default('pending');
            $table->text('staff_report')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_tasks');
    }
};
