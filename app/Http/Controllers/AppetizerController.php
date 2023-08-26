<?php

namespace App\Http\Controllers;

use App\Models\Appetizer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CateringService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AppetizerController extends Controller
{
    public function index()
    {
        try {
            $appetizers = Appetizer::all();
            return response()->json($appetizers);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'num_guest' => 'required|integer',
                'cost' => 'required|numeric',
                'image1.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Allow multiple images
                'catering_service_code' => 'required|string|exists:catering_services,catering_service_code',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $cateringService = CateringService::where('catering_service_code', $request->input('catering_service_code'))->first();

            if (!$cateringService) {
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $appetizerData = $request->all();
            $appetizerData['catering_service_id'] = $cateringService->id;
            $appetizerData['catering_service_name'] = $cateringService->name;

            // Handle image upload for multiple images
            $imagePaths = [];
            if ($request->hasFile('image1')) {
                foreach ($request->file('image1') as $image) {
                    $imagePath = $image->store('appetizer_images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $appetizerData['image1'] = $imagePaths;

            $appetizer = Appetizer::create($appetizerData);

            return response()->json(['message' => 'Appetizer created successfully', 'data' => $appetizer], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $appetizer = Appetizer::findOrFail($id);
            return response()->json($appetizer);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appetizer not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $appetizer = Appetizer::find($id);

            if (!$appetizer) {
                return response()->json(['error' => 'Appetizer not found'], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'num_guest' => 'required|integer',
                'cost' => 'required|numeric',
                'image1.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Allow multiple images
                'catering_service_code' => 'required|string|exists:catering_services,catering_service_code',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $cateringService = CateringService::where('catering_service_code', $request->input('catering_service_code'))->first();

            if (!$cateringService) {
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $appetizer->update($request->all());
            $appetizer->catering_service_id = $cateringService->id;
            $appetizer->catering_service_name = $cateringService->name;

            // Handle image upload for multiple images
            $imagePaths = [];
            if ($request->hasFile('image1')) {
                foreach ($request->file('image1') as $image) {
                    $imagePath = $image->store('appetizer_images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $appetizer->image1 = $imagePaths;
            $appetizer->save();

            return response()->json(['message' => 'Appetizer updated successfully', 'data' => $appetizer]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appetizer not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $appetizer = Appetizer::find($id);

            if (!$appetizer) {
                return response()->json(['error' => 'Appetizer not found'], Response::HTTP_NOT_FOUND);
            }

            // Delete related images
            if (is_array($appetizer->image1)) {
                foreach ($appetizer->image1 as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $appetizer->delete();

            return response()->json(['message' => 'Appetizer deleted successfully']);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e)
    {
        // Log the exception here if needed

        return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
