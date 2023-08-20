<?php

// app/Http/Controllers/HotelSelfController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Models\HotelSelf;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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
        try {
            $validator = Validator::make($request->all(), [
                'secret_key' => 'required|string|exists:users,secret_key',
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                // 'image' => 'required|string|max:255',
                'website' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            $hotelCode = 'HOTEL_' . uniqid() . '_AFRILINK';

            $hotelData = $request->except(['secret_key', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email']);
            $hotelData['manager_firstname'] = $user->firstname;
            $hotelData['manager_lastname'] = $user->lastname;
            $hotelData['manager_phone'] = $user->phone;
            $hotelData['manager_email'] = $user->email;
            $hotelData['user_id'] = $user->id;
            $hotelData['hotel_code'] = $hotelCode;

            $hotel = HotelSelf::create($hotelData);
            return response()->json($hotel, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

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
                'image' => 'required|string|max:255',
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
