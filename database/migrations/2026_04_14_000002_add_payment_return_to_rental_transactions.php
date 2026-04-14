<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rental_transactions', function (Blueprint $table) {
            // Payment
            $table->enum('payment_method', ['cash', 'transfer'])->nullable()->after('status');
            $table->timestamp('paid_at')->nullable()->after('payment_method');

            // Car return
            $table->date('actual_return_date')->nullable()->after('paid_at');
            $table->unsignedSmallInteger('late_days')->default(0)->after('actual_return_date');
            $table->decimal('late_charge', 12, 2)->default(0)->after('late_days');

            // Grand total = total_price + late_charge (set on return)
            $table->decimal('grand_total', 14, 2)->nullable()->after('late_charge');

            // Cashier who processed the transaction
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete()->after('grand_total');
        });
    }

    public function down(): void
    {
        Schema::table('rental_transactions', function (Blueprint $table) {
            $table->dropForeign(['cashier_id']);
            $table->dropColumn([
                'payment_method', 'paid_at',
                'actual_return_date', 'late_days', 'late_charge',
                'grand_total', 'cashier_id',
            ]);
        });
    }
};
