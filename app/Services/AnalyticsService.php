<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;

class AnalyticsService
{
    /**
     * Menghitung Tingkat Keterisian Kamar (Occupancy Rate)
     */
    public function calculateOccupancyRate(int $propertyId): array
    {
        $totalRooms = Room::where('property_id', $propertyId)->count();
        $occupiedRooms = Room::where('property_id', $propertyId)
            ->where('status', 'OCCUPIED')
            ->count();

        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        return [
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'available_rooms' => $totalRooms - $occupiedRooms,
            'occupancy_rate' => round($occupancyRate, 2), // Persentase %
        ];
    }

    /**
     * Menghitung Imbal Hasil Investasi Tahunan (Gross Yield)
     */
    public function calculateGrossYield(int $propertyId, int $year): array
    {
        $property = Property::findOrFail($propertyId);

        // Total pendapatan kas masuk selama 1 tahun penuh
        $annualRevenue = Payment::query()
            ->join('leases', 'payments.lease_id', '=', 'leases.id')
            ->join('rooms', 'leases.room_id', '=', 'rooms.id')
            ->where('rooms.property_id', $propertyId)
            ->where('payments.status', 'SUCCESS')
            ->whereYear('payments.payment_date', $year)
            ->sum('payments.amount');

        $initialInvestment = $property->initial_investment;

        $grossYield = ($initialInvestment > 0)
            ? ($annualRevenue / $initialInvestment) * 100
            : 0;

        return [
            'year' => $year,
            'initial_investment' => $initialInvestment,
            'annual_revenue' => $annualRevenue,
            'gross_yield' => round($grossYield, 2), // Persentase %
        ];
    }
}
