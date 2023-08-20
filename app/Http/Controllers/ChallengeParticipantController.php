<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChallengeParticipant;
use App\Models\Event;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Exception;

class ChallengeParticipantController extends Controller
{
    public function index()
    {
        try {
            $challengeParticipants = ChallengeParticipant::all();
            return response()->json(['data' => $challengeParticipants], 200);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = $request->validate([
                'secret_key' => 'required|exists:users,secret_key',
                'event_code' => 'required|exists:events,event_code',
                'challenge_code' => 'required|exists:challenges,challenge_code',
                'appreciation' => 'nullable|string',
                'result' => 'nullable|string',
                'opinion' => 'nullable|string',
            ]);

            $user = User::where('secret_key', $validator['secret_key'])->first();
            $event = Event::where('event_code', $validator['event_code'])->first();
            $challenge = Challenge::where('challenge_code', $validator['challenge_code'])->first();

            if (!$user || !$event || !$challenge) {
                return response()->json(['message' => 'User, event, or challenge not found.'], 404);
            }

            $data = [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'challenge_id' => $challenge->id,
                'event_name' => $event->name,
                'participant_firstname' => $user->firstname,
                'participant_lastname' => $user->lastname,
                'challenge_name' => $challenge->name,
                'appreciation' => $validator['appreciation'],
                'result' => $validator['result'],
                'opinion' => $validator['opinion'],
            ];

            $challengeParticipant = ChallengeParticipant::create($data);
            return response()->json(['message' => 'Challenge participant created successfully', 'data' => $challengeParticipant], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $challengeParticipant = ChallengeParticipant::findOrFail($id);
            return response()->json(['data' => $challengeParticipant], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge participant not found'], 404);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $challengeParticipant = ChallengeParticipant::findOrFail($id);

            $validator = $request->validate([
                'secret_key' => 'required|exists:users,secret_key',
                'event_code' => 'required|exists:events,event_code',
                'challenge_code' => 'required|exists:challenges,challenge_code',
                'appreciation' => 'nullable|string',
                'result' => 'nullable|string',
                'opinion' => 'nullable|string',
            ]);

            $user = User::where('secret_key', $validator['secret_key'])->first();
            $event = Event::where('event_code', $validator['event_code'])->first();
            $challenge = Challenge::where('challenge_code', $validator['challenge_code'])->first();

            if (!$user || !$event || !$challenge) {
                return response()->json(['message' => 'User, event, or challenge not found.'], 404);
            }

            $data = [
                'user_id' => $user->id,
                'event_id' => $event->id,
                'challenge_id' => $challenge->id,
                'event_name' => $event->name,
                'participant_firstname' => $user->firstname,
                'participant_lastname' => $user->lastname,
                'challenge_name' => $challenge->name,
                'appreciation' => $validator['appreciation'],
                'result' => $validator['result'],
                'opinion' => $validator['opinion'],
            ];

            $challengeParticipant->update($data);
            return response()->json(['message' => 'Challenge participant updated successfully', 'data' => $challengeParticipant], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge participant not found'], 404);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $challengeParticipant = ChallengeParticipant::findOrFail($id);
            $challengeParticipant->delete();

            return response()->json(['message' => 'Challenge participant deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Challenge participant not found'], 404);
        } catch (Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(Exception $e)
    {
        // Log the exception here if needed

        return response()->json(['message' => 'An error occurred'], 500);
    }
}
