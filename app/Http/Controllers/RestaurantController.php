<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RestaurantController extends Controller
{
    public function index()
    {
        try {
            $restaurants = Restaurant::all();

            // Append image URLs to the restaurants
            $restaurants->each(function ($restaurant) {
                $restaurant->image_url = asset('storage/' . $restaurant->image);
            });

            return response()->json(['restaurants' => $restaurants]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'website' => 'nullable|string|max:255',
                'secret_key' => 'required|exists:users,secret_key',
                'open_time' => 'nullable|string|max:255', // Include open_time field
                'close_time' => 'nullable|string|max:255', // Include close_time field
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }

            $restaurantCode = 'REST_' . uniqid() . '_AFRILINK';

            $restaurantData = $request->only(['name', 'address', 'city', 'description', 'website', 'open_time', 'close_time']); // Include open_time and close_time
            $restaurantData['user_id'] = $user->id;
            $restaurantData['manager_firstname'] = $user->firstname;
            $restaurantData['manager_lastname'] = $user->lastname;
            $restaurantData['manager_phone'] = $user->phone;
            $restaurantData['manager_email'] = $user->email;
            $restaurantData['restaurant_code'] = $restaurantCode;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('restaurant_images', 'public');
                $restaurantData['image'] = $imagePath;
            }

            $restaurant = Restaurant::create($restaurantData);

            return response()->json(['message' => 'Restaurant created successfully', 'data' => $restaurant->refresh()], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);

            // Append image URL to the restaurant
            $restaurant->image_url = asset('storage/' . $restaurant->image);

            return response()->json(['restaurant' => $restaurant]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['name', 'address', 'city', 'image', 'website', 'secret_key', 'description', 'open_time', 'close_time']), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'website' => 'nullable|string|max:255',
                'secret_key' => 'required|exists:users,secret_key',
                'open_time' => 'nullable|string|max:255',
                'close_time' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }

            $restaurant = Restaurant::findOrFail($id);

            if ($request->hasFile('image')) {
                if ($restaurant->image) {
                    Storage::delete('restaurant_images/' . $restaurant->image);
                }

                $image = $request->file('image');
                $imageName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $hashedImageName = md5(time() . '_' . $imageName) . '.' . $image->getClientOriginalExtension();
                $image->storeAs('restaurant_images', $hashedImageName);
                $restaurant->image = $hashedImageName;
            }

            $restaurant->update($request->only(['name', 'address', 'city', 'description', 'website', 'open_time', 'close_time']));
            $restaurant->user_id = $user->id;
            $restaurant->manager_firstname = $user->firstname;
            $restaurant->manager_lastname = $user->lastname;
            $restaurant->manager_phone = $user->phone;
            $restaurant->manager_email = $user->email;
            $restaurant->save();

            return response()->json(['message' => 'Restaurant updated successfully', 'data' => $restaurant->refresh()]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);

            if ($restaurant->image) {
                Storage::delete('restaurant_images/' . $restaurant->image);
            }

            $restaurant->delete();

            return response()->json(['message' => 'Restaurant deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Restaurant not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $exception)
    {
        // Log the exception here if needed

        return response()->json(['message' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
