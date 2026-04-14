<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryTask extends Model
{
    protected $fillable = [
        'type',
        'car_id',
        'maintenance_record_id',
        'transaction_id',
        'assigned_staff_id',
        'destination',
        'notes',
        'status',
        'staff_report',
        'picked_up_at',
        'delivered_at',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'delivered_at'  => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id', 'car_id');
    }

    public function maintenanceRecord(): BelongsTo
    {
        return $this->belongsTo(MaintenanceRecord::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(RentalTransaction::class, 'transaction_id', 'transaction_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }
}
