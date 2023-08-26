<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Answer;
use App\Models\CaterSum;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CateringService;
use App\Models\CateringServiceClient;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CateringServiceClientController extends Controller
{
    public function demand(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'aperitif_name' => 'required|string|max:255',
            'appetizer_name' => 'required|string|max:255',
            'main_dish_name' => 'required|string|max:255',
            'dessert_name' => 'required|string|max:255',
            'num_guest' => 'required|integer|min:1',
            'budget' => 'required|numeric',
            'user_secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('secret_key', $request->input('user_secret_key'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        // Create a new catering service client
        $clientData = $request->except('user_secret_key');
        $clientData['user_id'] = $user->id;

        $client = CateringServiceClient::create($clientData);

        // Update the sum field in the related Answer record
        $this->updateSum($client->catering_service_id);

        return response()->json(['message' => 'Catering service selected successfully', 'data' => $client]);
    }

    public function index()
    {
        $clients = CateringServiceClient::all();
        return response()->json($clients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'aperitif_name' => 'required|string|max:255',
            'appetizer_name' => 'required|string|max:255',
            'main_dish_name' => 'required|string|max:255',
            'dessert_name' => 'required|string|max:255',
            'num_guest' => 'required|integer',
            'budget' => 'required|numeric',
            'user_secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('secret_key', $request->input('user_secret_key'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        $clientData = $request->except('user_secret_key');
        $clientData['user_id'] = $user->id;

        $client = CateringServiceClient::create($clientData);

        return response()->json(['message' => 'Catering service client created successfully', 'data' => $client]);
    }

    public function show($id)
    {
        try {
            $client = CateringServiceClient::findOrFail($id);
            return response()->json($client);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Catering service client not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'aperitif_name' => 'required|string|max:255',
            'appetizer_name' => 'required|string|max:255',
            'main_dish_name' => 'required|string|max:255',
            'dessert_name' => 'required|string|max:255',
            'num_guest' => 'required|integer',
            'budget' => 'required|numeric',
            'user_secret_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $user = User::where('secret_key', $request->input('user_secret_key'))->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        try {
            $client = CateringServiceClient::findOrFail($id);
            $client->update($request->except('user_secret_key'));
            return response()->json(['message' => 'Catering service client updated successfully', 'data' => $client]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Catering service client not found'], Response::HTTP_NOT_FOUND);
        }
    }

    public function destroy($id)
    {
        try {
            $client = CateringServiceClient::findOrFail($id);
            $client->delete();
            return response()->json(['message' => 'Catering service client deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Catering service client not found'], Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Other methods...

    // Helper function to update the sum field in the related Answer record
    private function updateSum($cateringServiceId)
    {
        $answer = Answer::where('catering_service_id', $cateringServiceId)->first();
        if ($answer) {
            $answer->updateSum();
        }
    }

    //suggestion logic goes here


    
    // public function filteredCateringServices()
    // {
    //     $caterSumItems = CaterSum::all(); // Fetch all items from cater_sum table
    //     $filteredCaterSumItems = [];
    
    //     foreach ($caterSumItems as $caterSumItem) {
    //         $cateringServiceClient = CateringServiceClient::where('budget', '>=', $caterSumItem->total_cost)
    //             ->first(); // Check if there's a matching catering service client
    
    //         if ($cateringServiceClient) {
    //             $filteredCaterSumItems[] = $caterSumItem;
    //         }
    //     }
    
    //     return response()->json(['filtered_cater_sum_items' => $filteredCaterSumItems]);
    // }

    public function filteredCateringServices(Request $request, $budget)
{
    $caterSumItems = CaterSum::all(); // Fetch all items from cater_sum table
    $filteredCaterSumItems = [];

    foreach ($caterSumItems as $caterSumItem) {
        if ($caterSumItem->total_cost <= $budget) {
            $cateringServiceClient = CateringServiceClient::where('budget', '>=', $caterSumItem->total_cost)
                ->first(); // Check if there's a matching catering service client

            if ($cateringServiceClient) {
                $filteredCaterSumItems[] = $caterSumItem;
            }
        }
    }

    return response()->json(['filtered_cater_sum_items' => $filteredCaterSumItems]);
}
    
    

}
