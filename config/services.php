<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'recaptcha' => [
    // v3 (for checkout)
    'site_key' => env('RECAPTCHA_SITE_KEY'),
    'secret_key' => env('RECAPTCHA_SECRET_KEY'),
    
    // v2 Checkbox (for registration)
    'v2_checkbox_site_key' => env('RECAPTCHA_V2_CHECKBOX_SITE_KEY'),
    'v2_checkbox_secret_key' => env('RECAPTCHA_V2_CHECKBOX_SECRET_KEY'),
    
    // v2 Invisible (for login & guest)
    'v2_invisible_site_key' => env('RECAPTCHA_V2_INVISIBLE_SITE_KEY'),
    'v2_invisible_secret_key' => env('RECAPTCHA_V2_INVISIBLE_SECRET_KEY'),
    ],

];
