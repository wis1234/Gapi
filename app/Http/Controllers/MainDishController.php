<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MainDish;
use App\Models\CateringService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

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
                'image1' => 'nullable|string|max:255',
                'image2' => 'nullable|string|max:255',
                'image3' => 'nullable|string|max:255',
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
                'image1' => 'nullable|string|max:255',
                'image2' => 'nullable|string|max:255',
                'image3' => 'nullable|string|max:255',
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
            $mainDish->update($mainDishData);

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
