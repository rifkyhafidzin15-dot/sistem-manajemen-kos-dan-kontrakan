<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rental_rate', 12, 2); // Tarif yang dikunci saat kontrak dibuat
            $table->decimal('deposit_amount', 12, 2)->default(0);
            $table->string('status')->default('ACTIVE'); // ACTIVE, ENDED, CANCELLED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};