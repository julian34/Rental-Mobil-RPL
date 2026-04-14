<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalTransaction extends Model
{
    protected $primaryKey = 'transaction_id';

    protected $fillable = [
        'invoice_number',
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'duration_days',
        'rental_price_per_day',
        'total_price',
        'late_penalty_per_day',
        'delivery_method',
        'delivery_address',
        'status',
        // Payment
        'payment_method',
        'paid_at',
        // Handover
        'handed_over_at',
        // Return
        'actual_return_date',
        'late_days',
        'late_charge',
        'grand_total',
        'cashier_id',
    ];

    protected $casts = [
        'start_date'           => 'date',
        'end_date'             => 'date',
        'actual_return_date'   => 'date',
        'paid_at'              => 'datetime',
        'handed_over_at'       => 'datetime',
        'rental_price_per_day' => 'decimal:2',
        'total_price'          => 'decimal:2',
        'late_penalty_per_day' => 'decimal:2',
        'late_charge'          => 'decimal:2',
        'grand_total'          => 'decimal:2',
        'duration_days'        => 'integer',
        'late_days'            => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'car_id');
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Generate a unique invoice number: INV-YYYYMMDD-XXXXX
     */
    public static function generateInvoiceNumber(): string
    {
        do {
            $number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        } while (self::where('invoice_number', $number)->exists());

        return $number;
    }
}
