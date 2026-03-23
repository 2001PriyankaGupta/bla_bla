<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ride;

class StopPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'city_name',
        'price_from_pickup',
        'sequence'
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone(config('app.timezone', 'Asia/Kolkata')))->format('Y-m-d H:i:s');
    }
}
