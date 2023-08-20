<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Don't forget to import the User model

class BookTicket extends Model
{ 
    protected $table = 'book_ticket';
    protected $fillable = [
        'ticket_type',
        'ticket_name',
        'event_name',
        'ticket_code',
        'price',
        'validity',
        'secret_key',
        'owner_firstname',
        'owner_lastname',
        'owner_phone',
        'owner_email',
        'owner_photo',
    ];

    public function eventTicket()
    {
        return $this->belongsTo(EventTicket::class, 'ticket_name', 'ticket_name');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bookTicket) {
            // Fetch owner information from the User model
            $user = User::where('secret_key', $bookTicket->secret_key)->first();

            if ($user) {
                $bookTicket->owner_firstname = $user->firstname;
                $bookTicket->owner_lastname = $user->lastname;
                $bookTicket->owner_phone = $user->phone;
                $bookTicket->owner_email = $user->email;
                $bookTicket->owner_photo = $user->photo;
            }
        });
    }
}
