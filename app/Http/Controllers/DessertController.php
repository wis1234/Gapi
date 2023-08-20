<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dessert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Models\CateringService;

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
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $dessertData = $request->except('catering_service_code');
            $dessertData['catering_service_id'] = $cateringService->id;
            $dessertData['catering_service_name'] = $cateringService->name;

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
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $dessert = Dessert::findOrFail($id);
            $dessertData = $request->except('catering_service_code');
            $dessertData['catering_service_id'] = $cateringService->id;
            $dessertData['catering_service_name'] = $cateringService->name;
            $dessert->update($dessertData);

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
