<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'gender',
        'profile_picture',
        'is_admin',
        'user_type',
        'status',
        'locality',
        'google_id',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    protected $attributes = [
        'is_admin' => 0,
        'user_type' => 'passenger',
        'status' => 'active',
    ];

    // Gender constants for easy access
    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';
    const GENDER_OTHER = 'other';

  
    public static function getGenderOptions()
    {
        return [
            self::GENDER_MALE => 'Male',
            self::GENDER_FEMALE => 'Female',
            self::GENDER_OTHER => 'Other',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    public function scopeRegularUsers($query)
    {
        return $query->where('is_admin', false);
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
    // User.php
    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    // public function roles(): BelongsToMany
    // {
    //     return $this->belongsToMany(Role::class, 'admin_role', 'admin_id', 'role_id');
    // }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles()->where('slug', $role)->exists();
        }

        return $role->intersect($this->roles)->isNotEmpty();
    }

    public function hasPermission($permission): bool
    {
        return $this->roles()->whereHas('permissions', function ($query) use ($permission) {
            $query->where('slug', $permission);
        })->exists();
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('slug', $role)->firstOrFail();
        }

        $this->roles()->detach($role->id);
    }

    public function driverReviews()
    {
        return $this->hasMany(Review::class, 'driver_id');
    }

 
    public function passengerReviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'reviewed_by');
    }

   
    public function getDriverRatingAttribute()
    {
        return $this->driverReviews()->avg('rating') ?? 0;
    }

    
    public function getPassengerRatingAttribute()
    {
        return $this->passengerReviews()->avg('rating') ?? 0;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function rides()
    {
        return $this->hasManyThrough(Ride::class, Car::class, 'user_id', 'car_id', 'id', 'id');
    }
}