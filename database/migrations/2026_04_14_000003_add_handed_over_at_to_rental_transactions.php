<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_transactions', function (Blueprint $table) {
            // Timestamp set by cashier when the car is physically handed to the customer
            $table->timestamp('handed_over_at')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('rental_transactions', function (Blueprint $table) {
            $table->dropColumn('handed_over_at');
        });
    }
};
