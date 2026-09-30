<?php

use App\Models\Customer;
use App\Models\Lease;
use App\Models\LeaseCharge;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('dashboard provides realtime rent collection chart data and renders svg elements', function () {
    $role = Role::create(['name' => 'OWNER']);
    $user = User::factory()->create([
        'role_id' => $role->id,
        'email' => 'owner_chart@kost.com',
    ]);

    $property = Property::create([
        'name' => 'Kost Harmoni Eksklusif',
        'address' => 'Jl. Boulevard No. 10',
        'acquisition_date' => '2025-01-01',
        'initial_investment' => 2000000000,
        'base_currency' => 'IDR',
        'status' => 'ACTIVE',
    ]);

    $room = Room::create([
        'property_id' => $property->id,
        'room_number' => 'A101',
        'room_type' => 'Deluxe',
        'base_price' => 1500000,
        'status' => 'OCCUPIED',
    ]);

    $customer = Customer::create([
        'name' => 'Budi Santoso',
        'phone' => '081234567890',
        'email' => 'budi@test.com',
        'status' => 'ACTIVE',
    ]);

    $lease = Lease::create([
        'customer_id' => $customer->id,
        'room_id' => $room->id,
        'start_date' => now()->subMonths(3)->startOfMonth(),
        'end_date' => now()->addMonths(9)->endOfMonth(),
        'rental_rate' => 1500000,
        'deposit_amount' => 1500000,
        'status' => 'ACTIVE',
    ]);

    LeaseCharge::create([
        'lease_id' => $lease->id,
        'period_start' => now()->startOfMonth(),
        'period_end' => now()->endOfMonth(),
        'amount' => 1500000,
        'due_date' => now()->startOfMonth(),
        'status' => 'PAID',
    ]);

    Payment::create([
        'lease_id' => $lease->id,
        'customer_id' => $customer->id,
        'amount' => 1500000,
        'payment_date' => now(),
        'method' => 'Transfer BCA',
        'status' => 'SUCCESS',
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertStatus(200);
    $response->assertViewHas('collectionChart');

    $chart = $response->viewData('collectionChart');
    expect($chart)->toBeArray()
        ->toHaveKeys(['intervals', 'collection_rate', 'on_time_rate', 'donut_score', 'total_collected', 'line_actual_path', 'line_target_path'])
        ->and(count($chart['intervals']))->toBe(7)
        ->and($chart['total_collected'])->toBeGreaterThanOrEqual(1500000);

    $response->assertSee('Rent Collection');
    $response->assertSee('Realisasi Kas');
    $response->assertSee('Target Penagihan');
    $response->assertSee('Koleksi Tepat Waktu');
    $response->assertSee('Rasio Penagihan');
    $response->assertSee('SCORE');
});
