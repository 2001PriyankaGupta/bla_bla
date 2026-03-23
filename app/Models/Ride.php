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

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone(config('app.timezone', 'Asia/Kolkata')))->format('Y-m-d H:i:s');
    }

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

    public function stopPoints()
    {
        return $this->hasMany(StopPoint::class);
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
     * Get the full list of cities in the ride including pickup, stops and drop
     */
    public function getFullRoute()
    {
        $stops = $this->stopPoints()->orderBy('sequence', 'asc')->get();
        
        $route = [];
        $route[] = ['name' => $this->pickup_point, 'price' => 0, 'sequence' => 0];
        
        foreach ($stops as $stop) {
            $route[] = ['name' => $stop->city_name, 'price' => $stop->price_from_pickup, 'sequence' => $stop->sequence + 1];
        }
        
        $route[] = ['name' => $this->drop_point, 'price' => $this->price_per_seat, 'sequence' => count($route)];
        
        return $route;
    }

    /**
     * Calculate available seats for a specific segment
     */
    public function availableSeats($fromCity = null, $toCity = null)
    {
        $route = $this->getFullRoute();
        $cityOrder = array_column($route, 'name');
        
        $startIndex = $fromCity ? array_search($fromCity, $cityOrder) : 0;
        $endIndex = $toCity ? array_search($toCity, $cityOrder) : count($route) - 1;

        if ($startIndex === false) $startIndex = 0;
        if ($endIndex === false) $endIndex = count($route) - 1;

        // Number of segments in requested trip
        $numSegments = count($route) - 1;
        $segmentOccupancy = array_fill(0, $numSegments, 0);

        $bookings = $this->bookings()->whereIn('status', ['pending', 'confirmed'])->get();

        // Normalize city names for comparison
        $normalize = function($name) {
            return strtolower(trim(explode(',', $name)[0]));
        };
        
        $normalizedCityOrder = array_map($normalize, $cityOrder);

        foreach ($bookings as $booking) {
            $bStartName = $normalize($booking->pickup_point);
            $bEndName = $normalize($booking->drop_point);
            
            $bStart = array_search($bStartName, $normalizedCityOrder);
            $bEnd = array_search($bEndName, $normalizedCityOrder);

            // Fallback for safety
            if ($bStart === false) $bStart = 0;
            if ($bEnd === false) $bEnd = count($route) - 1;

            // Mark occupancy for all segments this booking covers
            for ($i = $bStart; $i < $bEnd; $i++) {
                if (isset($segmentOccupancy[$i])) {
                    $segmentOccupancy[$i] += $booking->seats_booked;
                }
            }
        }

        // Available seats for requested trip is total_seats - MAX(occupancy across all its segments)
        $maxOccupancy = 0;
        for ($i = $startIndex; $i < $endIndex; $i++) {
            if (isset($segmentOccupancy[$i])) {
                $maxOccupancy = max($maxOccupancy, $segmentOccupancy[$i]);
            }
        }

        return max(0, $this->total_seats - $maxOccupancy);
    }

    /**
     * Check if ride is full for a specific segment
     */
    public function isFull($fromCity = null, $toCity = null)
    {
        return $this->availableSeats($fromCity, $toCity) <= 0;
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