<?php

// app/Http/Controllers/HotelSelfController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Hotel;
use App\Models\HotelSelf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB; // Import DB

class HotelSelfController extends Controller
{
    public function index()
    {
        try {
            $hotels = HotelSelf::all();
    
            // Calculate the lowest room price from the hotels table
            foreach ($hotels as $hotel) {
                $lowestPrice = Hotel::where('hotel_name', $hotel->name)->min('room_price');
                $hotel->low_price = $lowestPrice;
            }
    
            return response()->json($hotels);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret_key' => 'required|string|exists:users,secret_key',
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'website' => 'string|max:255',
            'latitude' => 'nullable|numeric', // Add latitude to validation rules
            'longitude' => 'nullable|numeric', // Add longitude to validation rules
            'hotel_self_images' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('secret_key', $request->input('secret_key'))->first();

        // Generate the hotel_code
        $hotelCode = 'HOTEL_' . uniqid() . '_AFRILINK';

        // Handle image upload
        $imagePath = "uploads/hotels/1695147237_bg-01.jpg"; // initialize the default image path
        if ($request->hasFile('hotel_self_images')) {
            $image = $request->file('hotel_self_images');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('uploads/hotels', $imageName, 'public');
        }

        // Create the hotel_self record with data from the request
        $hotelData = $request->except(['secret_key', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email', 'hotel_self_images']);
        $hotelData['manager_firstname'] = $user->firstname;
        $hotelData['manager_lastname'] = $user->lastname;
        $hotelData['manager_phone'] = $user->phone;
        $hotelData['manager_email'] = $user->email;
        $hotelData['user_id'] = $user->id;
        $hotelData['hotel_code'] = $hotelCode; // Assign the generated hotel_code
        $hotelData['hotel_self_images'] = $imagePath; // Assign the image path to the database field

        // Create the hotel_self record with latitude and longitude
        $hotelData['latitude'] = $request->input('latitude');
        $hotelData['longitude'] = $request->input('longitude');

        $hotel = HotelSelf::create($hotelData);

        // Calculate the lowest room price from the hotels table for this hotel
        $lowestPrice = Hotel::where('hotel_name', $hotel->name)->min('room_price');

        // Update the low_price field in the hotel_self record
        $hotel->low_price = $lowestPrice;
        $hotel->save();

        // Create a response array with hotel data and lowest room price
        $response = [
            'hotel' => $hotel,
            'lowest_room_price' => $lowestPrice,
        ];

        return response()->json($response, Response::HTTP_CREATED);
    }

    public function show($id)
    {
        try {
            $hotel = HotelSelf::findOrFail($id);

            // Calculate the lowest room price from the hotels table
            $lowestPrice = Hotel::where('hotel_name', $hotel->name)->min('room_price');
            $hotel->low_price = $lowestPrice;

            return response()->json($hotel);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'secret_key' => 'required|string|exists:users,secret_key',
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                // 'image' => 'required|string|max:255',
                'hotel_self_images' => 'image|mimes:jpeg,png,jpg|max:2048',
                'low_price' => 'required|integer|max:255',
                'website' => 'string|max:255',
                'latitude' => 'nullable|numeric', // Add latitude to validation rules
                'longitude' => 'nullable|numeric', // Add longitude to validation rules
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            $hotel = HotelSelf::findOrFail($id);

            // Update the hotel_self record with data from the request
            $hotel->update($request->except(['secret_key', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email', 'latitude', 'longitude']) + [
                'manager_firstname' => $user->firstname,
                'manager_lastname' => $user->lastname,
                'manager_phone' => $user->phone,
                'manager_email' => $user->email,
                'user_id' => $user->id,
            ]);

            // Update the latitude and longitude
            $hotel->latitude = $request->input('latitude');
            $hotel->longitude = $request->input('longitude');

            // Update the hotel
            $hotel->save();

            return response()->json($hotel);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $hotel = HotelSelf::findOrFail($id);
            $hotel->delete();

            return response()->json(['message' => 'Hotel deleted successfully.'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e)
    {
        // Log the exception here if needed

        return response()->json(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
