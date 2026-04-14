<?php

namespace App\Http\Controllers;

use App\Models\RentalTransaction;
use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CashierController extends Controller
{
    /**
     * List ALL transactions with customer + car info.
     */
    public function index(Request $request)
    {
        $query = RentalTransaction::with(['user', 'car', 'cashier'])
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                     ->orWhere('username', 'like', "%{$search}%"));
            });
        }

        return response()->json(['transactions' => $query->get()], 200);
    }

    /**
     * Record payment (optional, can be done at any point before completion).
     * Works on pending OR active transactions that haven't been paid yet.
     * Does NOT change the transaction status — that is driven by handover/return.
     */
    public function processPayment(Request $request, RentalTransaction $transaction)
    {
        if (!in_array($transaction->status, ['pending', 'active'])) {
            return response()->json([
                'message' => 'Pembayaran hanya bisa dicatat pada transaksi yang masih berjalan.',
            ], 422);
        }

        if ($transaction->paid_at !== null) {
            return response()->json([
                'message' => 'Transaksi ini sudah tercatat sebagai dibayar.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|string|in:cash,transfer',
        ]);

        $transaction->update([
            'payment_method' => $validated['payment_method'],
            'paid_at'        => now(),
            'cashier_id'     => $request->user()->id,
        ]);

        return response()->json([
            'message'     => 'Pembayaran berhasil dicatat.',
            'transaction' => $transaction->fresh(['user', 'car', 'cashier']),
        ], 200);
    }

    /**
     * Confirm handover — car physically given to customer (pickup or delivery).
     * Works on PENDING transactions (payment is NOT required first).
     * Sets status → active, records handed_over_at, marks car → Rented.
     */
    public function confirmHandover(Request $request, RentalTransaction $transaction)
    {
        if ($transaction->status !== 'pending') {
            return response()->json([
                'message' => 'Serah terima hanya bisa dilakukan pada transaksi yang masih pending.',
            ], 422);
        }

        if ($transaction->handed_over_at !== null) {
            return response()->json([
                'message' => 'Kendaraan sudah pernah diserahkan sebelumnya.',
            ], 422);
        }

        // For delivery orders, validate staff assignment
        if ($transaction->delivery_method === 'delivery') {
            $request->validate([
                'assigned_staff_id' => 'required|exists:users,id',
            ]);
        }

        $transaction->update([
            'handed_over_at' => now(),
            'status'         => 'active',
            'cashier_id'     => $request->user()->id,
        ]);

        // Car is now officially out of the garage
        $transaction->car->update(['car_status' => 'Rented']);

        // Auto-create delivery task for delivery orders
        $deliveryTask = null;
        if ($transaction->delivery_method === 'delivery') {
            $deliveryTask = DeliveryTask::create([
                'type'              => 'customer_delivery',
                'car_id'            => $transaction->car_id,
                'transaction_id'    => $transaction->transaction_id,
                'assigned_staff_id' => $request->assigned_staff_id,
                'destination'       => $transaction->delivery_address ?? 'Alamat pelanggan',
                'notes'             => 'Antar mobil ke pelanggan: ' . ($transaction->user->name ?? ''),
                'status'            => 'pending',
            ]);
        }

        $label = $transaction->delivery_method === 'delivery'
            ? 'Pengiriman kendaraan dikonfirmasi & staff ditugaskan. Sewa berjalan.'
            : 'Pengambilan kendaraan berhasil dikonfirmasi. Sewa berjalan.';

        return response()->json([
            'message'       => $label,
            'transaction'   => $transaction->fresh(['user', 'car', 'cashier']),
            'delivery_task' => $deliveryTask,
        ], 200);
    }

    /**
     * Confirm car return.
     * - Handover must have been confirmed first.
     * - If the transaction has NOT been paid yet, payment_method is REQUIRED here
     *   (payment is collected at return time).
     * - Calculates late days & charge, sets grand_total, car → Available.
     */
    public function confirmReturn(Request $request, RentalTransaction $transaction)
    {
        if ($transaction->status !== 'active') {
            return response()->json([
                'message' => 'Hanya transaksi "active" yang dapat dikonfirmasi pengembaliannya.',
            ], 422);
        }

        if ($transaction->handed_over_at === null) {
            return response()->json([
                'message' => 'Kendaraan belum diserahkan kepada pelanggan. Konfirmasi serah terima terlebih dahulu.',
            ], 422);
        }

        // If not yet paid, payment method is required at return
        $rules = ['actual_return_date' => 'required|date'];
        if ($transaction->paid_at === null) {
            $rules['payment_method'] = 'required|string|in:cash,transfer';
        }

        $validated = $request->validate($rules);

        $actualReturn = Carbon::parse($validated['actual_return_date']);
        $expectedEnd  = Carbon::parse($transaction->end_date);

        $lateDays   = max(0, $expectedEnd->diffInDays($actualReturn, false));
        $lateCharge = $lateDays * $transaction->late_penalty_per_day;
        $grandTotal  = $transaction->total_price + $lateCharge;

        $updates = [
            'actual_return_date' => $actualReturn->toDateString(),
            'late_days'          => $lateDays,
            'late_charge'        => $lateCharge,
            'grand_total'        => $grandTotal,
            'status'             => 'completed',
            'cashier_id'         => $request->user()->id,
        ];

        // Collect payment at return if not yet paid
        if ($transaction->paid_at === null) {
            $updates['payment_method'] = $validated['payment_method'];
            $updates['paid_at']        = now();
        }

        $transaction->update($updates);
        $transaction->car->update(['car_status' => 'Available']);

        return response()->json([
            'message'     => 'Pengembalian mobil berhasil dikonfirmasi. Pembayaran selesai.',
            'transaction' => $transaction->fresh(['user', 'car', 'cashier']),
        ], 200);
    }

    /**
     * Cancel — only resets car to Available if handover had already happened.
     */
    public function cancel(Request $request, RentalTransaction $transaction)
    {
        if (!in_array($transaction->status, ['pending', 'active'])) {
            return response()->json([
                'message' => 'Transaksi ini tidak dapat dibatalkan.',
            ], 422);
        }

        $transaction->update([
            'status'     => 'cancelled',
            'cashier_id' => $request->user()->id,
        ]);

        if ($transaction->handed_over_at !== null) {
            $transaction->car->update(['car_status' => 'Available']);
        }

        return response()->json([
            'message'     => 'Transaksi berhasil dibatalkan.',
            'transaction' => $transaction->fresh(['user', 'car']),
        ], 200);
    }

    /**
     * Get staff list for assigning delivery tasks.
     */
    public function staffList()
    {
        $staff = User::whereHas('roles', fn ($q) => $q->where('name', 'Staff'))->get(['id', 'name', 'username']);
        return response()->json(['staff' => $staff], 200);
    }

    /**
     * Get delivery tasks linked to transactions (Cashier view).
     */
    public function deliveryTasks(Request $request)
    {
        $query = DeliveryTask::with(['car', 'assignedStaff', 'transaction.user'])
            ->where('type', 'customer_delivery')
            ->orderByDesc('created_at');

        $tasks = $query->get();
        return response()->json(['tasks' => $tasks], 200);
    }

    /**
     * Summary stats for the cashier dashboard.
     */
    public function stats()
    {
        $today = now()->toDateString();

        return response()->json([
            // pending = booking confirmed by customer, awaiting car handover
            'pending'         => RentalTransaction::where('status', 'pending')->count(),
            // active  = car already handed over, currently being rented
            'active'          => RentalTransaction::where('status', 'active')->count(),
            // unpaid  = active rentals that haven't been paid yet (pay at return)
            'unpaid_active'   => RentalTransaction::where('status', 'active')
                                    ->whereNull('paid_at')->count(),
            'completed_today' => RentalTransaction::where('status', 'completed')
                                    ->whereDate('updated_at', $today)->count(),
            'revenue_today'   => RentalTransaction::where('status', 'completed')
                                    ->whereDate('updated_at', $today)
                                    ->sum('grand_total'),
        ], 200);
    }
}
