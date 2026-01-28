<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'user_id',
        'seats_booked',
        'total_price',
        'status',
        'special_requests',
        'approved_at',
        'rejected_at',
        'rejection_reason'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'total_price' => 'decimal:2'
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->hasOneThrough(
            User::class,
            Ride::class,
            'id', // Foreign key on rides table
            'id', // Foreign key on users table
            'ride_id', // Local key on bookings table
            'car_id' // Local key on rides table
        )->join('cars', 'rides.car_id', '=', 'cars.id')
         ->select('users.*');
    }

    // Scope for active bookings
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    // Scope for completed bookings
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}