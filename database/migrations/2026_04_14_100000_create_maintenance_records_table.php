<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('car_id');
            $table->unsignedBigInteger('assigned_staff_id')->nullable();
            $table->text('description');
            $table->decimal('repair_cost', 12, 2)->default(0);
            $table->string('workshop_name', 100);
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->text('staff_notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('car_id')->references('car_id')->on('cars')->onDelete('cascade');
            $table->foreign('assigned_staff_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
