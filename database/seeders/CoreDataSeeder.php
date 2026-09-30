<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\Facility;
use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoreDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil Role Owner & Staff dari Database
        $ownerRole = Role::where('name', 'owner')->first();
        $staffRole = Role::where('name', 'staff')->first();

        // 2. Buat Akun Pengguna (Owner & Staff)
        User::firstOrCreate(
            ['email' => 'owner@kost.com'],
            [
                'role_id' => $ownerRole->id,
                'name' => 'Pemilik Kost',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'staff@kost.com'],
            [
                'role_id' => $staffRole->id,
                'name' => 'Staf Operasional',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );

        // 3. Buat Data Properti Utama (110m2)
        $property = Property::create([
            'name' => 'Kost & Kontrakan Utama',
            'address' => 'Jl. Merdeka No. 45',
            'acquisition_date' => '2025-01-01',
            'initial_investment' => 1500000000.00, // Nilai modal awal properti
            'base_currency' => 'IDR',
            'status' => 'ACTIVE',
        ]);

        // 4. Buat Master Fasilitas
        $kmDalam = Facility::create(['name' => 'Kamar Mandi Dalam']);
        $kmLuar = Facility::create(['name' => 'Kamar Mandi Luar']);
        $dapur = Facility::create(['name' => 'Dapur Pribadi']);
        $exhaust = Facility::create(['name' => 'Exhaust Fan']);
        $kasur = Facility::create(['name' => 'Kasur']);
        $lemari = Facility::create(['name' => 'Lemari Pakaian']);
        $parkir = Facility::create(['name' => 'Area Parkir']);
        $airSampah = Facility::create(['name' => 'Iuran Air Pompa & Sampah Included']);

        // -------------------------------------------------------------
        // 5. LANTAI 1: 2 Unit Kontrakan (40m2, 3 Sekat, Rp 1,5 Juta/bln)
        // -------------------------------------------------------------
        for ($i = 1; $i <= 2; $i++) {
            $kontrakan = Room::create([
                'property_id' => $property->id,
                'room_number' => 'K-0'.$i,
                'room_type' => 'Kontrakan 3 Sekat',
                'base_price' => 1500000.00,
                'size' => '40m2',
                'status' => 'AVAILABLE',
            ]);

            $kontrakan->facilities()->attach([
                $kmDalam->id,
                $dapur->id,
                $parkir->id,
                $airSampah->id,
            ]);
        }

        // -------------------------------------------------------------
        // 6. LANTAI 2: 2 Unit Kost KM Dalam (16m2, Rp 850 Ribu/bln)
        // -------------------------------------------------------------
        for ($i = 1; $i <= 2; $i++) {
            $kostKmDalam = Room::create([
                'property_id' => $property->id,
                'room_number' => 'KM-20'.$i,
                'room_type' => 'Kost KM Dalam',
                'base_price' => 850000.00,
                'size' => '16m2',
                'status' => 'AVAILABLE',
            ]);

            $kostKmDalam->facilities()->attach([
                $kmDalam->id,
                $exhaust->id,
                $kasur->id,
                $lemari->id,
                $parkir->id,
                $airSampah->id,
            ]);
        }

        // -------------------------------------------------------------
        // 7. LANTAI 2: 5 Unit Kost KM Luar (Shared KM)
        // -------------------------------------------------------------
        $kamarKmLuarConfig = [
            ['number' => 'K-203', 'size' => '8m2',  'price' => 500000.00, 'type' => 'Kost KM Luar (8m2)'],
            ['number' => 'K-204', 'size' => '8m2',  'price' => 600000.00, 'type' => 'Kost KM Luar (8m2)'],
            ['number' => 'K-205', 'size' => '10m2', 'price' => 650000.00, 'type' => 'Kost KM Luar (10m2)'],
            ['number' => 'K-206', 'size' => '12m2', 'price' => 700000.00, 'type' => 'Kost KM Luar (12m2)'],
            ['number' => 'K-207', 'size' => '16m2', 'price' => 750000.00, 'type' => 'Kost KM Luar (16m2)'],
        ];

        foreach ($kamarKmLuarConfig as $cfg) {
            $kostKmLuar = Room::create([
                'property_id' => $property->id,
                'room_number' => $cfg['number'],
                'room_type' => $cfg['type'],
                'base_price' => $cfg['price'],
                'size' => $cfg['size'],
                'status' => 'AVAILABLE',
            ]);

            $kostKmLuar->facilities()->attach([
                $kmLuar->id,
                $exhaust->id,
                $kasur->id,
                $lemari->id,
                $parkir->id,
                $airSampah->id,
            ]);
        }

        // -------------------------------------------------------------
        // 8. Buat Master Kategori Pengeluaran (OPEX & CAPEX)
        // -------------------------------------------------------------
        ExpenseCategory::firstOrCreate(['name' => 'Listrik & Air Pompa', 'expense_type' => 'OPEX']);
        ExpenseCategory::firstOrCreate(['name' => 'Kebersihan & Sampah', 'expense_type' => 'OPEX']);
        ExpenseCategory::firstOrCreate(['name' => 'Pemeliharaan & Perbaikan', 'expense_type' => 'OPEX']);
        ExpenseCategory::firstOrCreate(['name' => 'Renovasi / Modal Aset', 'expense_type' => 'CAPEX']);
    }
}
