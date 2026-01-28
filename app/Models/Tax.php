<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'rate',
        'type',
        'is_active',
        'description',
        'applicable_states'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rate' => 'decimal:2',
        'applicable_states' => 'array'
    ];
}