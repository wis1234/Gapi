<?php

namespace App\Http\Controllers;

use App\Models\Aperitif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use App\Models\CateringService;

class AperitifController extends Controller
{
    public function index()
    {
        try {
            $aperitifs = Aperitif::all();
            return response()->json($aperitifs);
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
                'catering_service_code' => 'required|string|exists:catering_services,catering_service_code',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $cateringService = CateringService::where('catering_service_code', $request->input('catering_service_code'))->first();

            if (!$cateringService) {
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $aperitifData = $request->all();
            $aperitifData['catering_service_id'] = $cateringService->id;
            $aperitifData['catering_service_name'] = $cateringService->name;

            $aperitif = Aperitif::create($aperitifData);

            return response()->json(['message' => 'Aperitif created successfully', 'data' => $aperitif], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $aperitif = Aperitif::findOrFail($id);
            return response()->json($aperitif);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Aperitif not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $aperitif = Aperitif::find($id);

            if (!$aperitif) {
                return response()->json(['error' => 'Aperitif not found'], Response::HTTP_NOT_FOUND);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'num_guest' => 'required|integer',
                'cost' => 'required|numeric',
                'image1' => 'nullable|string|max:255',
                'image2' => 'nullable|string|max:255',
                'image3' => 'nullable|string|max:255',
                'catering_service_code' => 'required|string|exists:catering_services,catering_service_code',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
            }

            $cateringService = CateringService::where('catering_service_code', $request->input('catering_service_code'))->first();

            if (!$cateringService) {
                return response()->json(['error' => 'Catering service not found.'], Response::HTTP_NOT_FOUND);
            }

            $aperitif->update($request->all());
            $aperitif->catering_service_id = $cateringService->id;
            $aperitif->catering_service_name = $cateringService->name;
            $aperitif->save();

            return response()->json(['message' => 'Aperitif updated successfully', 'data' => $aperitif]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Aperitif not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $aperitif = Aperitif::find($id);

            if (!$aperitif) {
                return response()->json(['error' => 'Aperitif not found'], Response::HTTP_NOT_FOUND);
            }

            $aperitif->delete();

            return response()->json(['message' => 'Aperitif deleted successfully']);
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
