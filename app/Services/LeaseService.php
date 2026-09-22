<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Lease;
use App\Models\LeaseCharge;
use App\Models\Payment;
use App\Models\PaymentAllocation;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LeaseService
{
    /**
     * Membuat Kontrak Sewa Baru (Lease) beserta Tagihan Bulan Pertama & Alokasi DP
     */
    public function createLease(array $data): Lease
    {
        return DB::transaction(function () use ($data) {
            $room = Room::findOrFail($data['room_id']);

            // 1. Perhitungan Tarif Sewa (Base Price + Rp 50.000 jika 2 orang)
            $extraOccupantFee = ($data['occupant_count'] ?? 1) > 1 ? 50000.00 : 0.00;
            $contractedRentalRate = $room->base_price + $extraOccupantFee;

            // 2. Tentukan Tanggal Selesai Periode Pertama (1 Bulan Penuh)
            $startDate = Carbon::parse($data['start_date']);
            $endDate = $startDate->copy()->addMonth();

            // 3. Buat Record Lease (Kontrak Sewa)
            $lease = Lease::create([
                'customer_id'    => $data['customer_id'],
                'room_id'        => $room->id,
                'booking_id'     => $data['booking_id'] ?? null,
                'start_date'     => $startDate->format('Y-m-d'),
                'end_date'       => $endDate->format('Y-m-d'),
                'rental_rate'    => $contractedRentalRate,
                'deposit_amount' => $data['deposit_amount'] ?? 0.00,
                'status'         => 'ACTIVE',
            ]);

            // 4. Update Status Kamar menjadi OCCUPIED
            $room->update(['status' => 'OCCUPIED']);

            // 5. Terbitkan Tagihan Sewa Bulan Pertama (Lease Charge)
            $charge = LeaseCharge::create([
                'lease_id'     => $lease->id,
                'period_start' => $startDate->format('Y-m-d'),
                'period_end'   => $endDate->format('Y-m-d'),
                'due_date'     => $startDate->format('Y-m-d'), // Jatuh tempo di hari pertama
                'amount'       => $contractedRentalRate,
                'status'       => 'PENDING',
            ]);

            // 6. Jika ada Booking ID dengan DP, Alokasikan Pemotongan DP
            if (!empty($data['booking_id'])) {
                $booking = Booking::find($data['booking_id']);
                if ($booking) {
                    $booking->update(['status' => 'APPROVED']);
                }

                $dpAmount = $data['dp_amount'] ?? 0.00;
                if ($dpAmount > 0) {
                    // Catat penerimaan kas DP
                    $payment = Payment::create([
                        'lease_id'       => $lease->id,
                        'paid_at'        => now(),
                        'amount'         => $dpAmount,
                        'payment_method' => $data['dp_payment_method'] ?? 'TRANSFER',
                        'status'         => 'SUCCESS',
                    ]);

                    // Alokasikan DP memotong Tagihan Bulan Pertama
                    PaymentAllocation::create([
                        'payment_id'      => $payment->id,
                        'lease_charge_id' => $charge->id,
                        'amount'          => $dpAmount,
                    ]);

                    // Update Status Tagihan (PARTIAL atau PAID jika DP Lunas)
                    $charge->update([
                        'status' => ($dpAmount >= $contractedRentalRate) ? 'PAID' : 'PARTIAL'
                    ]);
                }
            }

            return $lease;
        });
    }
}