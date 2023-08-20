<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Courier;
use App\Models\User;
use Illuminate\Http\Response;

class CourierController extends Controller
{
    public function index()
    {
        $couriers = Courier::all();
        return response()->json(['couriers' => $couriers], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'secret_key' => 'required|string',
            'role' => 'required|string|max:50',
            'photo' => 'required|string|max:50',
        ]);

        $user = User::where('secret_key', $validatedData['secret_key'])->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $courierData = [
            'user_id' => $user->id,
            'role' => $validatedData['role'],
            'photo' => $validatedData['photo'],
        ];

        $courier = Courier::create($courierData);

        return response()->json(['message' => 'Courier created successfully', 'courier' => $courier], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $courier = Courier::find($id);

        if (!$courier) {
            return response()->json(['message' => 'Courier not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['courier' => $courier], Response::HTTP_OK);
    }

    public function update(Request $request, $id)
    {
        $courier = Courier::find($id);

        if (!$courier) {
            return response()->json(['message' => 'Courier not found'], Response::HTTP_NOT_FOUND);
        }

        $validatedData = $request->validate([
            'role' => 'required|string|max:50',
            'photo' => 'required|string|max:50',
        ]);

        $courier->update($validatedData);

        return response()->json(['message' => 'Courier updated successfully'], Response::HTTP_OK);
    }

    public function destroy($id)
    {
        $courier = Courier::find($id);

        if (!$courier) {
            return response()->json(['message' => 'Courier not found'], Response::HTTP_NOT_FOUND);
        }

        $courier->delete();

        return response()->json(['message' => 'Courier deleted successfully'], Response::HTTP_OK);
    }
}
