<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ImmoAgence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImmoAgenceController extends Controller
{
    public function index()
    {
        try {
            $agences = ImmoAgence::all();
            return response()->json($agences);
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
                'image' => 'required|string|max:255',
                'website' => 'required|string|max:255',
                'secret_key' => 'required|exists:users,secret_key',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            $agenceData = $request->except(['secret_key', 'manager_firstname', 'manager_lastname', 'manager_phone', 'manager_email']);
            $agenceData['user_id'] = $user->id;

            // Set manager information
            $agenceData['manager_firstname'] = $user->firstname;
            $agenceData['manager_lastname'] = $user->lastname;
            $agenceData['manager_phone'] = $user->phone;
            $agenceData['manager_email'] = $user->email;

            // Generate immo_agence_code
            $agenceData['immo_agence_code'] = 'IMMO_' . uniqid() . '_AFRILINK';

            $agence = ImmoAgence::create($agenceData);
            return response()->json($agence, Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $agence = ImmoAgence::findOrFail($id);
            return response()->json($agence);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'image' => 'required|string|max:255',
                'website' => 'required|string|max:255',
                'secret_key' => 'required|exists:users,secret_key',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            $agence = ImmoAgence::findOrFail($id);
            $agence->update(array_merge($request->all(), ['user_id' => $user->id]));

            // Update manager information
            $agence->manager_firstname = $user->firstname;
            $agence->manager_lastname = $user->lastname;
            $agence->manager_phone = $user->phone;
            $agence->manager_email = $user->email;

            $agence->save();

            return response()->json($agence);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $agence = ImmoAgence::findOrFail($id);
            $agence->delete();

            return response()->json(['message' => 'Immo agence deleted successfully.'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Immo agence not found.'], Response::HTTP_NOT_FOUND);
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
