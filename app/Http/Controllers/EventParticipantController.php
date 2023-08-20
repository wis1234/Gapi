<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventParticipant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventParticipantController extends Controller
{
    public function index()
    {
        try {
            $participants = EventParticipant::all();
            return response()->json($participants);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'secret_key' => 'required|string',
                'event_code' => 'required|string',
                'opinion' => 'nullable|string',
            ]);

            // Find the user based on the secret key
            $user = User::where('secret_key', $request->secret_key)->firstOrFail();

            // Find the event based on the event code
            $event = Event::where('event_code', $request->event_code)->firstOrFail();

            // Create the event participant record in the database
            $participant = EventParticipant::create([
                'user_id' => $user->id, // Automatically generated
                'event_code' => $event->event_code,
                'opinion' => $request->opinion,
            ]);

            return response()->json($participant, 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User, Event, or Event Code not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $participant = EventParticipant::findOrFail($id);
            return response()->json($participant);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Participant not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $participant = EventParticipant::findOrFail($id);

            $request->validate([
                'secret_key' => 'required|string',
                'event_code' => 'required|string',
                'opinion' => 'nullable|string',
            ]);

            // Find the user based on the secret key
            $user = User::where('secret_key', $request->secret_key)->firstOrFail();

            // Find the event based on the event code
            $event = Event::where('event_code', $request->event_code)->firstOrFail();

            $participant->update([
                'user_id' => $user->id, // Automatically generated
                'event_code' => $event->event_code,
                'opinion' => $request->opinion,
            ]);

            return response()->json($participant);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Participant not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $participant = EventParticipant::findOrFail($id);
            $participant->delete();

            return response()->json(['message' => 'Participant deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Participant not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $exception)
    {
        // Log the exception here if needed

        return response()->json(['message' => 'An error occurred'], 500);
    }
}
