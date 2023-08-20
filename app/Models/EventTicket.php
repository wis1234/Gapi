<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTicket extends Model
{
    use HasFactory;
    protected $table = 'events_tickets';

    protected $fillable = [
        'name',
        'type',
        'price',
        'total',
        'solded',
        'available',
        'event_name',
        'creator_firstname',
        'creator_lastname',
        'expiration_date',
        'ticket_n°',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function bookedTickets()
    {
        return $this->hasMany(BookTicket::class, 'ticket_name', 'ticket_name');
    }
}
