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
            'website' => 'required|string|max:255',
            'hotel_self_images' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Adjust the image validation rules as needed
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }
    
        $user = User::where('secret_key', $request->input('secret_key'))->first();
    
        // Generate the hotel_code
        $hotelCode = 'HOTEL_' . uniqid() . '_AFRILINK';
    
        // Handle image upload
        $imagePath = null;
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
    
        // Create the hotel_self record
        $hotel = HotelSelf::create($hotelData);
    
        // Calculate the lowest room price from the hotels table
        $lowestPrice = Hotel::min('room_price');
    
        // Update the low_price field in the hotel_self record
        $hotelSelf = HotelSelf::where('hotel_id', $hotel->id)->first();
        if ($hotelSelf) {
            $hotelSelf->low_price = $lowestPrice;
            $hotelSelf->save();
        }
    
        // Create a response array with hotel data and lowest room price
        $response = [
            'hotel' => $hotel,
            'lowest_room_price' => $lowestPrice,
        ];
    
        return response()->json($response, Response::HTTP_CREATED);
    }
    
    
    
    

//     public function store(Request $request)
// {
//     try {
//         $validator = Validator::make($request->all(), [
//             'secret_key' => 'required|string|exists:users,secret_key',
//             'name' => 'required|string|max:255',
//             'address' => 'required|string|max:255',
//             'city' => 'required|string|max:255',
//             'low_price' => 'required|integer|max:255',
//             'website' => 'required|string|max:255',
//             'hotel_self_images' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Adjust the image validation rules as needed
//         ]);

//         if ($validator->fails()) {
//             throw new ValidationException($validator);
//         }

//         $user = User::where('secret_key', $request->input('secret_key'))->first();

//         $hotelCode = 'HOTEL_' . uniqid() . '_AFRILINK';

//         // Handle image upload
//         $imagePath = null;
//         if ($request->hasFile('hotel_self_images')) {
//             $image = $request->file('hotel_self_images');
//             $imageName = time() . '_' . $image->getClientOriginalName();
//             $imagePath = $image->storeAs('uploads/hotels', $imageName, 'public');
//         }

//         $hotelData = $request->except(['secret_key', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email', 'hotel_self_images']);
//         $hotelData['manager_firstname'] = $user->firstname;
//         $hotelData['manager_lastname'] = $user->lastname;
//         $hotelData['manager_phone'] = $user->phone;
//         $hotelData['manager_email'] = $user->email;
//         $hotelData['user_id'] = $user->id;
//         $hotelData['hotel_code'] = $hotelCode;
//         $hotelData['hotel_self_images'] = $imagePath; // Assign the image path to the database field

//         $hotel = HotelSelf::create($hotelData);
//         return response()->json($hotel, Response::HTTP_CREATED);
//     } catch (ValidationException $e) {
//         return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
//     } catch (\Exception $e) {
//         return $this->handleException($e);
//     }
// }




    public function show($id)
    {
        try {
            $hotel = HotelSelf::findOrFail($id);
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
                'hotel_self_images' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'low_price' => 'required|integer|max:255',
                'website' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            $hotel = HotelSelf::findOrFail($id);
            $hotel->update($request->except(['secret_key', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email']) + [
                'manager_firstname' => $user->firstname,
                'manager_lastname' => $user->lastname,
                'manager_phone' => $user->phone,
                'manager_email' => $user->email,
                'user_id' => $user->id,
            ]);

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
