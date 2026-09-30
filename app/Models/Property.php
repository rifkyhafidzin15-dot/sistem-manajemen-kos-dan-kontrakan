<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'acquisition_date',
        'initial_investment',
        'base_currency',
        'status',
    ];

    // Relasi: 1 Properti memiliki Banyak Kamar (1:N)
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
}
