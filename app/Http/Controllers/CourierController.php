<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class CourierController extends Controller
{
    public function index()
    {
        try {
            $couriers = Courier::whereHas('user', function ($query) {
                $query->whereNotNull('created_at');
            })->get();

            $couriers->each(function ($courier) {
                $courier->user->makeVisible(['firstname', 'lastname', 'phone', 'email']);
                $courier->user_data = $courier->user; // Include user data in the courier object
                unset($courier->user); // Remove the 'user' attribute
            });

            return response()->json(['data' => $couriers], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'secret_key' => 'required|string',
                'role' => 'nullable|string|max:50',
                'tm_type' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $courierData = $request->only(['role', 'tm_type', 'description']);
            $courierData['user_id'] = $user->id;

            $courier = Courier::create($courierData);

            $courier->user_data = $user; // Include user data in the courier object
            unset($courier->user); // Remove the 'user' attribute

            return response()->json(['data' => $courier], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $courier = Courier::with('user')->findOrFail($id);

            if ($courier->user && $courier->user->created_at) {
                $courier->user->makeVisible(['firstname', 'lastname', 'phone', 'email']);
                $courier->user_data = $courier->user; // Include user data in the courier object
                unset($courier->user); // Remove the 'user' attribute

                return response()->json(['data' => $courier], Response::HTTP_OK);
            } else {
                return response()->json(['error' => 'Courier not found or associated user not created'], Response::HTTP_NOT_FOUND);
            }
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'role' => 'required|string|max:50',
                'tm_type' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $courier = Courier::findOrFail($id);

            $courierData = $request->only(['role', 'tm_type', 'description']);
            $courier->update($courierData);

            $courier->user_data = $courier->user; // Include user data in the courier object
            unset($courier->user); // Remove the 'user' attribute

            return response()->json(['data' => $courier], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $courier = Courier::findOrFail($id);

            $courier->delete();

            $courier->user_data = $courier->user; // Include user data in the courier object
            unset($courier->user); // Remove the 'user' attribute

            // return response()->json(['data' => $courier], Response::HTTP_OK);
            $success_message = 'Courier deleted successfully';
            return response()->json(['data' => $success_message ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $exception)
    {
        // Log the exception here if needed

        return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
