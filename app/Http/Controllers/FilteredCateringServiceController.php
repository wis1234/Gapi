<?php

namespace App\Http\Controllers;

use App\Models\CaterSum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class FilteredCateringServiceController extends Controller
{
    public function postBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'budget' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        $budget = $request->input('budget');

        $filteredCaterSumItems = CaterSum::where('total_cost', '<=', $budget)->get();

        return response()->json(['filtered_cater_sum_items' => $filteredCaterSumItems]);
    }
}
