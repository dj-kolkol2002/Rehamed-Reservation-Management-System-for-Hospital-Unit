<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe API Keys
    |--------------------------------------------------------------------------
    |
    | The Stripe publishable key and secret key give you access to Stripe's
    | API. The "publishable" key is typically used when interacting with
    | Stripe.js while the "secret" key is used when making requests from
    | your application's backend.
    |
    */

    'key' => env('STRIPE_KEY'),

    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhook Secret
    |--------------------------------------------------------------------------
    |
    | Your Stripe webhook secret is used to verify that incoming webhook
    | requests are genuinely from Stripe.
    |
    */

    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency for your payments.
    |
    */

    'currency' => env('STRIPE_CURRENCY', 'pln'),

    /*
    |--------------------------------------------------------------------------
    | Company Information
    |--------------------------------------------------------------------------
    |
    | This information will be used for generating invoices.
    |
    */

    'company' => [
        'name' => env('COMPANY_NAME', 'Rehamed Fizjoterapia'),
        'nip' => env('COMPANY_NIP', ''),
        'address' => env('COMPANY_ADDRESS', ''),
        'city' => env('COMPANY_CITY', ''),
        'postal_code' => env('COMPANY_POSTAL_CODE', ''),
        'phone' => env('COMPANY_PHONE', ''),
        'email' => env('COMPANY_EMAIL', env('MAIL_FROM_ADDRESS')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    |
    | Settings for invoice generation.
    |
    */

    'invoice' => [
        'prefix' => env('INVOICE_PREFIX', 'INV'),
        'logo_path' => env('INVOICE_LOGO_PATH', null),
    ],

];
