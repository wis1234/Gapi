<?php

namespace App\Http\Controllers;

use App\Models\PointToPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class PointToPointController extends Controller
{
    public function index()
    {
        // Retrieve all records from the point_to_point table
        $points = PointToPoint::all();
        return response()->json(['data' => $points]);
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:20',
            'receiver_address' => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Create a new point_to_point record
            $point = PointToPoint::create($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the record'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Record created successfully', 'data' => $point], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        // Find a point_to_point record by ID
        $point = PointToPoint::find($id);

        if (!$point) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $point]);
    }

    public function update(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'sender_address' => 'nullable|string|max:255',
            'sender_phone' => 'nullable|string|max:20',
            'receiver_address' => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:20',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Find the point_to_point record by ID and update it
            $point = PointToPoint::findOrFail($id);
            $point->update($request->all());
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the record'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'Record updated successfully', 'data' => $point]);
    }

    public function destroy($id)
    {
        try {
            // Attempt to find the point_to_point record by ID
            $point = PointToPoint::findOrFail($id);
            
            // Delete the record
            $point->delete();
    
            // Return a success response
            return response()->json(['message' => 'Record deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle exceptions (e.g., database connection issues, etc.)
            return response()->json(['error' => 'An error occurred while deleting the record'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
