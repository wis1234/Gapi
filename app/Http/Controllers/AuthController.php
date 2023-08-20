<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', [
            'except' => ['login', 'register']
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'account_type'=> 'required|string',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $imagePath = $request->file('photo')->store('uploads/profiles', 'public'); // Store the uploaded image

        $user = User::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'age' => $request->age,
            'gender' => $request->gender,
            'account_type' => $request->account_type,
            'photo' => $imagePath, // Store the path of the uploaded image
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully', 'user' => $user]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
        } catch (TokenExpiredException $e) {
            // Token has expired, refresh it
            $token = JWTAuth::refresh();
        }

        // Update the token in the database
        $user = auth()->user();
        DB::table('users')->where('id', $user->id)->update(['secret_key' => $token]);

        return response()->json(['user_secret_key' => $token]);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user()
    {
        $user = auth()->user();

        return response()->json(['user' => $user]);
    }

    public function refreshToken()
    {
        $token = JWTAuth::refresh();

        // Update the token in the database
        $user = auth()->user();
        DB::table('users')->where('id', $user->id)->update(['secret_key' => $token]);

        return response()->json(['token' => $token]);
    }

    public function refresh()
    {
        $token = Auth::refresh();

        return response()->json(['token' => $token]);
    }
}
