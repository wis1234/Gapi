<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\BookTicket;
use App\Models\EventTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Observers\BookTicketObserver; // Import the observer

class BookTicketController extends Controller
{
    public function __construct()
    {
        BookTicket::observe(BookTicketObserver::class); // Attach the observer globally
    }

    public function index()
    {
        $bookTickets = BookTicket::all();
        return response()->json(['data' => $bookTickets]);
    }

    public function create()
    {
        $eventTickets = EventTicket::pluck('ticket_name', 'ticket_name');
        return response()->json(['data' => $eventTickets]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_name' => 'required',
            'secret_key' => 'required|exists:users,secret_key',
            // Add other validation rules here
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('secret_key', $request->input('secret_key'))->first();

        $agenceData = $request->except(['secret_key']);
        $agenceData['user_id'] = $user->id;

        // Set owner information
        $agenceData['owner_firstname'] = $user->firstname;
        $agenceData['owner_lastname'] = $user->lastname;
        $agenceData['owner_phone'] = $user->phone;
        $agenceData['owner_email'] = $user->email;
        $agenceData['owner_photo'] = $user->photo;

        $bookTicket = new BookTicket($agenceData);
        $bookTicket->save();

        $response = $bookTicket->toArray();
        unset($response['secret_key']);

        return response()->json(['message' => 'Book Ticket created successfully', 'info' => $response], JsonResponse::HTTP_CREATED);
    }

    public function show($id)
    {
        $bookTicket = BookTicket::find($id);
    
        if (!$bookTicket) {
            return response()->json(['error' => 'Book Ticket not found.'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $response = $bookTicket->toArray();
        unset($response['secret_key']);
    
        return response()->json(['data' => $response]);
    }
    

    public function update(Request $request, BookTicket $bookTicket)
    {
        $validator = Validator::make($request->all(), [
            'ticket_name' => 'required',
            'secret_key' => 'required|exists:users,secret_key',
            // more validation rules here
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = User::where('secret_key', $request->input('secret_key'))->first();

        $agenceData = $request->except(['secret_key']);
        $agenceData['user_id'] = $user->id;

        // Set owner information
        $agenceData['owner_firstname'] = $user->firstname;
        $agenceData['owner_lastname'] = $user->lastname;
        $agenceData['owner_phone'] = $user->phone;
        $agenceData['owner_email'] = $user->email;
        $agenceData['owner_photo'] = $user->photo;

        $bookTicket->update($agenceData);

        $response = $bookTicket->toArray();
        unset($response['secret_key']);

        return response()->json(['message' => 'Book Ticket updated successfully', 'info' => $response]);
    }

    public function destroy(BookTicket $bookTicket)
    {
        $bookTicket->delete();

        return response()->json(['message' => 'Book Ticket deleted successfully']);
    }
}
