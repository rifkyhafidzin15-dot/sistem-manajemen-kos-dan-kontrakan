<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Payment;
use Carbon\Carbon;

class FinancialLedgerService
{
    /**
     * Menghitung Laporan Laba Rugi (Profit & Loss Statement) Bulanan
     */
    public function calculateMonthlyProfitAndLoss(int $propertyId, int $year, int $month): array
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // Total Pendapatan Sewa Terkumpul (Cash Collected / Gross Revenue)
        $totalRevenue = Payment::query()
            ->join('leases', 'payments.lease_id', '=', 'leases.id')
            ->join('rooms', 'leases.room_id', '=', 'rooms.id')
            ->where('rooms.property_id', $propertyId)
            ->where('payments.status', 'SUCCESS')
            ->whereBetween('payments.payment_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('payments.amount');

        // 2. Total Pengeluaran Operasional (OPEX)
        $totalOpex = Expense::where('property_id', $propertyId)
            ->whereHas('category', function ($query) {
                $query->where('expense_type', 'OPEX');
            })
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        // 3. Total Pengeluaran Capital (CAPEX - Investasi Aset/Renovasi)
        $totalCapex = Expense::where('property_id', $propertyId)
            ->whereHas('category', function ($query) {
                $query->where('expense_type', 'CAPEX');
            })
            ->whereBetween('expense_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->sum('amount');

        // 4. Net Operating Income (NOI) / Laba Bersih Operasional
        $netOperatingIncome = $totalRevenue - $totalOpex;

        // 5. Net Cash Flow (NOI dikurangi CAPEX)
        $netCashFlow = $netOperatingIncome - $totalCapex;

        return [
            'period' => $startDate->translatedFormat('F Y'),
            'gross_revenue' => $totalRevenue,
            'total_opex' => $totalOpex,
            'net_operating_income' => $netOperatingIncome,
            'total_capex' => $totalCapex,
            'net_cash_flow' => $netCashFlow,
        ];
    }
}
