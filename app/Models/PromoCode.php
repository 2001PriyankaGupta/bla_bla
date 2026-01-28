<?php
// app/Models/PromoCode.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'promo_code',
        'type',
        'discount_value',
        'usage_limit',
        'used_count',
        'expiry_date',
        'is_active'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'expiry_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Check if promo code is valid
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expiry_date < Carbon::today()) {
            return false;
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    // Calculate discount amount
    public function calculateDiscount($totalAmount)
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($this->type === 'percentage') {
            return ($totalAmount * $this->discount_value) / 100;
        }

        return min($this->discount_value, $totalAmount);
    }

    // Increment usage count
    public function incrementUsage()
    {
        $this->increment('used_count');
    }
}