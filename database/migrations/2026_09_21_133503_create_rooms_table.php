<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained('properties')->cascadeOnDelete();
            $table->string('room_number');
            $table->string('room_type');
            $table->decimal('base_price', 12, 2);
            $table->string('size')->nullable(); // contoh: '3x4m'
            $table->string('status')->default('AVAILABLE'); // AVAILABLE, RESERVED, OCCUPIED, MAINTENANCE, INACTIVE
            $table->timestamps();

            // Unique Constraint: Nomor kamar harus unik dalam 1 properti
            $table->unique(['property_id', 'room_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
