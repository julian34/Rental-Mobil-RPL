<?php

namespace App\Http\Controllers;

use App\Models\Car;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * List all maintenance records (Admin).
     */
    public function index(Request $request)
    {
        $query = MaintenanceRecord::with(['car', 'assignedStaff']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->orderByDesc('created_at')->get();

        return response()->json(['records' => $records], 200);
    }

    /**
     * Create a new maintenance record and set car status to Maintenance.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'car_id'            => 'required|exists:cars,car_id',
            'description'       => 'required|string|max:1000',
            'repair_cost'       => 'required|numeric|min:0',
            'workshop_name'     => 'required|string|max:100',
            'assigned_staff_id' => 'nullable|exists:users,id',
        ]);

        $car = Car::findOrFail($validated['car_id']);

        if ($car->car_status === 'Rented') {
            return response()->json(['message' => 'Mobil sedang disewa, tidak bisa masuk maintenance.'], 422);
        }

        // Create the record
        $record = MaintenanceRecord::create([
            'car_id'            => $validated['car_id'],
            'description'       => $validated['description'],
            'repair_cost'       => $validated['repair_cost'],
            'workshop_name'     => $validated['workshop_name'],
            'assigned_staff_id' => $validated['assigned_staff_id'] ?? null,
            'status'            => 'pending',
        ]);

        // Set car status to Maintenance
        $car->update([
            'car_status'    => 'Maintenance',
            'car_condition' => $validated['description'],
        ]);

        $record->load(['car', 'assignedStaff']);

        return response()->json([
            'message' => 'Maintenance record created',
            'record'  => $record,
        ], 201);
    }

    /**
     * Staff updates progress on a maintenance record.
     */
    public function updateProgress(Request $request, MaintenanceRecord $maintenanceRecord)
    {
        $validated = $request->validate([
            'staff_notes' => 'required|string|max:2000',
            'status'      => 'nullable|string|in:in_progress,completed',
        ]);

        $data = ['staff_notes' => $validated['staff_notes']];

        if (isset($validated['status'])) {
            $data['status'] = $validated['status'];

            if ($validated['status'] === 'in_progress' && !$maintenanceRecord->started_at) {
                $data['started_at'] = now();
            }

            if ($validated['status'] === 'completed') {
                $data['completed_at'] = now();
                // Set car back to Available
                $maintenanceRecord->car->update([
                    'car_status'    => 'Available',
                    'car_condition' => 'Selesai maintenance: ' . $validated['staff_notes'],
                ]);
            }
        }

        $maintenanceRecord->update($data);
        $maintenanceRecord->load(['car', 'assignedStaff']);

        return response()->json([
            'message' => 'Progress updated',
            'record'  => $maintenanceRecord,
        ], 200);
    }

    /**
     * Admin marks maintenance as complete.
     */
    public function complete(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        $maintenanceRecord->car->update([
            'car_status'    => 'Available',
            'car_condition' => 'Selesai maintenance: ' . $maintenanceRecord->description,
        ]);

        $maintenanceRecord->load(['car', 'assignedStaff']);

        return response()->json([
            'message' => 'Maintenance completed, car is now available',
            'record'  => $maintenanceRecord,
        ], 200);
    }

    /**
     * Get staff list (users with Staff role) for assignment dropdown.
     */
    public function staffList()
    {
        $staffUsers = User::whereHas('roles', function ($q) {
            $q->where('name', 'Staff');
        })->select('id', 'name', 'email')->get();

        return response()->json(['staff' => $staffUsers], 200);
    }

    /**
     * Staff views their assigned maintenance tasks.
     */
    public function myTasks(Request $request)
    {
        $records = MaintenanceRecord::with(['car'])
            ->where('assigned_staff_id', $request->user()->id)
            ->orderByRaw("CASE status WHEN 'in_progress' THEN 1 WHEN 'pending' THEN 2 WHEN 'completed' THEN 3 ELSE 4 END")
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['records' => $records], 200);
    }
}
