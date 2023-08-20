<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\House;
use App\Models\ImmoAgence;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class HouseController extends Controller
{
    public function index()
    {
        try {
            $houses = House::all();
            return response()->json(['data' => $houses], 200);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'country' => 'required|string|max:255',
                'num_bedrooms' => 'required|integer',
                'num_bathrooms' => 'required|integer',
                'num_livingrooms' => 'required|integer',
                'num_apartments' => 'required|integer',
                'price' => 'required|numeric',
                'type' => 'required|string',
                'apartments_type' => 'required|string',
                'property_type' => 'required|string',
                'area' => 'required|numeric',
                'image1' => 'required|string',
                'image2' => 'required|string',
                'image3' => 'required|string',
                'description' => 'required|string',
                'notarial_information' => 'required|string',
                'immo_agence_code' => 'required|exists:immo_agences,immo_agence_code',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $immoAgence = ImmoAgence::where('immo_agence_code', $request->input('immo_agence_code'))->first();

            if (!$immoAgence) {
                return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
            }

            $houseData = $request->except('immo_agence_code'); // Exclude immo_agence_code from the response data
            $houseData['immo_agence_id'] = $immoAgence->id;
            $houseData['immo_agence_name'] = $immoAgence->name;

            $house = House::create($houseData);

            return response()->json(['data' => $house], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $house = House::findOrFail($id);
            return response()->json(['data' => $house], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'House not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $house = House::findOrFail($id);

            $validator = Validator::make($request->all(), [
                // Validation rules...
                'immo_agence_code' => 'required|exists:immo_agences,immo_agence_code',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $immoAgence = ImmoAgence::where('immo_agence_code', $request->input('immo_agence_code'))->first();

            if (!$immoAgence) {
                return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
            }

            $house->update(array_merge($request->all(), [
                'immo_agence_id' => $immoAgence->id,
                'immo_agence_name' => $immoAgence->name,
            ]));

            return response()->json(['data' => $house], 200);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'House not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $house = House::findOrFail($id);
            $house->delete();

            return response()->json(['message' => 'House deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'House not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    protected function handleException(\Exception $e)
    {
        // Log the exception here if needed

        return response()->json(['error' => 'An error occurred'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
