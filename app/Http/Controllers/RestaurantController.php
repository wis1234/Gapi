<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::all();
        return response()->json($restaurants);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->only(['name', 'address', 'city', 'image', 'website', 'secret_key']), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'image' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'secret_key' => 'required|exists:users,secret_key',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::where('secret_key', $request->input('secret_key'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $restaurantCode = 'REST_' . uniqid() . '_AFRILINK';

        $restaurantData = $request->only(['name', 'address', 'city', 'menu', 'website']);
        $restaurantData['user_id'] = $user->id;
        $restaurantData['manager_firstname'] = $user->firstname;
        $restaurantData['manager_lastname'] = $user->lastname;
        $restaurantData['manager_phone'] = $user->phone;
        $restaurantData['manager_email'] = $user->email;
        $restaurantData['restaurant_code'] = $restaurantCode;

        $restaurant = Restaurant::create($restaurantData);

        return response()->json(['message' => 'Restaurant created successfully', 'data' => $restaurant], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return response()->json(['restaurant' => $restaurant]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->only(['name', 'address', 'city', 'image', 'website', 'secret_key']), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'image' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'secret_key' => 'required|exists:users,secret_key',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::where('secret_key', $request->input('secret_key'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update($request->only(['name', 'address', 'city', 'image', 'website']));
        $restaurant->user_id = $user->id;
        $restaurant->manager_firstname = $user->firstname;
        $restaurant->manager_lastname = $user->lastname;
        $restaurant->manager_phone = $user->phone;
        $restaurant->manager_email = $user->email;
        $restaurant->save();

        return response()->json(['message' => 'Restaurant updated successfully', 'data' => $restaurant]);
    }

    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->delete();

        return response()->json(['message' => 'Restaurant deleted successfully'], Response::HTTP_OK);
    }
}
