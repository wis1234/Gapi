<?php

namespace App\Http\Controllers;

use App\Models\RidesSharing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RidesSharingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['name', 'address', 'city', 'website', 'secret_key']), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'website' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:255',
                'secret_key' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }

            $ridesSharingCode = 'RIDES_' . uniqid() . '_AFRILINK';

            $ridesSharingData = $request->only(['name', 'address', 'city', 'website']);
            
            // Automatically fill manager information
            $ridesSharingData['manager_firstname'] = $user->firstname;
            $ridesSharingData['manager_lastname'] = $user->lastname;
            $ridesSharingData['manager_phone'] = $user->phone;
            $ridesSharingData['manager_email'] = $user->email;
            $ridesSharingData['user_id'] = $user->id;
            $ridesSharingData['rides_sharing_code'] = $ridesSharingCode;

            $ridesSharing = RidesSharing::create($ridesSharingData);

            return response()->json(['message' => 'Rides sharing created successfully', 'data' => $ridesSharing], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the rides sharing'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function index()
    {
        $ridesSharing = RidesSharing::all();
        return response()->json($ridesSharing);
    }

    public function show($id)
    {
        try {
            $ridesSharing = RidesSharing::findOrFail($id);
            return response()->json($ridesSharing);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Rides sharing not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the rides sharing'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['name', 'address', 'city', 'website', 'secret_key']), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'website' => 'nullable|string|max:255',
                'image' => 'nullable|string|max:255',
                'secret_key' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
            }

            $ridesSharing = RidesSharing::findOrFail($id);
            $ridesSharingData = $request->only(['name', 'address', 'city', 'website']);
            $ridesSharingData['user_id'] = $user->id;
            $ridesSharingData['manager_firstname'] = $user->firstname;
            $ridesSharingData['manager_lastname'] = $user->lastname;
            $ridesSharingData['manager_phone'] = $user->phone;
            $ridesSharingData['manager_email'] = $user->email;
            $ridesSharing->update($ridesSharingData);

            return response()->json(['message' => 'Rides sharing updated successfully', 'data' => $ridesSharing]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Rides sharing not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the rides sharing'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $ridesSharing = RidesSharing::findOrFail($id);
            $ridesSharing->delete();

            return response()->json(['message' => 'Rides sharing deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Rides sharing not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the rides sharing'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
