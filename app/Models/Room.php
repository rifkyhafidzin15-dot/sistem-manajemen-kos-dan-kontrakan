<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_number',
        'room_type',
        'base_price',
        'size',
        'status',
    ];

    // Relasi: Kamar milik 1 Properti
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    // Relasi: Kamar memiliki Banyak Fasilitas (N:M)
    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'room_facilities');
    }
}