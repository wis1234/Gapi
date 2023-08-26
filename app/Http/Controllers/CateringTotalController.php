<?php

namespace App\Http\Controllers;

use App\Models\Dessert;
use App\Models\Aperitif;
use App\Models\CaterSum;
use App\Models\MainDish;
use App\Models\Appetizer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CateringServiceClient;

class CateringTotalController extends Controller
{
    public function calculateTotals()
    {
        $cateringServices = array_unique(array_merge(
            Aperitif::pluck('catering_service_name')->toArray(),
            Appetizer::pluck('catering_service_name')->toArray(),
            MainDish::pluck('catering_service_name')->toArray(),
            Dessert::pluck('catering_service_name')->toArray()
        ));

        $totals = [];

        foreach ($cateringServices as $cateringService) {
            $totalCost = Aperitif::where('catering_service_name', $cateringService)->sum('cost')
                + Appetizer::where('catering_service_name', $cateringService)->sum('cost')
                + MainDish::where('catering_service_name', $cateringService)->sum('cost')
                + Dessert::where('catering_service_name', $cateringService)->sum('cost');
            
            $totals[$cateringService] = $totalCost;

            CaterSum::updateOrCreate(
                ['catering_service_name' => $cateringService],
                ['total_cost' => $totalCost]
            );
        }

        return response()->json(['message' => 'Catering totals calculated and updated.', 'totals' => $totals]);
    }




    public function tochoose()
{
    $cateringServices = CateringServiceClient::with('user')->get(); // Fetch all catering services
    $filteredCateringServices = [];

    foreach ($cateringServices as $cateringService) {
        $caterSum = CaterSum::where('catering_service_name', $cateringService->catering_service_name)->first();
        
        if ($caterSum && $cateringService->budget >= $caterSum->total_cost) {
            $filteredCateringServices[] = $cateringService;
        }
    }

    return response()->json(["Suggestion:" =>$filteredCateringServices]);
}

}
