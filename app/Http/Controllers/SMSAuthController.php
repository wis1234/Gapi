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
            env('TWILIO_SID'),           // Your Twilio Account SID from .env
            env('TWILIO_AUTH_TOKEN')     // Your Twilio Auth Token from .env
        );

        $message = $client->messages->create(
            $userPhoneNumber,
            [
                'from' => env('TWILIO_PHONE_NUMBER'), // Use Twilio phone number from .env
                'body' => "Your verification code is: $verificationCode",
            ]
        );

        return response()->json(['message' => 'Verification code sent']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|numeric',
            'code' => 'required|numeric',
        ]);

        $userPhoneNumber = $request->input('phone');
        $userCode = $request->input('code');

        $user = User::where('phone', $userPhoneNumber)->first();

        if (!$user || $user->phone_code !== $userCode) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }

        // Verification successful, generate and return a token
        $token = JWTAuth::fromUser($user);
        DB::table('users')->where('id', $user->id)->update(['secret_key' => $token]);

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
