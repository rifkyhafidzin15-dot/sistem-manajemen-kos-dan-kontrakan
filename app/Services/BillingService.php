<?php

namespace App\Services;

use App\Models\Lease;
use App\Models\LeaseCharge;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Menerbitkan Tagihan Bulanan Otomatis Berdasarkan Tanggal Masuk (Anniversary Date)
     */
    public function generateRecurringCharge(Lease $lease, Carbon $targetDate): ?LeaseCharge
    {
        if ($lease->status !== 'ACTIVE') {
            return null;
        }

        $periodStart = $targetDate->copy();
        $periodEnd = $periodStart->copy()->addMonth();
        $dueDate = $periodStart->copy(); // Jatuh tempo sesuai anak tanggal masuk

        // Cek apakah tagihan periode ini sudah pernah diterbitkan
        $existingCharge = LeaseCharge::where('lease_id', $lease->id)
            ->where('period_start', $periodStart->format('Y-m-d'))
            ->first();

        if ($existingCharge) {
            return $existingCharge;
        }

        return LeaseCharge::create([
            'lease_id'     => $lease->id,
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end'   => $periodEnd->format('Y-m-d'),
            'due_date'     => $dueDate->format('Y-m-d'),
            'amount'       => $lease->rental_rate,
            'status'       => 'PENDING',
        ]);
    }

    /**
     * Mencatat Pembayaran dari Penghuni & Mengalokasikannya ke Tagihan
     */
    public function recordPayment(LeaseCharge $charge, float $amountPaid, string $paymentMethod): Payment
    {
        return DB::transaction(function () use ($charge, $amountPaid, $paymentMethod) {
            // 1. Catat Penerimaan Kas
            $payment = Payment::create([
                'lease_id'       => $charge->lease_id,
                'paid_at'        => now(),
                'amount'         => $amountPaid,
                'payment_method' => $paymentMethod,
                'status'         => 'SUCCESS',
            ]);

            // 2. Hubungkan ke Tagihan
            PaymentAllocation::create([
                'payment_id'      => $payment->id,
                'lease_charge_id' => $charge->id,
                'amount'          => $amountPaid,
            ]);

            // 3. Hitung Total Pembayaran Terkumpul untuk Tagihan Ini
            $totalAllocated = PaymentAllocation::where('lease_charge_id', $charge->id)->sum('amount');

            // 4. Update Status Tagihan (PAID / PARTIAL)
            if ($totalAllocated >= $charge->amount) {
                $charge->update(['status' => 'PAID']);
            } else {
                $charge->update(['status' => 'PARTIAL']);
            }

            return $payment;
        });
    }
}