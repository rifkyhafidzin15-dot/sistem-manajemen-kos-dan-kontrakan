<?php

use App\Models\Property;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

function createPublicListingSchema(): void
{
    Schema::dropIfExists('room_facilities');
    Schema::dropIfExists('facilities');
    Schema::dropIfExists('rooms');
    Schema::dropIfExists('properties');

    Schema::create('properties', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->text('address');
        $table->date('acquisition_date');
        $table->decimal('initial_investment', 15, 2);
        $table->string('base_currency', 3);
        $table->string('status');
        $table->timestamps();
    });

    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('property_id');
        $table->string('room_number');
        $table->string('room_type');
        $table->decimal('base_price', 12, 2);
        $table->string('size')->nullable();
        $table->string('status');
        $table->timestamps();
    });

    Schema::create('facilities', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    Schema::create('room_facilities', function (Blueprint $table) {
        $table->foreignId('room_id');
        $table->foreignId('facility_id');
    });
}

function createManagementAuthSchema(): void
{
    Schema::dropIfExists('users');
    Schema::dropIfExists('roles');

    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->foreignId('role_id');
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->boolean('is_active')->default(true);
        $table->rememberToken();
        $table->timestamps();
    });
}

it('shows available units without exposing occupied or archived ones', function () {
    createPublicListingSchema();

    $activeProperty = Property::create([
        'name' => 'Kost Cempaka',
        'address' => 'Jl. Melati No. 10',
        'acquisition_date' => '2025-01-01',
        'initial_investment' => 1500000000,
        'base_currency' => 'IDR',
        'status' => 'ACTIVE',
    ]);

    Room::create([
        'property_id' => $activeProperty->id,
        'room_number' => 'K-01',
        'room_type' => 'Kost Kamar Mandi Dalam',
        'size' => '16m2',
        'base_price' => 850000,
        'status' => 'AVAILABLE',
    ]);

    Room::create([
        'property_id' => $activeProperty->id,
        'room_number' => 'K-02',
        'room_type' => 'Unit Rahasia Terisi',
        'size' => '20m2',
        'base_price' => 1200000,
        'status' => 'OCCUPIED',
    ]);

    $archivedProperty = Property::create([
        'name' => 'Properti Arsip',
        'address' => 'Alamat Privat',
        'acquisition_date' => '2024-01-01',
        'initial_investment' => 2000000000,
        'base_currency' => 'IDR',
        'status' => 'ARCHIVED',
    ]);

    Room::create([
        'property_id' => $archivedProperty->id,
        'room_number' => 'A-01',
        'room_type' => 'Unit Arsip Rahasia',
        'size' => '24m2',
        'base_price' => 2000000,
        'status' => 'AVAILABLE',
    ]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Kost Cempaka')
        ->assertSee('Kost Kamar Mandi Dalam')
        ->assertSee('850.000')
        ->assertDontSee('Unit Rahasia Terisi')
        ->assertDontSee('Properti Arsip')
        ->assertDontSee('Unit Arsip Rahasia');
});

it('redirects guests from every management route to login', function () {
    foreach ([
        '/management',
        '/management/units',
        '/management/tenants',
        '/management/payments',
        '/management/expenses',
    ] as $uri) {
        $this->get($uri)->assertRedirect(route('login'));
    }

    $this->post('/management/leases')->assertRedirect(route('login'));
    $this->delete('/management/tenants/1')->assertRedirect(route('login'));
});

it('allows an active staff account to sign in to management', function () {
    createManagementAuthSchema();
    createPublicListingSchema();

    $role = Role::create(['name' => 'staff']);
    $user = User::create([
        'role_id' => $role->id,
        'name' => 'Staf Kost',
        'email' => 'staff@example.test',
        'password' => 'correct-password',
        'is_active' => true,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
    $this->get('/management')->assertOk();
});

it('rejects inactive management accounts', function () {
    createManagementAuthSchema();

    $role = Role::create(['name' => 'staff']);
    $user = User::create([
        'role_id' => $role->id,
        'name' => 'Staf Nonaktif',
        'email' => 'inactive@example.test',
        'password' => 'correct-password',
        'is_active' => false,
    ]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'correct-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});
