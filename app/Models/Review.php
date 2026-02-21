<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'type',
        'rating',
        'comment',
        'driver_id',
        'user_id',
        'reviewed_by'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    /**
     * Relationship with booking
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Relationship with driver
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Relationship with user (passenger)
     */
    public function passenger()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with reviewer
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope for driver reviews
     */
    public function scopeDriverReviews($query)
    {
        return $query->where('type', 'driver');
    }

    /**
     * Scope for passenger reviews
     */
    public function scopePassengerReviews($query)
    {
        return $query->where('type', 'passenger');
    }
}