<?php

namespace App\Http\Controllers;

use App\Models\PointToPoint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PointToPointController extends Controller
{
    public function index()
    {
        try {
            $points = PointToPoint::all();
            return response()->json(['data' => $points]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching records.', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'sender_address' => 'nullable|string|max:255',
                'sender_phone' => 'nullable|string|max:20',
                'receiver_address' => 'nullable|string|max:255',
                'receiver_phone' => 'nullable|string|max:20',
                'courier' => 'nullable|string|max:25',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $point = PointToPoint::create($request->all());

            return response()->json(['message' => 'Record created successfully', 'data' => $point], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the record', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $point = PointToPoint::find($id);

            if (!$point) {
                return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
            }

            return response()->json(['data' => $point]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the record.', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'sender_address' => 'nullable|string|max:255',
                'sender_phone' => 'nullable|string|max:20',
                'receiver_address' => 'nullable|string|max:255',
                'receiver_phone' => 'nullable|string|max:20',
                'courier' => 'nullable|string|max:25',
                'description' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $point = PointToPoint::findOrFail($id);
            $point->update($request->all());

            return response()->json(['message' => 'Record updated successfully', 'data' => $point]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the record', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $point = PointToPoint::findOrFail($id);

            $point->delete();

            return response()->json(['message' => 'Record deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Record not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the record', 'details' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
