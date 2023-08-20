<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::all();
            return response()->json(['data' => $events]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:events|max:255', // Updated 'unique' rule

                'type' => 'required|string|max:100',
                'description' => 'required|string',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'place' => 'required|string|max:255',
                'appreciation' => 'nullable|numeric',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Multiple image validation
                'total_seat' => 'required|numeric',
                'remain_seat' => 'required|numeric',
                'secret_key' => 'required|string',
            ]);

            // Find the user based on the secret key
            $user = User::where('secret_key', $request->secret_key)->firstOrFail();

            // Generate a unique event code
            $eventCode = $this->generateEventCode();

            // Prepare event data to be inserted into the database
            $eventData = [
                'name' => $request->name,
                'type' => $request->type,
                'description' => $request->description,
                'date' => $request->date,
                'time' => $request->time,
                'place' => $request->place,
                'creator_firstname' => $user->firstname,
                'creator_lastname' => $user->lastname,
                'appreciation' => $request->appreciation,
                'total_seat' => $request->total_seat,
                'remain_seat' => $request->remain_seat,
                'creator_id' => $user->id,
                'event_code' => $eventCode,
            ];

            // Start a database transaction
            DB::beginTransaction();

            // Create the event record in the database
            $event = Event::create($eventData);

            // Store event images
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $imagePath = 'event_images/' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public', $imagePath);
                    EventImage::create([
                        'event_id' => $event->id,
                        'event_name' => $event->name,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            // Commit the transaction
            DB::commit();

            // Return the event details including the generated event code
            return response()->json(['data' => $event, 'event_code' => $eventCode], 201);
        } catch (ValidationException $e) {
            // Rollback the transaction on validation error
            DB::rollback();
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            // Rollback the transaction on user not found
            DB::rollback();
            return response()->json(['message' => 'User not found'], 404);
        } catch (\Exception $e) {
            // Rollback the transaction on any other exception
            DB::rollback();
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            return response()->json(['data' => $event]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'unique:events', 'max:255'], // Updated 'unique' rule

                'type' => 'required|string|max:100',
                'description' => 'required|string',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'place' => 'required|string|max:255',
                'appreciation' => 'numeric',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Multiple image validation
                'total_seat' => 'required|numeric',
                'remain_seat' => 'required|numeric',
                'secret_key' => 'required|string',
            ]);

            $event = Event::findOrFail($id);
            $event->update($request->only([
                'name', 'type', 'description', 'date', 'time',
                'place', 'appreciation', 'total_seat', 'remain_seat'
            ]));

            // Store event images
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                foreach ($images as $image) {
                    $imagePath = 'event_images/' . uniqid() . '.' . $image->getClientOriginalExtension();
                    $image->storeAs('public', $imagePath);
                    EventImage::create([
                        'event_id' => $event->id,
                        'event_name' => $event->name,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            return response()->json(['data' => $event], 200);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();

            return response()->json(['message' => 'Event deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function generateEventCode()
    {
        return 'EVENT_' . uniqid() . '_AFRILINK';
    }

    protected function handleException(\Exception $exception)
    {
        // Log the exception here if needed

        return response()->json(['message' => 'An error occurred'], 500);
    }
}
