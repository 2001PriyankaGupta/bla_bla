<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'logo',
        'contact_email',
        'gst_percentage',
        'rounding_rules',
        'surcharge_configuration'
    ];

    protected $casts = [
        'surcharge_configuration' => 'array',
        'gst_percentage' => 'decimal:2'
    ];
}