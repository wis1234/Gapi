<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;

class SMSAuthController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $userPhoneNumber = $request->input('phone');
        $verificationCode = $this->generateVerificationCode(); // Call the method to generate the code

        // Store the verification code in the user's database record
        $user = User::where('phone', $userPhoneNumber)->first();
        $user->phone_code = $verificationCode;
        $user->save();

        $client = new Client(
            env('TWILIO_SID'),         
            env('TWILIO_AUTH_TOKEN')     
        );

        $message = $client->messages->create(
            $userPhoneNumber,
            [
                'from' => env('TWILIO_PHONE_NUMBER'), // Use Twilio phone number from .env
                'body' => "Your verification code is: $verificationCode",
            ]
        );

        // Generate and return a token after sending the verification code
        // $token = JWTAuth::fromUser($user);
        // $user->secret_key = $token;
        // $user->save();

        // return response()->json(['message' => 'Verification code sent', 'user_secret_key' => $token]);
        return response()->json(['message' => 'verification code sent sucessfully']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'code' => 'required|numeric',
        ]);
    
        $userCode = $request->input('code');
    
        $user = User::where('phone_code', $userCode)->first();
    
        if (!$user) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }
    
        // Verification successful, generate and return a token
        $token = JWTAuth::fromUser($user);
    
        // Update the user's secret key if needed
        $user->secret_key = $token;
        $user->save();
    
        return response()->json(['user_secret_key' => $token]);
    }
    

    /**
     * Generate a random verification code.
     *
     * @return string
     */
    private function generateVerificationCode()
    {
        // Generate a random 6-digit code
        $code = rand(100000, 999999);

        return (string) $code;
    }
}
