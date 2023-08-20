<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use App\Models\RidesSharing;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\RidesSharingImage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Exception;

class VehiculeController extends Controller
{
    public function index()
    {
        try {
            $vehicles = Vehicule::all();
            return response()->json(['data' => $vehicles], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching vehicles', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'availability' => 'required|string',
            'rides_sharing_code' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // Retrieve rides_sharing information based on provided rides_sharing_code
        $ridesSharing = RidesSharing::where('rides_sharing_code', $request->input('rides_sharing_code'))->first();

        $vehicleData = $request->all();
        $vehicleData['rides_sharing_id'] = $ridesSharing->id;
        $vehicleData['rides_sharing_name'] = $ridesSharing->name;

        $vehicle = Vehicule::create($vehicleData);

        // Store the image paths in the rides_sharing_images table
        $images = $request->file('images');

        if ($images) {
            foreach ($images as $image) {
                $imagePath = $image->store('rides_sharing_images', 'public');
        
                RidesSharingImage::create([
                    'rides_sharing_id' => $vehicle->rides_sharing_id,
                    'rides_sharing_name' => $vehicle->rides_sharing_name,
                    'vehicule_name' => $vehicle->name,
                    'image_path' => $imagePath,
                ]);
            }
        }

        return response()->json(['message' => 'Vehicle created successfully', 'data' => $vehicle], Response::HTTP_CREATED);
    } catch (Exception $e) {
        return response()->json(['message' => 'Error creating vehicle', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

    public function show($id)
    {
        try {
            $vehicle = Vehicule::findOrFail($id);
            return response()->json(['data' => $vehicle], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error fetching vehicle', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'description' => 'required|string',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'availability' => 'required|string',
                'rides_sharing_code' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            // Retrieve rides_sharing information based on provided rides_sharing_code
            $ridesSharing = RidesSharing::where('rides_sharing_code', $request->input('rides_sharing_code'))->first();

            $vehicle = Vehicule::findOrFail($id);

            $vehicleData = $request->all();
            $vehicleData['rides_sharing_id'] = $ridesSharing->id;
            $vehicleData['rides_sharing_name'] = $ridesSharing->name;

            $vehicle->update($vehicleData);

            return response()->json(['message' => 'Vehicle updated successfully', 'data' => $vehicle], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error updating vehicle', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $vehicle = Vehicule::with('ridesSharingImages')->findOrFail($id);
    
            // Delete related images based on vehicle name
            foreach ($vehicle->ridesSharingImages as $image) {
                if ($image->vehicule_name === $vehicle->name) {
                    // Delete the image file from storage
                    Storage::delete($image->image_path);
    
                    // Delete the image record from the database
                    $image->delete();
                }
            }
    
            // Delete the vehicle itself
            $vehicle->delete();
    
            return response()->json(['message' => 'Vehicle and related images deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Vehicle not found'], Response::HTTP_NOT_FOUND);
        } catch (Exception $e) {
            return response()->json(['message' => 'Error deleting vehicle', 'error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
