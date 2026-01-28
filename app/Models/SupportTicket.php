<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
        'assigned_to'
    ];

    protected $casts = [
        'priority' => 'string',
        'status' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

     public function replies()
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
        
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_id)) {
                $ticket->ticket_id = '#' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
        });
    }
}