<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\RentalTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RentalTransactionController extends Controller
{
    /**
     * List all transactions belonging to the authenticated customer.
     */
    public function index(Request $request)
    {
        $transactions = RentalTransaction::with('car')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['transactions' => $transactions], 200);
    }

    /**
     * Create a new rental transaction.
     * - Validates dates and car availability.
     * - Snapshots pricing at booking time.
     * - Marks car as Rented.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id'           => 'required|integer|exists:cars,car_id',
            'start_date'       => 'required|date|after_or_equal:today',
            'end_date'         => 'required|date|after:start_date',
            'delivery_method'  => 'required|string|in:delivery,pickup',
            'delivery_address' => 'required_if:delivery_method,delivery|nullable|string|max:500',
        ]);

        $car = Car::where('car_id', $validated['car_id'])->firstOrFail();

        if ($car->car_status !== 'Available') {
            return response()->json([
                'message' => 'Mobil tidak tersedia untuk disewa saat ini.',
            ], 422);
        }

        $start        = Carbon::parse($validated['start_date']);
        $end          = Carbon::parse($validated['end_date']);
        $durationDays = $start->diffInDays($end);

        // Late-return penalty: 30% of daily rate per day
        $latePenaltyPerDay = round($car->rental_price_per_day * 0.30, 2);
        $totalPrice        = $car->rental_price_per_day * $durationDays;

        $transaction = RentalTransaction::create([
            'invoice_number'       => RentalTransaction::generateInvoiceNumber(),
            'user_id'              => $request->user()->id,
            'car_id'               => $car->car_id,
            'start_date'           => $validated['start_date'],
            'end_date'             => $validated['end_date'],
            'duration_days'        => $durationDays,
            'rental_price_per_day' => $car->rental_price_per_day,
            'total_price'          => $totalPrice,
            'late_penalty_per_day' => $latePenaltyPerDay,
            'delivery_method'      => $validated['delivery_method'],
            'delivery_address'     => $validated['delivery_address'] ?? null,
            'status'               => 'pending',
        ]);

        // Car status stays Available until the cashier confirms the physical handover.

        return response()->json([
            'message'     => 'Transaksi berhasil dibuat.',
            'transaction' => $transaction->load('car'),
        ], 201);
    }

    /**
     * Show a single transaction (must belong to the authenticated user).
     */
    public function show(Request $request, RentalTransaction $rentalTransaction)
    {
        if ($rentalTransaction->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return response()->json(['transaction' => $rentalTransaction->load('car')], 200);
    }
}
