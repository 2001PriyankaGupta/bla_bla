<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'pickup_point',
        'drop_point',
        'date_time',
        'total_seats',
        'price_per_seat',
        'car_id',
        'luggage_allowed',
        'status'
    ];

    protected $casts = [
        'date_time' => 'datetime',
        'luggage_allowed' => 'boolean',
        'status' => 'string'
    ];

    /**
     * Get the car for this ride
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Get the driver through car
     */
    public function driver()
    {
        return $this->hasOneThrough(
            User::class,           // Final model
            Car::class,            // Intermediate model
            'id',                  // Foreign key on cars table
            'id',                  // Foreign key on users table
            'car_id',              // Local key on rides table
            'user_id'              // Local key on cars table
        );
    }

    /**
     * Get bookings for this ride
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all passengers (users) through bookings
     */
    public function passengers()
    {
        return $this->hasManyThrough(
            User::class,           // Final model
            Booking::class,        // Intermediate model
            'ride_id',             // Foreign key on bookings table
            'id',                  // Foreign key on users table
            'id',                  // Local key on rides table
            'user_id'              // Local key on bookings table
        );
    }

    /**
     * Get first passenger (for backward compatibility)
     */
    public function passenger()
    {
        return $this->bookings()->first()->user ?? null;
    }

    /**
     * Get messages for this ride
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Calculate available seats
     */
    public function availableSeats()
    {
        $bookedSeats = $this->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->sum('seats_booked');
        
        return $this->total_seats - $bookedSeats;
    }

    /**
     * Check if ride is full
     */
    public function isFull()
    {
        return $this->availableSeats() <= 0;
    }

    /**
     * Scope for active rides
     */
    public function scopeActive($query)
    {
        return $query->where('date_time', '>=', now());
    }

    /**
     * Scope for completed rides
     */
    public function scopeCompleted($query)
    {
        return $query->where('date_time', '<', now());
    }
}