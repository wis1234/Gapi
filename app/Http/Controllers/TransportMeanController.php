<?php

namespace App\Http\Controllers;

use App\Models\TransportMean;
use App\Models\TravelAgency;
use App\Models\TransportMeanImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TransportMeanController extends Controller
{
    public function index()
    {
        try {
            $transportMeans = TransportMean::all();
            return response()->json([
                'success' => true,
                'data' => $transportMeans,
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
                'type' => 'required|string|max:255',
                'description' => 'required|string',
                'departure_country' => 'required|string|max:255',
                'departure_city' => 'required|string|max:255',
                'departure_date' => 'required|date',
                'departure_time' => 'required|date_format:H:i:s',
                'destination_country' => 'required|string|max:255',
                'destination_city' => 'required|string|max:255',
                'destination_date' => 'required|date',
                'destination_time' => 'required|date_format:H:i:s',
                'price' => 'required|numeric',
                'travel_agency_code' => 'required|string|max:255',
                'images' => 'array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $travelAgency = TravelAgency::where('travel_agency_code', $request->input('travel_agency_code'))->first();

            if (!$travelAgency) {
                return response()->json([
                    'success' => false,
                    'error' => 'Travel agency not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $transportMeanData = $request->all();
            $transportMeanData['travel_agency_id'] = $travelAgency->id;
            $transportMeanData['travel_agency_name'] = $travelAgency->name;

            $transportMean = TransportMean::create($transportMeanData);

            // Store the image paths in the transport_mean_images table
            $images = $request->file('images');

            if ($images) {
                foreach ($images as $image) {
                    $imagePath = $image->store('transport_mean_images', 'public');

                    $transportMean->transportMeanImages()->create([
                        'transport_mean_name' => $transportMean->name,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transport mean created successfully',
                'data' => $transportMean,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $transportMean = TransportMean::find($id);
            if (!$transportMean) {
                return response()->json([
                    'success' => false,
                    'error' => 'Transport mean not found',
                ], Response::HTTP_NOT_FOUND);
            }
            return response()->json([
                'success' => true,
                'data' => $transportMean,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
                'description' => 'required|string',
                'departure_country' => 'required|string|max:255',
                'departure_city' => 'required|string|max:255',
                'departure_date' => 'required|date',
                'departure_time' => 'required|date_format:H:i:s',
                'destination_country' => 'required|string|max:255',
                'destination_city' => 'required|string|max:255',
                'destination_date' => 'required|date',
                'destination_time' => 'required|date_format:H:i:s',
                'price' => 'required|numeric',
                'travel_agency_code' => 'required|string|max:255',
                'images' => 'array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $transportMean = TransportMean::find($id);

            if (!$transportMean) {
                return response()->json([
                    'success' => false,
                    'error' => 'Transport mean not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $travelAgency = TravelAgency::where('travel_agency_code', $request->input('travel_agency_code'))->first();

            if (!$travelAgency) {
                return response()->json([
                    'success' => false,
                    'error' => 'Travel agency not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $transportMeanData = $request->all();
            $transportMeanData['travel_agency_id'] = $travelAgency->id;
            $transportMeanData['travel_agency_name'] = $travelAgency->name;

            $transportMean->update($transportMeanData);

            // Update the image paths in the transport_mean_images table
            $images = $request->file('images');

            if ($images) {
                foreach ($images as $image) {
                    $imagePath = $image->store('transport_mean_images', 'public');

                    $transportMean->transportMeanImages()->create([
                        'transport_mean_name' => $transportMean->name,
                        'image_path' => $imagePath,
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Transport mean updated successfully',
                'data' => $transportMean,
            ]);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $transportMean = TransportMean::find($id);

            if (!$transportMean) {
                return response()->json([
                    'success' => false,
                    'error' => 'Transport mean not found',
                ], Response::HTTP_NOT_FOUND);
            }

            // Delete related images
            $transportMean->transportMeanImages()->delete();

            // Delete the transport mean itself
            $transportMean->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transport mean and related images deleted successfully',
            ], Response::HTTP_OK);
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
