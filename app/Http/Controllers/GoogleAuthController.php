<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            // Handle authentication failure (e.g., user denied access)
            return response()->json(['error' => 'Authentication failed.'], 401);
        }

        // Check if the user already exists in the database
        $user = User::where('google_id', $googleUser->getId())->first();

        if (!$user) {
            // Create a new user in your database if they don't exist
            $newUser = User::create([
                'firstname' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
            ]);

            $token = JWTAuth::fromUser($newUser);
            DB::table('users')->where('id', $newUser->id)->update(['secret_key' => $token]);

            return response()->json(['message' => 'Successfully logged in with Google!', 'user_secret_key' => $token]);
        } else {
            // Log in the existing user and generate a token
            Auth::login($user);
            $token = JWTAuth::fromUser($user);
            DB::table('users')->where('id', $user->id)->update(['secret_key' => $token]);

            return response()->json(['message' => 'Successfully logged in with Google!', 'user_secret_key' => $token]);
        }
    }
}
