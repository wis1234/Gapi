<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AffordableCateringService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class AffordableCateringServiceController extends Controller
{
    /**
     * Display the specified affordable catering service.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $affordableService = AffordableCateringService::findOrFail($id);

            // Calculate the total cost of the catering service
            $totalCost = $affordableService->calculateTotalCost();

            return response()->json(['data' => $affordableService, 'total_cost' => $totalCost]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Affordable catering service not found'], 404);
        }
    }

    // Other controller methods can be added here as needed
}
