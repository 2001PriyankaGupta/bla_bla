<?php
// app/Models/FareConfiguration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FareConfiguration extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_fare',
        'per_km_charge',
        'waiting_fee',
        'home_pickup_fee',
        'night_holiday_surcharge',
        'is_active'
    ];

    protected $casts = [
        'base_fare' => 'decimal:2',
        'per_km_charge' => 'decimal:2',
        'waiting_fee' => 'decimal:2',
        'home_pickup_fee' => 'decimal:2',
        'night_holiday_surcharge' => 'decimal:2',
        'is_active' => 'boolean'
    ];
}