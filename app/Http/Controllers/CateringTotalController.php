<?php

namespace App\Http\Controllers;

use App\Models\Aperitif;
use App\Models\Appetizer;
use App\Models\MainDish;
use App\Models\Dessert;
use App\Models\CaterSum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
