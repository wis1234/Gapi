<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventImageController extends Controller
{
    public function index($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $images = $event->images;
            return response()->json(['data' => $images]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request, $eventId)
    {
        try {
            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Multiple image validation
            ]);

            $event = Event::findOrFail($eventId);

            foreach ($request->file('images') as $image) {
                $imagePath = 'event_images/' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->storeAs('public', $imagePath);
                EventImage::create([
                    'event_id' => $event->id,
                    'event_name' => $event->name,
                    'image_path' => $imagePath,
                ]);
            }

            return response()->json(['message' => 'Images uploaded successfully'], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Event not found'], 404);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($eventId, $imageId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $image = EventImage::findOrFail($imageId);

            // Delete the image file from storage
            Storage::delete('public/' . $image->image_path);

            $image->delete();

            return response()->json(['message' => 'Image deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Event or image not found'], 404);
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
