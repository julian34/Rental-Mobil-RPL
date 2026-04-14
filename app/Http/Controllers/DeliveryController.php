<?php

namespace App\Http\Controllers;

use App\Models\DeliveryTask;
use App\Models\User;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * List all delivery tasks (Admin view).
     */
    public function index(Request $request)
    {
        $query = DeliveryTask::with(['car', 'assignedStaff', 'maintenanceRecord', 'transaction.user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tasks = $query->orderByDesc('created_at')->get();

        return response()->json(['tasks' => $tasks], 200);
    }

    /**
     * Admin creates a delivery task (workshop or customer delivery).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type'                  => 'required|string|in:workshop_delivery,customer_delivery',
            'car_id'                => 'required|exists:cars,car_id',
            'maintenance_record_id' => 'nullable|exists:maintenance_records,id',
            'transaction_id'        => 'nullable|exists:rental_transactions,transaction_id',
            'assigned_staff_id'     => 'nullable|exists:users,id',
            'destination'           => 'required|string|max:500',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $task = DeliveryTask::create(array_merge($validated, ['status' => 'pending']));
        $task->load(['car', 'assignedStaff', 'maintenanceRecord', 'transaction.user']);

        return response()->json([
            'message' => 'Tugas pengantaran berhasil dibuat.',
            'task'    => $task,
        ], 201);
    }

    /**
     * Staff views their assigned delivery tasks.
     */
    public function myDeliveries(Request $request)
    {
        $tasks = DeliveryTask::with(['car', 'maintenanceRecord', 'transaction.user'])
            ->where('assigned_staff_id', $request->user()->id)
            ->orderByRaw("CASE status WHEN 'on_the_way' THEN 1 WHEN 'pending' THEN 2 WHEN 'delivered' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['tasks' => $tasks], 200);
    }

    /**
     * Staff updates delivery status and reports.
     */
    public function updateStatus(Request $request, DeliveryTask $deliveryTask)
    {
        $validated = $request->validate([
            'status'       => 'required|string|in:on_the_way,delivered',
            'staff_report' => 'nullable|string|max:2000',
        ]);

        $data = ['status' => $validated['status']];

        if (isset($validated['staff_report'])) {
            $data['staff_report'] = $validated['staff_report'];
        }

        if ($validated['status'] === 'on_the_way' && !$deliveryTask->picked_up_at) {
            $data['picked_up_at'] = now();
        }

        if ($validated['status'] === 'delivered') {
            $data['delivered_at'] = now();
        }

        $deliveryTask->update($data);
        $deliveryTask->load(['car', 'assignedStaff', 'maintenanceRecord', 'transaction.user']);

        $msg = $validated['status'] === 'delivered'
            ? 'Pengantaran selesai.'
            : 'Status pengantaran diperbarui.';

        return response()->json([
            'message' => $msg,
            'task'    => $deliveryTask,
        ], 200);
    }
}
