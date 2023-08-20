<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Answer;
use App\Models\Aperitif;
use App\Models\Appetizer;
use App\Models\MainDish;
use App\Models\Dessert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AnswerController extends Controller
{
    /**
     * Display the specified answer.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $answer = Answer::findOrFail($id);
            return response()->json($answer);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Answer not found'], Response::HTTP_NOT_FOUND);
        }
    }

    // Other CRUD methods can be added here as needed

    /**
     * Update the sum field in the specified answer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSum(Request $request, $id)
    {
        try {
            $answer = Answer::findOrFail($id);
            $answer->updateSum();
            return response()->json(['message' => 'Sum updated successfully', 'data' => $answer]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Answer not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
