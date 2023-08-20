<?php

namespace App\Http\Controllers;

use App\Models\TravelAgency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TravelAgencyController extends Controller
{
    public function index()
    {
        try {
            $travelAgencies = TravelAgency::all();
            return response()->json([
                'success' => true,
                'data' => $travelAgencies,
            ]);
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
                'website' => 'required|string|max:255',
                'image' => 'nullable|string|max:255',
                'secret_key' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            // Generate a unique travel_agency_code using uniqid method
            $travelAgencyCode = 'TRAVEL_' . uniqid() . '_AFRILINK';

            $travelAgencyData = $request->except(['secret_key']);
            $travelAgencyData['user_id'] = $user->id;
            $travelAgencyData['manager_firstname'] = $user->firstname;
            $travelAgencyData['manager_lastname'] = $user->lastname;
            $travelAgencyData['manager_email'] = $user->email;
            $travelAgencyData['manager_phone'] = $user->phone;
            $travelAgencyData['travel_agency_code'] = $travelAgencyCode;

            $newTravelAgency = TravelAgency::create($travelAgencyData);

            return response()->json([
                'success' => true,
                'message' => 'Travel agency created successfully',
                'data' => $newTravelAgency,
                'travel_agency_code' => $travelAgencyCode,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $travelAgency = TravelAgency::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $travelAgency,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Travel agency not found',
            ], Response::HTTP_NOT_FOUND);
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
                'website' => 'required|string|max:255',
                'image' => 'nullable|string|max:255',
                'secret_key' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $user = User::where('secret_key', $request->input('secret_key'))->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User not found.',
                ], Response::HTTP_NOT_FOUND);
            }

            $travelAgency = TravelAgency::findOrFail($id);
            $travelAgencyData = $request->except(['secret_key']);
            $travelAgencyData['user_id'] = $user->id;
            $travelAgencyData['manager_firstname'] = $user->firstname;
            $travelAgencyData['manager_lastname'] = $user->lastname;
            $travelAgencyData['manager_email'] = $user->email;
            $travelAgencyData['manager_phone'] = $user->phone;

            $travelAgency->update($travelAgencyData);

            return response()->json([
                'success' => true,
                'message' => 'Travel agency updated successfully',
                'data' => $travelAgency,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Travel agency not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $travelAgency = TravelAgency::findOrFail($id);
            $travelAgency->delete();
            return response()->json([
                'success' => true,
                'message' => 'Travel agency deleted successfully',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Travel agency not found',
            ], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Handle exceptions and provide a standardized response.
     *
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    protected function handleException(\Exception $exception)
    {
        // Log the exception here if needed

        return response()->json([
            'success' => false,
            'error' => 'Something went wrong',
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
