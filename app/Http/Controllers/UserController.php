<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        $requestData = $request->only([
            'firstname',
            'lastname',
            'age',
            'gender',
            'hobby',
            'phone',
            'photo',
            'email',
            'google_id',
            'password',
        ]);

        $validator = Validator::make($requestData, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'age' => 'required|numeric',
            'gender' => 'required|string|max:100',
            'hobby' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'photo' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email',
            'google_id' => 'string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = User::create($requestData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while creating the user'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'User created successfully', 'data' => $user], Response::HTTP_CREATED);
        dd($request->all());
    }

    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => $user]);
    }

    public function update(Request $request, $id)
    {
        $requestData = $request->only([
            'firstname',
            'lastname',
            'age',
            'gender',
            'hobby',
            'phone',
            'photo',
            'email',
            'google_id',
            'password',
        ]);

        $validator = Validator::make($requestData, [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'age' => 'required|numeric',
            'gender' => 'required|string|max:100',
            'hobby' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'photo' => 'required|string|max:20',
            'email' => 'required|email|unique:users,email,' . $id,
            'google_id' => 'string',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Validation failed', 'details' => $validator->errors()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $user = User::findOrFail($id);
            $user->update($requestData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while updating the user'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(['message' => 'User updated successfully', 'data' => $user]);
    }

    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }
}
