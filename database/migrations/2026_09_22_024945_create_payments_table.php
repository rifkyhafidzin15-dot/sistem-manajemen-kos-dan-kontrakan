<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lease_id')->constrained('leases')->cascadeOnDelete();
            $table->timestamp('paid_at');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method'); // CASH, TRANSFER, E-WALLET
            $table->string('status')->default('SUCCESS'); // SUCCESS, PENDING, REJECTED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};