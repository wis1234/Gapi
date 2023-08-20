<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelImage;
use App\Models\Hotel;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class HotelImageController extends Controller
{
    public function index()
    {
        try {
            $images = HotelImage::all();
            return response()->json(['data' => $images], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the images.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required|exists:hotel_self,id',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $imagePath = $request->file('image')->store('hotel_images', 'public');

            $hotel = Hotel::findOrFail($request->input('hotel_id'));

            $imageData = [
                'hotel_id' => $request->input('hotel_id'),
                'hotel_name' => $hotel->name,
                'image_path' => $imagePath,
            ];

            $image = HotelImage::create($imageData);

            return response()->json(['message' => 'Image uploaded successfully.', 'data' => $image], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while uploading the image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $image = HotelImage::findOrFail($id);
            return response()->json(['data' => $image], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Image not found.'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'hotel_id' => 'required|exists:hotel_self,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $image = HotelImage::findOrFail($id);
            $image->update([
                'hotel_id' => $request->input('hotel_id'),
                'hotel_name' => $image->hotel->name,
            ]);

            return response()->json(['data' => $image], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $image = HotelImage::findOrFail($id);
            $image->delete();

            return response()->json(['message' => 'Image deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the image.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

