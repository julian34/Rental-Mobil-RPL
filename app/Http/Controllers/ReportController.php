<?php

namespace App\Http\Controllers;

use App\Models\RentalTransaction;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Revenue analytics — real data from rental_transactions.
     */
    public function revenue(Request $request)
    {
        $period = $request->input('period', 'monthly');

        // Determine date range based on period
        [$startDate, $endDate, $prevStart, $prevEnd] = $this->periodRange($period);

        // ── Current period totals ──
        $currentRevenue = (float) RentalTransaction::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->selectRaw('COALESCE(SUM(COALESCE(grand_total, total_price)), 0) as total')
            ->value('total');

        $currentRentals = RentalTransaction::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();

        $currentAvgDays = RentalTransaction::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->avg('duration_days') ?? 0;

        // ── Previous period totals (for % change) ──
        $prevRevenue = (float) RentalTransaction::where('status', 'completed')
            ->whereBetween('updated_at', [$prevStart, $prevEnd])
            ->selectRaw('COALESCE(SUM(COALESCE(grand_total, total_price)), 0) as total')
            ->value('total');

        $prevRentals = RentalTransaction::where('status', 'completed')
            ->whereBetween('updated_at', [$prevStart, $prevEnd])
            ->count();

        // ── All-time totals ──
        $allTimeRevenue = (float) RentalTransaction::where('status', 'completed')
            ->selectRaw('COALESCE(SUM(COALESCE(grand_total, total_price)), 0) as total')
            ->value('total');
        $allTimeRentals = RentalTransaction::where('status', 'completed')->count();
        $totalLateCharge = (float) RentalTransaction::where('status', 'completed')->sum('late_charge');
        $avgRevenuePerRental = $allTimeRentals > 0 ? $allTimeRevenue / $allTimeRentals : 0;

        // ── Active cars count ──
        $totalCars = Car::count();
        $rentedCars = Car::where('car_status', 'Rented')->count();
        $fleetUtilization = $totalCars > 0 ? round($rentedCars / $totalCars * 100) : 0;

        // % change helpers
        $revenueChange = $prevRevenue > 0
            ? round(($currentRevenue - $prevRevenue) / $prevRevenue * 100, 1)
            : ($currentRevenue > 0 ? 100 : 0);
        $rentalChange = $prevRentals > 0
            ? round(($currentRentals - $prevRentals) / $prevRentals * 100, 1)
            : ($currentRentals > 0 ? 100 : 0);

        // ── Monthly revenue chart (last 6 months) ──
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd   = Carbon::now()->subMonths($i)->endOfMonth();
            $rev = (float) RentalTransaction::where('status', 'completed')
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->selectRaw('COALESCE(SUM(COALESCE(grand_total, total_price)), 0) as total')
                ->value('total');
            $count = RentalTransaction::where('status', 'completed')
                ->whereBetween('updated_at', [$monthStart, $monthEnd])
                ->count();
            $monthlyRevenue[] = [
                'month'    => $monthStart->translatedFormat('M Y'),
                'monthKey' => $monthStart->format('M'),
                'revenue'  => (float) $rev,
                'rentals'  => $count,
                'current'  => $i === 0,
            ];
        }

        // ── Revenue by car brand ──
        $byBrand = RentalTransaction::where('rental_transactions.status', 'completed')
            ->join('cars', 'rental_transactions.car_id', '=', 'cars.car_id')
            ->select('cars.brand', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(rental_transactions.grand_total, rental_transactions.total_price)) as total'))
            ->groupBy('cars.brand')
            ->orderByDesc('total')
            ->get();

        $totalBrandRentals = $byBrand->sum('count');
        $brandChart = $byBrand->map(function ($row) use ($totalBrandRentals) {
            return [
                'brand'   => $row->brand,
                'count'   => $row->count,
                'revenue' => (float) $row->total,
                'pct'     => $totalBrandRentals > 0 ? round($row->count / $totalBrandRentals * 100) : 0,
            ];
        })->values();

        // ── Monthly detail rows (last 6 months) ──
        $detailRows = [];
        for ($i = 0; $i < 6; $i++) {
            $mStart = Carbon::now()->subMonths($i)->startOfMonth();
            $mEnd   = Carbon::now()->subMonths($i)->endOfMonth();

            $txs = RentalTransaction::where('status', 'completed')
                ->whereBetween('updated_at', [$mStart, $mEnd]);

            $rev  = (float) (clone $txs)->selectRaw('COALESCE(SUM(COALESCE(grand_total, total_price)), 0) as total')->value('total');
            $cnt  = (clone $txs)->count();
            $avgD = round((float) ((clone $txs)->avg('duration_days') ?? 0), 1);

            // New customers this month (first completed transaction)
            $newCust = RentalTransaction::where('status', 'completed')
                ->whereBetween('updated_at', [$mStart, $mEnd])
                ->whereIn('user_id', function ($q) use ($mStart, $mEnd) {
                    $q->select('user_id')
                      ->from('rental_transactions')
                      ->where('status', 'completed')
                      ->groupBy('user_id')
                      ->havingRaw('MIN(updated_at) BETWEEN ? AND ?', [$mStart, $mEnd]);
                })
                ->distinct('user_id')
                ->count('user_id');

            $detailRows[] = [
                'period'       => $mStart->translatedFormat('F Y'),
                'transactions' => $cnt,
                'revenue'      => $rev,
                'newCustomers' => $newCust,
                'avgDays'      => $avgD,
                'current'      => $i === 0,
            ];
        }

        // ── Top performing cars ──
        $topCars = RentalTransaction::where('rental_transactions.status', 'completed')
            ->join('cars', 'rental_transactions.car_id', '=', 'cars.car_id')
            ->select(
                'cars.car_id',
                'cars.brand',
                'cars.model',
                'cars.license_plate',
                DB::raw('COUNT(*) as rental_count'),
                DB::raw('SUM(COALESCE(rental_transactions.grand_total, rental_transactions.total_price)) as total_revenue')
            )
            ->groupBy('cars.car_id', 'cars.brand', 'cars.model', 'cars.license_plate')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get();

        // ── Revenue by payment method ──
        $byPayment = RentalTransaction::where('status', 'completed')
            ->whereNotNull('payment_method')
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(COALESCE(grand_total, total_price)) as total'))
            ->groupBy('payment_method')
            ->get();

        return response()->json([
            'kpis' => [
                'all_time_revenue'    => (float) $allTimeRevenue,
                'all_time_rentals'    => $allTimeRentals,
                'current_revenue'     => (float) $currentRevenue,
                'current_rentals'     => $currentRentals,
                'revenue_change'      => $revenueChange,
                'rental_change'       => $rentalChange,
                'avg_revenue_rental'  => round($avgRevenuePerRental),
                'avg_rental_days'     => round($currentAvgDays, 1),
                'total_late_charge'   => (float) $totalLateCharge,
                'fleet_utilization'   => $fleetUtilization,
                'total_cars'          => $totalCars,
                'rented_cars'         => $rentedCars,
            ],
            'monthly_revenue' => $monthlyRevenue,
            'brand_chart'     => $brandChart,
            'detail_rows'     => $detailRows,
            'top_cars'        => $topCars,
            'by_payment'      => $byPayment,
            'period'          => $period,
            'range'           => [
                'start' => $startDate->toDateString(),
                'end'   => $endDate->toDateString(),
            ],
        ], 200);
    }

    /**
     * CSV export of completed transactions.
     */
    public function exportCsv(Request $request)
    {
        $period = $request->input('period', 'monthly');
        [$startDate, $endDate] = $this->periodRange($period);

        $transactions = RentalTransaction::with(['user', 'car', 'cashier'])
            ->where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->orderByDesc('updated_at')
            ->get();

        $csv = "Invoice,Pelanggan,Mobil,Plat,Mulai,Selesai,Durasi (hari),Harga/Hari,Total Sewa,Hari Telat,Denda Telat,Grand Total,Metode Bayar,Kasir,Tanggal Selesai\n";

        foreach ($transactions as $tx) {
            $csv .= implode(',', [
                $tx->invoice_number,
                '"' . str_replace('"', '""', $tx->user->name ?? '') . '"',
                '"' . ($tx->car->brand ?? '') . ' ' . ($tx->car->model ?? '') . '"',
                $tx->car->license_plate ?? '',
                $tx->start_date?->format('Y-m-d') ?? '',
                $tx->end_date?->format('Y-m-d') ?? '',
                $tx->duration_days,
                $tx->rental_price_per_day,
                $tx->total_price,
                $tx->late_days ?? 0,
                $tx->late_charge ?? 0,
                $tx->grand_total ?? $tx->total_price,
                $tx->payment_method ?? '',
                '"' . str_replace('"', '""', $tx->cashier->name ?? '') . '"',
                $tx->updated_at?->format('Y-m-d H:i'),
            ]) . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="revenue-report-' . $period . '-' . now()->format('Ymd') . '.csv"',
        ]);
    }

    /**
     * Calculate period date ranges.
     */
    private function periodRange(string $period): array
    {
        $now = Carbon::now();

        switch ($period) {
            case 'weekly':
                $start    = $now->copy()->startOfWeek();
                $end      = $now->copy()->endOfWeek();
                $prevStart = $now->copy()->subWeek()->startOfWeek();
                $prevEnd   = $now->copy()->subWeek()->endOfWeek();
                break;
            case 'quarterly':
                $start    = $now->copy()->firstOfQuarter();
                $end      = $now->copy()->lastOfQuarter()->endOfDay();
                $prevStart = $now->copy()->subQuarter()->firstOfQuarter();
                $prevEnd   = $now->copy()->subQuarter()->lastOfQuarter()->endOfDay();
                break;
            case 'yearly':
                $start    = $now->copy()->startOfYear();
                $end      = $now->copy()->endOfYear();
                $prevStart = $now->copy()->subYear()->startOfYear();
                $prevEnd   = $now->copy()->subYear()->endOfYear();
                break;
            default: // monthly
                $start    = $now->copy()->startOfMonth();
                $end      = $now->copy()->endOfMonth();
                $prevStart = $now->copy()->subMonth()->startOfMonth();
                $prevEnd   = $now->copy()->subMonth()->endOfMonth();
                break;
        }

        return [$start, $end, $prevStart, $prevEnd];
    }
}
