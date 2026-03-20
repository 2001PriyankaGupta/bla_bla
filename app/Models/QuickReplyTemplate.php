<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickReplyTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'content',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->setTimezone(new \DateTimeZone(config('app.timezone', 'Asia/Kolkata')))->format('Y-m-d H:i:s');
    }

}