<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Apartment;
use App\Models\ImmoAgence;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ApartmentController extends Controller
{
    public function index()
    {
        try {
            $apartments = Apartment::all();
            return response()->json($apartments);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'address' => 'required|string',
                'num_bedrooms' => 'required|integer',
                'num_bathrooms' => 'required|integer',
                'num_livingrooms' => 'required|integer',
                'description' => 'required|string',
                'image1' => 'required|string',
                'image2' => 'required|string',
                'image3' => 'required|string',
                'price' => 'required|numeric',
                'notarial_information' => 'nullable|string',
                'immo_agence_code' => 'required|exists:immo_agences,immo_agence_code',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $immoAgence = ImmoAgence::where('immo_agence_code', $request->input('immo_agence_code'))->first();

            if (!$immoAgence) {
                return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
            }

            $apartmentData = $request->except('immo_agence_code'); // Exclude immo_agence_code from the response data
            $apartmentData['immo_agence_id'] = $immoAgence->id;
            $apartmentData['immo_agence_name'] = $immoAgence->name;

            $apartment = Apartment::create($apartmentData);

            return response()->json($apartment, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $apartment = Apartment::findOrFail($id);
            return response()->json($apartment);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Apartment not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $apartment = Apartment::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'address' => 'required|string',
                'num_bedrooms' => 'required|integer',
                'num_bathrooms' => 'required|integer',
                'num_livingrooms' => 'required|integer',
                'description' => 'required|string',
                'image1' => 'required|string',
                'image2' => 'required|string',
                'image3' => 'required|string',
                'price' => 'required|numeric',
                'notarial_information' => 'nullable|string',
                'immo_agence_code' => 'required|exists:immo_agences,immo_agence_code',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $immoAgence = ImmoAgence::where('immo_agence_code', $request->input('immo_agence_code'))->first();

            if (!$immoAgence) {
                return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
            }

            $apartment->update(array_merge($request->all(), ['immo_agence_id' => $immoAgence->id]));

            return response()->json($apartment);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Apartment not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $apartment = Apartment::findOrFail($id);
            $apartment->delete();

            return response()->json(['message' => 'Apartment deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Apartment not found'], Response::HTTP_NOT_FOUND);
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
