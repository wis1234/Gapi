<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelTicket;

class HotelTicketController extends Controller
{
    public function index()
    {
        $tickets = HotelTicket::all();
        return response()->json($tickets);
    }

    public function show($id)
    {
        $ticket = HotelTicket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Hotel ticket not found'], 404);
        }
        return response()->json($ticket);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'name' => 'required',
            'type' => 'required',
            'price' => 'required|numeric',
            'total' => 'required|numeric',
            'solded' => 'required|numeric',
            'available' => 'required|numeric',
            'buyer_id' => 'required|exists:users,id',
            'purchase_date' => 'required|date',
            'status' => 'required',
        ]);

        $ticket = HotelTicket::create($request->all());
        return response()->json($ticket, 201);
    }

    public function update(Request $request, $id)
    {
        $ticket = HotelTicket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Hotel ticket not found'], 404);
        }

        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'name' => 'required',
            'type' => 'required',
            'price' => 'required|numeric',
            'total' => 'required|numeric',
            'solded' => 'required|numeric',
            'available' => 'required|numeric',
            'buyer_id' => 'required|exists:users,id',
            'purchase_date' => 'required|date',
            'status' => 'required',
        ]);

        $ticket->update($request->all());
        return response()->json($ticket, 200);
    }

    public function destroy($id)
    {
        $ticket = HotelTicket::find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Hotel ticket not found'], 404);
        }

        $ticket->delete();
        return response()->json(['message' => 'Hotel ticket deleted'], 200);
    }
}
