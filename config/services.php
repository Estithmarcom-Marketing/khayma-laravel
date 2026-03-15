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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'algolia' => [
        'app_id' => env('ALGOLIA_APP_ID'),
        'secret' => env('ALGOLIA_SECRET'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'payments_urls' => [
        'success' => env('PAYMENTS_SUCCESS_URL'),
        'cancel' => env('PAYMENTS_CANCEL_URL'),
        'failure' => env('PAYMENTS_FAILURE_URL'),
    ],
    'tabby' => [
        'merchant_code' => env('TABBY_ENV') === 'test'
            ? env('TABBY_TEST_MERCHANT_CODE')
            : env('TABBY_MERCHANT_CODE'),
        'secret_key' => env('TABBY_ENV') === 'test'
            ? env('TABBY_TEST_SECRET_KEY')
            : env('TABBY_SECRET_KEY'),
        'public_key' => env('TABBY_ENV') === 'test'
            ? env('TABBY_TEST_PUBLIC_KEY')
            : env('TABBY_PUBLIC_KEY'),

        'api_url' => env('TABBY_API_URL', 'https://api.tabby.ai/api/v2/checkout'),
        'allowed_ips' => explode(',', env('TABBY_WEBHOOK_IPS')),
    ],
    'tamara' => [
        'env' => env('TAMARA_ENV', 'sandbox'),

        'api_url' => env('TAMARA_ENV') === 'sandbox'
            ? env('TAMARA_SANDBOX_API_URL')
            : env('TAMARA_LIVE_API_URL'),

        'token' => env('TAMARA_ENV') === 'sandbox'
            ? env('TAMARA_SANDBOX_API_TOKEN')
            : env('TAMARA_API_TOKEN'),

        'notification_token' => env('TAMARA_ENV') === 'sandbox'
            ? env('TAMARA_SANDBOX_NOTIFICATION_TOKEN')
            : env('TAMARA_NOTIFICATION_TOKEN'),

        'public_key' => env('TAMARA_ENV') === 'sandbox'
            ? env('TAMARA_SANDBOX_PUBLIC_KEY')
            : env('TAMARA_PUBLIC_KEY'),

        'merchant_id' => env('TAMARA_MERCHANT_ID'),
        'notification_url' => env('TAMARA_NOTIFICATION_URL'),
    ],
    'myfatoorah' => [
        'api_url' => env('MYFATOORAH_API_URL'),
        'api_key' => env('MYFATOORAH_API_KEY'),
        'webhook_secret' => env('MYFATOORAH_WEBHOOK_SECRET'),
    ],
    'tqnyat' => [
        'api_url' => env('TQNYAT_API_URL'),
        'api_token' => env('TQNYAT_API_TOKEN'),
        'sender' => env('TQNYAT_SENDER'),
    ],

];
