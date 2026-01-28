<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'car_make',
        'car_model',
        'car_year',
        'car_color',
        'licence_plate',
        'car_photo',
        'driver_license_front',
        'driver_license_back',
        'license_verified',
        'verification_notes',
        'verified_by',
        'verified_at'
    ];

    protected $casts = [
        'car_year' => 'integer',
        'verified_at' => 'datetime',
    ];

    // Scope for verification status
    public function scopePending($query)
    {
        return $query->where('license_verified', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('license_verified', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('license_verified', 'rejected');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper methods
    public function isVerified()
    {
        return $this->license_verified === 'verified';
    }

    public function isPending()
    {
        return $this->license_verified === 'pending';
    }

    public function isRejected()
    {
        return $this->license_verified === 'rejected';
    }
     public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    
}