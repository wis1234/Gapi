<?php

namespace App\Http\Controllers;

use App\Models\TransportMeanImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TransportMeanImageController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'transport_mean_id' => 'required|exists:transport_means,id',
                'transport_mean_name' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'details' => $validator->errors(),
                ], Response::HTTP_BAD_REQUEST);
            }

            $transportMeanImage = new TransportMeanImage([
                'transport_mean_id' => $request->input('transport_mean_id'),
                'transport_mean_name' => $request->input('transport_mean_name'),
                'image_path' => $request->file('image')->store('transport_mean_images', 'public'),
            ]);

            $transportMeanImage->save();

            return response()->json([
                'success' => true,
                'message' => 'Transport mean image uploaded successfully',
                'data' => $transportMeanImage,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $transportMeanImage = TransportMeanImage::find($id);

            if (!$transportMeanImage) {
                return response()->json([
                    'success' => false,
                    'error' => 'Transport mean image not found',
                ], Response::HTTP_NOT_FOUND);
            }

            $imagePath = $transportMeanImage->image_path;

            $transportMeanImage->delete();

            // Delete the image file from storage
            \Illuminate\Support\Facades\Storage::delete($imagePath);

            return response()->json([
                'success' => true,
                'message' => 'Transport mean image deleted successfully',
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
