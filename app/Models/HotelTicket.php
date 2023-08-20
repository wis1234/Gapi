<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelTicket extends Model

{
    protected $table = "hotels_tickets";
    protected $fillable = [
        'hotel_id',
        'name',
        'type',
        'price',
        'total',
        'solded',
        'available',
        'buyer_id',
        'purchase_date',
        'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
