<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomerDemand;
use App\Models\Restaurant;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerDemandController extends Controller
{
    public function index()
    {
        try {
            $customerDemands = CustomerDemand::all();
            return response()->json(['customer_demands' => $customerDemands], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->only(['dish_name', 'drink_name', 'num_dish', 'num_drink', 'restaurant_code', 'option']), [
                'dish_name' => 'required|string|max:255',
                'drink_name' => 'nullable|string|max:255',
                'num_dish' => 'required|integer|min:1',
                'num_drink' => 'required|integer|min:0',
                'restaurant_code' => 'required|exists:restaurants,restaurant_code',
                'option' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $restaurant = Restaurant::where('restaurant_code', $request->input('restaurant_code'))->first();

            if (!$restaurant) {
                return response()->json(['error' => 'Restaurant not found.'], Response::HTTP_NOT_FOUND);
            }

            $customerDemandData = $request->only(['dish_name', 'drink_name', 'num_dish', 'num_drink', 'option']);
            $customerDemandData['restaurant_id'] = $restaurant->id;
            $customerDemandData['restaurant_name'] = $restaurant->name;

            $customerDemand = CustomerDemand::create($customerDemandData);

            return response()->json(['message' => 'Customer demand created successfully', 'customer_demand' =>  $customerDemand], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function show($id)
    {
        try {
            $customerDemand = CustomerDemand::findOrFail($id);
            return response()->json(['customer_demand' => $customerDemand], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Customer demand not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->only(['dish_name', 'drink_name', 'num_dish', 'num_drink', 'restaurant_code', 'option']), [
                'dish_name' => 'required|string|max:255',
                'drink_name' => 'nullable|string|max:255',
                'num_dish' => 'required|integer|min:1',
                'num_drink' => 'required|integer|min:0',
                'restaurant_code' => 'required|exists:restaurants,restaurant_code',
                'option' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $restaurant = Restaurant::where('restaurant_code', $request->input('restaurant_code'))->first();

            if (!$restaurant) {
                return response()->json(['error' => 'Restaurant not found.'], Response::HTTP_NOT_FOUND);
            }

            $customerDemand = CustomerDemand::findOrFail($id);
            $customerDemand->update($request->only(['dish_name', 'drink_name', 'num_dish', 'num_drink', 'option']));
            $customerDemand->restaurant_id = $restaurant->id;
            $customerDemand->restaurant_name = $restaurant->name;
            $customerDemand->save();

            return response()->json(['message' => 'Customer demand updated successfully', 'customer_demand' =>  $customerDemand], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Customer demand not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    public function destroy($id)
    {
        try {
            $customerDemand = CustomerDemand::findOrFail($id);
            $customerDemand->delete();

            return response()->json(['message' => 'Customer demand deleted successfully'], Response::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Customer demand not found'], Response::HTTP_NOT_FOUND);
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
