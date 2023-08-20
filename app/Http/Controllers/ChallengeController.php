<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Challenge;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class ChallengeController extends Controller
{
    public function index()
    {
        try {
            $challenges = Challenge::all();
            return response()->json(['data' => $challenges], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'delay' => 'required|string',
                'awards' => 'nullable|string',
                'starts_at' => 'required|date',
                'ends_at' => 'required|date|after:starts_at',
                'appreciation' => 'nullable|string',
                'event_code' => 'required|exists:events,event_code',
                'secret_key' => 'required|exists:users,secret_key',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $event = Event::where('event_code', $request->input('event_code'))->first();
            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$event || !$user) {
                return response()->json(['message' => 'Event or user not found.'], 404);
            }

            $challenge = new Challenge($request->except(['event_code', 'secret_key']));
            $challenge->event_id = $event->id;
            $challenge->event_name = $event->name;
            $challenge->creator_id = $user->id;
            $challenge->creator_firstname = $user->firstname;   
            $challenge->creator_lastname = $user->lastname;
            $challenge->challenge_code = $this->generateUniqueChallengeCode(); // Generate challenge code
            $challenge->save();

            return response()->json(['message' => 'Challenge created successfully', 'data' => $challenge], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            return response()->json(['data' => $challenge], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge not found'], 404);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $challenge = Challenge::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'delay' => 'required|string',
                'awards' => 'nullable|string',
                'starts_at' => 'required|date',
                'ends_at' => 'required|date|after:starts_at',
                'appreciation' => 'nullable|string',
                'event_code' => 'required|exists:events,event_code',
                'secret_key' => 'required|exists:users,secret_key',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $event = Event::where('event_code', $request->input('event_code'))->first();
            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$event || !$user) {
                return response()->json(['message' => 'Event or user not found.'], 404);
            }

            $challenge->fill($request->except(['event_code', 'secret_key']));
            $challenge->event_id = $event->id;
            $challenge->creator_id = $user->id;
            $challenge->save();

            return response()->json(['message' => 'Challenge updated successfully', 'data' => $challenge], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge not found'], 404);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $challenge = Challenge::findOrFail($id);
            $challenge->delete();

            return response()->json(['message' => 'Challenge deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge not found'], 404);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function generateUniqueChallengeCode()
    {
        // return Str::random(10); // Generate a random unique challenge code (adjust length as needed)

        return 'CHAL_' . uniqid() .'_AFRILINK';
    }

    protected function handleException(Exception $e)
    {
        // Log the exception here if needed

        return response()->json(['message' => 'An error occurred'], 500);
    }
}
