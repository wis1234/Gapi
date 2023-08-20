<?php

namespace App\Http\Controllers;

use App\Models\MenuPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class MenuPriceController extends Controller
{
    public function index()
    {
        $menuPrices = MenuPrice::all();
        return response()->json($menuPrices);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['cost', 'restaurant_id']), [
                'cost' => 'required|numeric',
                'restaurant_id' => 'required|exists:restaurants,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $menuPrice = MenuPrice::create($request->only(['cost', 'restaurant_id']));

            return response()->json(['message' => 'Menu price created successfully', 'data' => $menuPrice]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the menu price'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $menuPrice = MenuPrice::findOrFail($id);
            return response()->json($menuPrice);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu price not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while fetching the menu price'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['cost', 'restaurant_id']), [
                'cost' => 'required|numeric',
                'restaurant_id' => 'required|exists:restaurants,id',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $menuPrice = MenuPrice::findOrFail($id);
            $menuPrice->update($request->only(['cost', 'restaurant_id']));

            return response()->json(['message' => 'Menu price updated successfully', 'data' => $menuPrice]);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu price not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the menu price'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $menuPrice = MenuPrice::findOrFail($id);
            $menuPrice->delete();

            return response()->json(['message' => 'Menu price deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Menu price not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while deleting the menu price'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
