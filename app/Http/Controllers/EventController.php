<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::all();

            $formattedEvents = $events->map(function ($event) {
                $imagePaths = $event->images->pluck('image_path')->toArray();

                return [
                    'id' => $event->id,
                    'name' => $event->name,
                    'type' => $event->type,
                    'description' => $event->description,
                    'date' => $event->date,
                    'time' => $event->time,
                    'place' => $event->place,
                    'creator_firstname' => $event->creator_firstname,
                    'creator_lastname' => $event->creator_lastname,
                    'appreciation' => $event->appreciation,
                    'total_seat' => $event->total_seat,
                    'remain_seat' => $event->remain_seat,
                    'created_at' => $event->created_at,
                    'updated_at' => $event->updated_at,
                    'image_paths' => $imagePaths,
                    'event_code' => $event->event_code,
                    'latitude' => $event->latitude,
                    'longitude' => $event->longitude,
                ];
            });

            return response()->json(['data' => $formattedEvents], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|unique:events|max:255',
                'type' => 'required|string|max:100',
                'description' => 'required|string',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'place' => 'required|string|max:255',
                'appreciation' => 'nullable|numeric',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'total_seat' => 'required|numeric',
                'remain_seat' => 'required|numeric',
                'secret_key' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            $user = User::where('secret_key', $request->secret_key)->firstOrFail();

            $eventCode = $this->generateEventCode();

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
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ];

            DB::beginTransaction();

            $event = Event::create($eventData);

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

            DB::commit();

            return response()->json(['data' => $event, 'event_code' => $eventCode], 201);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['message' => 'User not found'], 404);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            $imagePaths = $event->images->pluck('image_path')->toArray();

            $formattedEvent = [
                'id' => $event->id,
                'name' => $event->name,
                'type' => $event->type,
                'description' => $event->description,
                'date' => $event->date,
                'time' => $event->time,
                'place' => $event->place,
                'creator_firstname' => $event->creator_firstname,
                'creator_lastname' => $event->creator_lastname,
                'appreciation' => $event->appreciation,
                'total_seat' => $event->total_seat,
                'remain_seat' => $event->remain_seat,
                'created_at' => $event->created_at,
                'updated_at' => $event->updated_at,
                'image_paths' => $imagePaths,
                'event_code' => $event->event_code,
                'latitude' => $event->latitude,
                'longitude' => $event->longitude,
            ];

            return response()->json(['data' => $formattedEvent]);
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
                'name' => 'required|string|unique:events,name,' . $id . '|max:255',
                'type' => 'required|string|max:100',
                'description' => 'required|string',
                'date' => 'required|date',
                'time' => 'required|date_format:H:i',
                'place' => 'required|string|max:255',
                'appreciation' => 'nullable|numeric',
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'total_seat' => 'required|numeric',
                'remain_seat' => 'required|numeric',
                'secret_key' => 'required|string',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
            ]);

            $event = Event::findOrFail($id);
            $event->update($request->only([
                'name', 'type', 'description', 'date', 'time',
                'place', 'appreciation', 'total_seat', 'remain_seat',
                'latitude', 'longitude',
            ]));

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
        return response()->json(['message' => 'An error occurred'], 500);
    }
}
