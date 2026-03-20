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

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone(config('app.timezone', 'Asia/Kolkata')))->format('Y-m-d H:i:s');
    }

}