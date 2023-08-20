<?php

namespace App\Observers;

use App\Models\User;
use App\Models\BookTicket;
use App\Models\EventTicket;

class BookTicketObserver
{
    public function creating(BookTicket $bookTicket)
    {
        // Fetch owner information from the User model
        $user = User::where('secret_key', $bookTicket->secret_key)->first();

        if ($user) {
            $bookTicket->owner_firstname = $user->firstname;
            $bookTicket->owner_lastname = $user->lastname;
            $bookTicket->owner_phone = $user->phone;
            $bookTicket->owner_email = $user->email;
            $bookTicket->owner_photo = $user->photo;
        }

// Fetch ticket information from the EventTicket model
$ticketInfo = EventTicket::where('name', $bookTicket->ticket_name)->first();

if ($ticketInfo) {
    $bookTicket->ticket_type = $ticketInfo->type;
    $bookTicket->price = $ticketInfo->price;
    $bookTicket->event_name = $ticketInfo->event_name; // Automatically fill event_name
}

// Generate ticket code
$ticketCode = $ticketInfo->ticket_n° . '_' . $bookTicket->owner_email;
$bookTicket->ticket_code = $ticketCode;
    }
}
