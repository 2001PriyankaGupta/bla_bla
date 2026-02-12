<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'user_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'meta',
        'payment_date'
    ];

    protected $casts = [
        'meta' => 'array',
        'payment_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
