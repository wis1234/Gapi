<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainDish;
use App\Models\CateringService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class MainDishController extends Controller
{
    public function index()
    {
        try {
            $mainDishes = MainDish::all();
            return response()->json($mainDishes);
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
                return response()->json(['error' => 'Catering service not found'], Response::HTTP_NOT_FOUND);
            }

            $mainDishData = $request->except('catering_service_code');
            $mainDishData['catering_service_id'] = $cateringService->id;
            $mainDishData['catering_service_name'] = $cateringService->name;

            // Handle image upload for multiple images
            $imagePaths = [];
            if ($request->hasFile('image1')) {
                foreach ($request->file('image1') as $image) {
                    $imagePath = $image->store('main_dish_images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $mainDishData['image1'] = $imagePaths;

            $mainDish = MainDish::create($mainDishData);

            return response()->json(['message' => 'Main dish created successfully', 'data' => $mainDish], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $mainDish = MainDish::findOrFail($id);
            return response()->json($mainDish);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Main dish not found'], Response::HTTP_NOT_FOUND);
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
                return response()->json(['error' => 'Catering service not found'], Response::HTTP_NOT_FOUND);
            }

            $mainDish = MainDish::findOrFail($id);
            $mainDishData = $request->except('catering_service_code');
            $mainDishData['catering_service_id'] = $cateringService->id;
            $mainDishData['catering_service_name'] = $cateringService->name;

            // Handle image upload for multiple images
            $imagePaths = [];
            if ($request->hasFile('image1')) {
                foreach ($request->file('image1') as $image) {
                    $imagePath = $image->store('main_dish_images', 'public');
                    $imagePaths[] = $imagePath;
                }
            }

            $mainDish->update($mainDishData);
            $mainDish->image1 = $imagePaths;
            $mainDish->save();

            return response()->json(['message' => 'Main dish updated successfully', 'data' => $mainDish]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Main dish not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $mainDish = MainDish::findOrFail($id);
            
            // Delete related images
            if (is_array($mainDish->image1)) {
                foreach ($mainDish->image1 as $imagePath) {
                    Storage::disk('public')->delete($imagePath);
                }
            }

            $mainDish->delete();

            return response()->json(['message' => 'Main dish deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Main dish not found'], Response::HTTP_NOT_FOUND);
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
