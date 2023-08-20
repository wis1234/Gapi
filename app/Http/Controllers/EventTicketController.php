<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use PHPUnit\Framework\Attributes\Ticket;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventTicketController extends Controller
{

    // ...
    
    public function store(Request $request)
    {
        $request->validate([
            'event_code' => ['required', 'exists:events,event_code'],
            'secret_key' => ['required', 'exists:users,secret_key'],
            'name' => ['required', 'string', 'unique:events_tickets', 'max:255'], // Updated 'unique' rule
            'type' => ['required', 'string', 'max:255'],
            'price' => ['integer', 'min:0'],
            'total' => ['integer', 'min:1'],
        ]);
    
        $eventCode = $request->input('event_code');
        $secretKey = $request->input('secret_key');
    
        $event = Event::where('event_code', $eventCode)->first();
        $user = User::where('secret_key', $secretKey)->first();
    
        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        $ticket = new EventTicket();
        $ticket->name = $request->input('name');
        $ticket->type = $request->input('type');
        $ticket->price = $request->input('price');
        $ticket->total = $request->input('total');
        $ticket->solded = 0;
        $ticket->available = $request->input('total');
        $ticket->event_name = $event->name;
        $ticket->creator_firstname = $user->firstname;
        $ticket->creator_lastname = $user->lastname;
    
        $ticket->set_date = $request->input('set_date'); // 
        $ticket->ticket_n° = 'TICKET_' . uniqid() . '_AFRILINK'; // Corrected field name
        $ticket->save();
    
        return response()->json(['message' => 'Ticket created successfully.', 'ticket' => $ticket]);
    }
    

    public function index()
    {
        $tickets = EventTicket::all();
        return response()->json($tickets);
    }

    public function show($id)
    {
        try {
            $ticket = EventTicket::findOrFail($id);
            return response()->json($ticket);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'total' => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $ticket = EventTicket::findOrFail($id);
            $ticket->name = $request->input('name');
            $ticket->type = $request->input('type');
            $ticket->price = $request->input('price');
            $ticket->total = $request->input('total');
            // ... Update other fields as needed ...
            $ticket->save();

            return response()->json(['message' => 'Ticket updated successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = EventTicket::findOrFail($id);
            $ticket->delete();

            return response()->json(['message' => 'Ticket deleted successfully.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }
    }
}
