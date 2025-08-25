<?php

return [
    /*
    |--------------------------------------------------------------------------
    | PayMongo API Configuration
    |--------------------------------------------------------------------------
    */
    'secret_key' => env('PAYMONGO_SECRET_KEY'),
    'public_key' => env('PAYMONGO_PUBLIC_KEY'),
    'webhook_secret' => env('PAYMONGO_WEBHOOK_SECRET'),
    
    'base_url' => env('PAYMONGO_BASE_URL', 'https://api.paymongo.com/v1'),
    'sandbox_mode' => env('PAYMONGO_SANDBOX', true),
    
    /*
    |--------------------------------------------------------------------------
    | Supported Payment Methods
    |--------------------------------------------------------------------------
    */
    'payment_methods' => [
        'gcash' => [
            'enabled' => env('PAYMONGO_GCASH_ENABLED', true),
            'name' => 'GCash',
            'description' => 'Pay using your GCash wallet',
            'icon' => 'gcash-icon',
            'type' => 'ewallet',
        ],
        'paymaya' => [
            'enabled' => env('PAYMONGO_PAYMAYA_ENABLED', true),
            'name' => 'PayMaya',
            'description' => 'Pay using your PayMaya wallet',
            'icon' => 'paymaya-icon',
            'type' => 'ewallet',
        ],
        'card' => [
            'enabled' => env('PAYMONGO_CARD_ENABLED', true),
            'name' => 'Credit/Debit Card',
            'description' => 'Pay using your credit or debit card',
            'icon' => 'card-icon',
            'type' => 'card',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    */
    'currency' => 'PHP',
    'statement_descriptor' => 'LTO CANTEEN',
    
    /*
    |--------------------------------------------------------------------------
    | Redirect URLs
    |--------------------------------------------------------------------------
    */
    'redirect_urls' => [
        'success' => env('APP_URL') . '/payment/success',
        'failed' => env('APP_URL') . '/payment/failed',
        'cancelled' => env('APP_URL') . '/payment/cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    */
    'webhook_url' => env('APP_URL') . '/webhook/paymongo',
];