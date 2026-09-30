<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TenantController;
use App\Models\Customer;
use App\Models\Lease;
use App\Models\LeaseCharge;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Services\AnalyticsService;
use App\Services\FinancialLedgerService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $properties = Property::query()
        ->where('status', 'ACTIVE')
        ->whereHas('rooms', fn ($query) => $query->where('status', 'AVAILABLE'))
        ->with(['rooms' => fn ($query) => $query
            ->where('status', 'AVAILABLE')
            ->with('facilities')])
        ->get();

    $totalAvailableCount = $properties->sum(fn ($p) => $p->rooms->count());
    $totalRoomsCount = Schema::hasTable('rooms') ? Room::count() : $totalAvailableCount;
    $activeCustomersCount = Schema::hasTable('customers') ? Customer::count() : 0;

    return view('listings.index', compact('properties', 'totalAvailableCount', 'totalRoomsCount', 'activeCustomersCount'));
})->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::prefix('management')->middleware('auth')->group(function () {
    Route::get('/', function (AnalyticsService $analyticsService, FinancialLedgerService $financialService) {
        $property = Property::first();

        $occupancyData = [
            'total_rooms' => 0,
            'occupied_rooms' => 0,
            'available_rooms' => 0,
            'occupancy_rate' => 0,
        ];
        $yieldData = [
            'year' => (int) date('Y'),
            'initial_investment' => 0,
            'annual_revenue' => 0,
            'gross_yield' => 0,
        ];
        $pnlData = [
            'period' => date('F Y'),
            'gross_revenue' => 0,
            'total_opex' => 0,
            'net_operating_income' => 0,
            'total_capex' => 0,
            'net_cash_flow' => 0,
        ];
        $rooms = collect();
        $recentPayments = collect();
        $recentLeases = collect();
        $pendingChargesCount = 0;
        $activeCustomersCount = 0;

        if ($property) {
            $propertyId = $property->id;
            $currentYear = (int) date('Y');
            $currentMonth = (int) date('n');

            $occupancyData = $analyticsService->calculateOccupancyRate($propertyId);
            $yieldData = $analyticsService->calculateGrossYield($propertyId, $currentYear);
            $pnlData = $financialService->calculateMonthlyProfitAndLoss($propertyId, $currentYear, $currentMonth);
            $rooms = Room::where('property_id', $propertyId)->with('facilities')->orderBy('room_number')->get();

            if (Schema::hasTable('payments')) {
                $recentPayments = Payment::with(['customer', 'lease.room'])->latest('payment_date')->take(5)->get();
            }
            if (Schema::hasTable('leases')) {
                $recentLeases = Lease::with(['customer', 'room'])->where('status', 'ACTIVE')->latest()->take(5)->get();
            }
            if (Schema::hasTable('lease_charges')) {
                $pendingChargesCount = LeaseCharge::where('status', 'PENDING')->count();
            }
            if (Schema::hasTable('customers')) {
                $activeCustomersCount = Customer::count();
            }
        }

        // Realtime Rent Collection Chart Calculation
        $chartIntervals = [];
        $now = now();
        $totalCollectedSum = 0;
        $totalTargetSum = 0;
        $dayCodes = ['04', '10', '15', '18', '19', '20', '23'];

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i);
            $year = (int) $date->year;
            $month = (int) $date->month;

            $monthLabel = $date->translatedFormat('M');
            $idx = 6 - $i;
            $dayCode = $dayCodes[$idx] ?? $date->format('d');

            $actualRev = 0;
            $targetRev = 0;

            if ($property && Schema::hasTable('payments')) {
                $actualRev = (float) Payment::query()
                    ->join('leases', 'payments.lease_id', '=', 'leases.id')
                    ->join('rooms', 'leases.room_id', '=', 'rooms.id')
                    ->where('rooms.property_id', $property->id)
                    ->where('payments.status', 'SUCCESS')
                    ->whereYear('payments.payment_date', $year)
                    ->whereMonth('payments.payment_date', $month)
                    ->sum('payments.amount');
            }

            if ($property && Schema::hasTable('lease_charges')) {
                $targetRev = (float) LeaseCharge::query()
                    ->join('leases', 'lease_charges.lease_id', '=', 'leases.id')
                    ->join('rooms', 'leases.room_id', '=', 'rooms.id')
                    ->where('rooms.property_id', $property->id)
                    ->whereYear('lease_charges.due_date', $year)
                    ->whereMonth('lease_charges.due_date', $month)
                    ->sum('lease_charges.amount');
            }

            if ($targetRev <= 0 && $property) {
                $targetRev = (float) Room::where('property_id', $property->id)->where('status', 'OCCUPIED')->sum('base_price');
                if ($targetRev <= 0) {
                    $targetRev = (float) Room::where('property_id', $property->id)->sum('base_price');
                }
            }

            $totalCollectedSum += $actualRev;
            $totalTargetSum += $targetRev;

            $chartIntervals[] = [
                'label' => $monthLabel,
                'code' => $dayCode,
                'year' => $year,
                'month' => $month,
                'actual' => $actualRev,
                'target' => $targetRev,
            ];
        }

        $maxAmount = max(1000000, (float) collect($chartIntervals)->max(fn ($it) => max($it['actual'], $it['target'])));

        $barXCoords = [92, 162, 232, 302, 372, 442, 513];
        $barGradients = [
            'url(#barGrad1)',
            'url(#barGrad2)',
            'url(#barGrad3)',
            'url(#barGrad4)',
            'url(#barGrad5)',
            'url(#barGrad6)',
            'url(#barGrad7)',
        ];

        foreach ($chartIntervals as $idx => &$interval) {
            $ratio = $maxAmount > 0 ? min(1, $interval['actual'] / $maxAmount) : 0;
            $targetRatio = $maxAmount > 0 ? min(1, $interval['target'] / $maxAmount) : 0;
            $bx = $barXCoords[$idx] ?? (92 + $idx * 70);

            $interval['cx'] = $bx;
            $interval['x'] = $bx - 17;
            $interval['width'] = ($idx === 6) ? 36 : 34;
            $interval['gradient'] = $barGradients[$idx] ?? 'url(#barGrad7)';
            $interval['bar_height'] = $interval['actual'] > 0 ? max(10, (int) round($ratio * 153)) : 4;
            $interval['bar_y'] = (int) (195 - $interval['bar_height']);
            $interval['bar_pct'] = (int) round($ratio * 100);
            $interval['line_y_actual'] = (int) round(188 - ($ratio * 165));
            $interval['line_y_target'] = (int) round(185 - ($targetRatio * 160));
        }
        unset($interval);

        // Build smooth Cubic Bézier Spline paths
        $actualPoints = [['x' => 45, 'y' => 188]];
        $targetPoints = [['x' => 45, 'y' => 185]];

        foreach ($chartIntervals as $inv) {
            $actualPoints[] = ['x' => $inv['cx'], 'y' => $inv['line_y_actual'], 'actual' => $inv['actual'], 'label' => $inv['label']];
            $targetPoints[] = ['x' => $inv['cx'], 'y' => $inv['line_y_target'], 'target' => $inv['target'], 'label' => $inv['label']];
        }

        $buildSpline = function (array $pts): string {
            $count = count($pts);
            if ($count === 0) {
                return '';
            }
            if ($count === 1) {
                return "M {$pts[0]['x']},{$pts[0]['y']}";
            }

            $d = "M {$pts[0]['x']},{$pts[0]['y']}";
            for ($i = 0; $i < $count - 1; $i++) {
                $p0 = $i > 0 ? $pts[$i - 1] : $pts[$i];
                $p1 = $pts[$i];
                $p2 = $pts[$i + 1];
                $p3 = $i + 2 < $count ? $pts[$i + 2] : $p2;

                $cp1x = (int) round($p1['x'] + ($p2['x'] - $p0['x']) / 6);
                $cp1y = (int) round($p1['y'] + ($p2['y'] - $p0['y']) / 6);
                $cp2x = (int) round($p2['x'] - ($p3['x'] - $p1['x']) / 6);
                $cp2y = (int) round($p2['y'] - ($p3['y'] - $p1['y']) / 6);

                $d .= " C {$cp1x},{$cp1y} {$cp2x},{$cp2y} {$p2['x']},{$p2['y']}";
            }

            return $d;
        };

        $lineActualPath = $buildSpline($actualPoints);
        $lineTargetPath = $buildSpline($targetPoints);

        $collectionRate = $totalTargetSum > 0 ? min(100, (int) round(($totalCollectedSum / $totalTargetSum) * 100)) : 0;
        $onTimeRate = 0;
        if (Schema::hasTable('lease_charges')) {
            $totalCharges = LeaseCharge::count();
            $paidCharges = LeaseCharge::where('status', 'PAID')->count();
            if ($totalCharges > 0) {
                $onTimeRate = (int) round(($paidCharges / $totalCharges) * 100);
            }
        }

        $donutTotal = $rooms->count();
        $donutOccupied = $rooms->where('status', 'OCCUPIED')->count();
        $donutScore = $donutOccupied > 0 ? ('+'.($donutOccupied * 100 + $donutTotal * 10)) : '0';

        $collectionChart = [
            'intervals' => $chartIntervals,
            'collection_rate' => $collectionRate,
            'on_time_rate' => $onTimeRate,
            'donut_score' => $donutScore,
            'total_collected' => $totalCollectedSum,
            'total_target' => $totalTargetSum,
            'max_amount' => $maxAmount,
            'line_actual_path' => $lineActualPath,
            'line_target_path' => $lineTargetPath,
            'actual_points' => $actualPoints,
            'target_points' => $targetPoints,
        ];

        return view('dashboard', [
            'property' => $property,
            'occupancyData' => $occupancyData,
            'yieldData' => $yieldData,
            'pnlData' => $pnlData,
            'rooms' => $rooms,
            'recentPayments' => $recentPayments,
            'recentLeases' => $recentLeases,
            'pendingChargesCount' => $pendingChargesCount,
            'activeCustomersCount' => $activeCustomersCount,
            'collectionChart' => $collectionChart,
        ]);
    })->name('dashboard');

    Route::get('/units', function () {
        $rooms = Room::orderBy('room_number')->get();

        return view('units.index', compact('rooms'));
    })->name('units.index');

    Route::get('/tenants', [TenantController::class, 'index'])->name('tenants.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/expenses', fn () => view('expenses.index'))->name('expenses.index');

    Route::post('/leases', [LeaseController::class, 'store'])->name('leases.store');
    Route::delete('/tenants/{id}', [TenantController::class, 'destroy'])->name('tenants.destroy');
});
