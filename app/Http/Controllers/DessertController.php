<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dessert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Models\CateringService;
use Illuminate\Support\Facades\Storage;

class DessertController extends Controller
{
    public function index()
    {
        try {
            $desserts = Dessert::all();
            return response()->json($desserts);
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
                'catering_service_code' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $cateringService = CateringService::where('catering_service_code', $request->input('catering_service_code'))->first();

            if (!$cateringService) {
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $dessertData = $request->except('catering_service_code');
            $dessertData['catering_service_id'] = $cateringService->id;
            $dessertData['catering_service_name'] = $cateringService->name;

            // Handle image upload for multiple images
            $imagePaths = [];
            if ($request->hasFile('image1')) {
                foreach ($request->file('image1') as $image) {
                    $imagePath = $image->store('dessert_images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $dessertData['image1'] = $imagePaths;

            $dessert = Dessert::create($dessertData);

            return response()->json(['message' => 'Dessert created successfully', 'data' => $dessert], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $dessert = Dessert::findOrFail($id);
            return response()->json($dessert);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Dessert not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'num_guest' => 'required|integer',
                'cost' => 'required|numeric',
                'image1.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Allow multiple images
                'catering_service_code' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $cateringService = CateringService::where('catering_service_code', $request->input('catering_service_code'))->first();

            if (!$cateringService) {
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $dessert = Dessert::findOrFail($id);
            $dessertData = $request->except('catering_service_code');
            $dessertData['catering_service_id'] = $cateringService->id;
            $dessertData['catering_service_name'] = $cateringService->name;

            // Handle image upload for multiple images
            $imagePaths = [];
            if ($request->hasFile('image1')) {
                foreach ($request->file('image1') as $image) {
                    $imagePath = $image->store('dessert_images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $dessert->update($dessertData);
            $dessert->image1 = $imagePaths;
            $dessert->save();

            return response()->json(['message' => 'Dessert updated successfully', 'data' => $dessert]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Dessert not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $dessert = Dessert::findOrFail($id);

            // Delete related images
            if (is_array($dessert->image1)) {
                foreach ($dessert->image1 as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $dessert->delete();

            return response()->json(['message' => 'Dessert deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Dessert not found'], Response::HTTP_NOT_FOUND);
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
