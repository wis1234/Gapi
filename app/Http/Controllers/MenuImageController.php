<?php

namespace App\Http\Controllers;

use App\Models\MenuImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class MenuImageController extends Controller
{
    public function index()
    {
        try {
            $menuImages = MenuImage::all();
            return response()->json($menuImages);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['restaurant_id', 'restaurant_name', 'image_path']), [
                'restaurant_id' => 'required|exists:restaurants,id',
                'restaurant_name' => 'required|string',
                'image_path' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $menuImage = MenuImage::create($request->all());

            return response()->json(['message' => 'Menu image created successfully', 'data' => $menuImage]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $menuImage = MenuImage::findOrFail($id);
            return response()->json($menuImage);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['restaurant_id', 'restaurant_name', 'image_path']), [
                'restaurant_id' => 'required|exists:restaurants,id',
                'restaurant_name' => 'required|string',
                'image_path' => 'required|string',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $menuImage = MenuImage::findOrFail($id);
            $menuImage->update($request->all());

            return response()->json(['message' => 'Menu image updated successfully', 'data' => $menuImage]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $menuImage = MenuImage::findOrFail($id);
            $menuImage->delete();

            return response()->json(['message' => 'Menu image deleted successfully'], Response::HTTP_OK);
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
