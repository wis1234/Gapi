<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\HotelSelf;
use App\Models\HotelImage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::all();
        return response()->json(['data' => $hotels], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'hotel_code' => 'required|string',
            'room_type' => 'required|string|max:255',
            'room_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        // Find the corresponding hotel data in the HotelSelf model
        $hotelSelf = HotelSelf::where('hotel_code', $request->input('hotel_code'))->first();

        if (!$hotelSelf) {
            return response()->json(['error' => 'Hotel code not found in hotel_self table.'], Response::HTTP_BAD_REQUEST);
        }

        // Fetch the hotel_id from HotelSelf
        $hotel_id = $hotelSelf->id;

        // Automatically fill hotel_name, hotel_id, and other fields
        $request->merge([
            'hotel_name' => $hotelSelf->name,
            'hotel_id' => $hotel_id,
            'hotel_address' => $hotelSelf->address,
        ]);

        // Initialize an empty array for image paths
        $uploadedImages = [];

        // Handle multiple images and store their paths in the $uploadedImages array
        foreach ($request->file('images') as $image) {
            if ($image->isValid()) {
                $imagePath = $image->store('hotel_images', 'public');
                $uploadedImages[] = $imagePath;
            }
        }

        // Create the hotel record with data from the request
        $hotel = new Hotel();
        $hotel->fill($request->all());
        $hotel->image_path = implode(',', $uploadedImages);
        $hotel->save();

        // Exclude 'num_roomavail' from the JSON response
        return response()->json(['message' => 'Hotel created successfully.', 'data' => $hotel], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $hotel = Hotel::findOrFail($id);
        return response()->json(['data' => $hotel], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'hotel_code' => 'required|string',
            'state' => 'required|string|max:255',
            'num_roomavail' => 'required|integer|min:1',
            'room_type' => 'required|string|max:255',
            'room_price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $hotelSelf = HotelSelf::where('hotel_code', $request->input('hotel_code'))->first();

        if (!$hotelSelf) {
            return response()->json(['error' => 'Hotel code not found in hotel_self table.'], Response::HTTP_BAD_REQUEST);
        }

        $hotelData = $request->except('images');
        $hotelData['hotel_name'] = $hotelSelf->name;
        $hotelData['hotel_address'] = $hotelSelf->address;

        $hotel = Hotel::findOrFail($id);
        $hotel->update($hotelData);

        $hotel->images()->delete(); // Delete existing images

        foreach ($request->file('images') as $image) {
            $imagePath = $image->store('hotel_images', 'public');

            HotelImage::create([
                'hotel_id' => $hotel->id,
                'image_path' => $imagePath,
            ]);
        }

        return response()->json(['data' => $hotel], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $hotel = Hotel::findOrFail($id);
        // $hotel->images()->delete(); // Delete associated images
        $hotel->delete();

        return response()->json(['message' => 'Hotel deleted successfully.'], Response::HTTP_OK);
    }
}
