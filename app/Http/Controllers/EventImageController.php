<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class EventImageController extends Controller
{
    public function index()
    {
        $images = EventImage::all();
    
        $formattedImages = $images->map(function ($image) {
            return [
                'id' => $image->id,
                'event_id' => $image->event_id,
                'event_name' => $image->event_name,
                'image_path' => $image->image_path,
            ];
        });
    
        return response()->json(['data' => $formattedImages], Response::HTTP_OK);
    }
    

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_code' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $event = Event::where('event_code', $request->input('event_code'))->first();

        if (!$event) {
            return response()->json(['error' => 'Event code not found.'], Response::HTTP_BAD_REQUEST);
        }

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            if ($image->isValid()) {
                $imagePath = $image->store('event_images', 'http://127.0.0.1:8000/storage/event_images');
                $uploadedImages[] = $imagePath;
            }
        }

        foreach ($uploadedImages as $imagePath) {
            EventImage::create([
                'event_id' => $event->id,
                'image_path' => $imagePath,
            ]);
        }

        return response()->json(['message' => 'Event images uploaded successfully.'], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        $images = $event->images;
        return response()->json(['data' => $images], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'event_code' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $event = Event::where('event_code', $request->input('event_code'))->first();

        if (!$event) {
            return response()->json(['error' => 'Event code not found.'], Response::HTTP_BAD_REQUEST);
        }

        $event->images()->delete(); // Delete existing images

        $uploadedImages = [];

        foreach ($request->file('images') as $image) {
            if ($image->isValid()) {
                $imagePath = $image->store('event_images', 'public');
                $uploadedImages[] = $imagePath;
            }
        }

        foreach ($uploadedImages as $imagePath) {
            EventImage::create([
                'event_id' => $event->id,
                'image_path' => $imagePath,
            ]);
        }

        return response()->json(['message' => 'Event images updated successfully.'], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->images()->delete(); // Delete associated images
        return response()->json(['message' => 'Event and its images deleted successfully.'], Response::HTTP_OK);
    }
}
