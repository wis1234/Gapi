<?php

namespace App\Http\Controllers;

use App\Models\DrinkImage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;

class DrinkImageController extends Controller
{
    public function index()
    {
        try {
            $drinkImages = DrinkImage::all();
            return response()->json(['data' => $drinkImages], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $drinkImage = DrinkImage::findOrFail($id);
            return response()->json(['data' => $drinkImage], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Drink image not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['drink_id', 'drink_name', 'image']), [
                'drink_id' => 'required|exists:drinks,id',
                'drink_name' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $drinkImage = DrinkImage::findOrFail($id);

            // Delete the previous image file from storage
            Storage::delete($drinkImage->image_path);

            // Upload the new image file
            $imagePath = $request->file('image')->store('drink_images', 'public');

            $drinkImage->update([
                'drink_id' => $request->input('drink_id'),
                'drink_name' => $request->input('drink_name'),
                'image_path' => $imagePath,
            ]);

            return response()->json(['message' => 'Drink image updated successfully', 'data' => $drinkImage], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Drink image not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $drinkImage = DrinkImage::findOrFail($id);

            // Delete the image file from storage
            Storage::delete($drinkImage->image_path);

            $drinkImage->delete();

            return response()->json(['message' => 'Drink image deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Drink image not found'], Response::HTTP_NOT_FOUND);
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
