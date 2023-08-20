<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twilio Account SID and Auth Token
    |--------------------------------------------------------------------------
    |
    | Here you should provide your Twilio Account SID and Auth Token.
    |
    */

    'account_sid' => env('TWILIO_SID'),
    'auth_token' => env('TWILIO_AUTH_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Default Phone Number
    |--------------------------------------------------------------------------
    |
    | Define your default Twilio phone number here.
    |
    */

    'phone_number' => env('TWILIO_PHONE_NUMBER'),

];
