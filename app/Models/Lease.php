<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'room_id',
        'booking_id',
        'start_date',
        'end_date',
        'rental_rate',
        'deposit_amount',
        'status',
    ];

    // Jembatan relasi ke data Kamar (Room)
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    // Jembatan relasi ke data Penyewa (Customer)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(LeaseCharge::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
