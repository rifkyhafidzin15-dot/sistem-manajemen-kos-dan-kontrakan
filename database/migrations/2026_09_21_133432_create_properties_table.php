<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->date('acquisition_date');
            $table->decimal('initial_investment', 15, 2); // Digunakan untuk menghitung Yield on Initial Investment
            $table->string('base_currency', 3)->default('IDR');
            $table->string('status')->default('ACTIVE'); // ACTIVE, ARCHIVED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};