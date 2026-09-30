<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lease;
use App\Models\LeaseCharge;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaseController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input ketat (nomor HP wajib 08 & murni angka)
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'tenant_name' => 'required|string|max:255',
            'phone_number' => ['required', 'string', 'regex:/^08[0-9]{8,13}$/'],
            'start_date' => 'required|date',
            'rent_amount' => 'required|numeric|min:0',
            'deposit' => 'nullable|numeric|min:0',
        ], [
            'phone_number.regex' => 'Nomor HP tidak valid! Harus diawali dengan "08" dan hanya berisi angka (10-15 digit).',
        ]);

        DB::transaction(function () use ($request) {
            // Bersihkan format nomor HP & buat email default
            $cleanPhone = preg_replace('/[^0-9]/', '', $request->phone_number);
            $generatedEmail = $cleanPhone.'@tenant.kost';

            // 2. Simpan atau Ambil Data Customer
            $customer = Customer::firstOrCreate(
                ['phone' => $request->phone_number],
                [
                    'name' => $request->tenant_name,
                    'email' => $generatedEmail,
                ]
            );

            // 3. Ambil Data Kamar
            $room = Room::findOrFail($request->room_id);

            // 4. Format Tanggal Mulai dan Berakhir
            $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
            $endDate = Carbon::parse($startDate)->addMonth()->format('Y-m-d');

            // 5. Simpan Kontrak Sewa (Lease)
            $lease = Lease::create([
                'customer_id' => $customer->id,
                'room_id' => $room->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'rental_rate' => $request->rent_amount,
                'deposit_amount' => $request->deposit ?? 0,
                'status' => 'ACTIVE',
            ]);

            // 6. Terbitkan Tagihan Sewa Bulan Pertama
            LeaseCharge::create([
                'lease_id' => $lease->id,
                'period_start' => $startDate,
                'period_end' => $endDate,
                'due_date' => $startDate,
                'amount' => $request->rent_amount,
                'status' => 'PENDING',
            ]);

            // 7. Ubah Status Kamar Menjadi Terisi (Occupied)
            $room->update(['status' => 'OCCUPIED']);
        });

        return redirect()->route('dashboard')->with('success', 'Penyewa baru berhasil didaftarkan dan kamar terisi!');
    }
}
