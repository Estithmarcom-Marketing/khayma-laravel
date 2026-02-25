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
        'merchant_code' => env('TABBY_MERCHANT_CODE'),
        'secret_key' => env('TABBY_SECRET_KEY'),
        'api_url' => env('TABBY_API_URL', 'https://api.tabby.ai/api/v2/checkout'),
        'allowed_ips' => explode(',', env('TABBY_WEBHOOK_IPS')),
    ],
    'tamara' => [
        'env' => env('TAMARA_ENV', 'sandbox'),

        'base_url' => env('TAMARA_ENV') === 'sandbox'
            // ? env('TAMARA_SANDBOX_BASE_URL')
            ? 'https://api-sandbox.tamara.co/checkout'
            : env('TAMARA_LIVE_BASE_URL'),

        'token' => env('TAMARA_ENV') === 'sandbox'
            // ? env('TAMARA_SANDBOX_API_TOKEN')
            ? 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhY2NvdW50SWQiOiI4NGYwMmExYi0yZWRlLTRhMzUtOGNiNi0xY2UwZmMwZmNlYWUiLCJ0eXBlIjoibWVyY2hhbnQiLCJzYWx0IjoiM2RlNmQ5NWMzOWVlMzk3YTk4ZDA1MTUwM2QyOTMxYzgiLCJyb2xlcyI6WyJST0xFX01FUkNIQU5UIl0sImlhdCI6MTc3MTQ4MzY0NywiaXNzIjoiVGFtYXJhIn0.onZqons4s4HfbGuX67T1i6Wwn2bW9_4WKkbkokMu_vwiGhwvHu34MweGAYi1xJguaMZA8WariNrA07XncZyuxBLd4fSIEUdH3zqrsz3wUMIM-9bUyYc2jP9sMr5y_I6obH-fE9SUf-43aYXqejfzdSXVyTQ7JraGG4HzoMokIX3eYmz0EIm_mbJJPtnFEAM5Ie6dtCvStVEwgRWu-ULnJ-5s2mlw7MeCwVVpLbI-Kl90rMGV8_OSaQH3AbiZUQJKfc-b6w3L6S5nKuSjObGqnoCpDqwgK_fbkkqFrAOsuKdo2S-TNFqNwTZgwi7yv5XJ8oRq1VZPx9tGHwiB5KTvrw'
            : env('TAMARA_API_TOKEN'),

        'notification_token' => env('TAMARA_ENV') === 'sandbox'
            // ? env('TAMARA_SANDBOX_NOTIFICATION_TOKEN')
            ? 'ce26ecae-fce8-42a7-957e-ccdec1463978'
            : env('TAMARA_NOTIFICATION_TOKEN'),

        'public_key' => env('TAMARA_ENV') === 'sandbox'
            // ? env('TAMARA_SANDBOX_PUBLIC_KEY')
            ? 'fdb31b16-24b2-41e3-9f42-bbe9834464dc'
            : env('TAMARA_PUBLIC_KEY'),

        'merchant_id' => env('TAMARA_MERCHANT_ID'),
        'notification_url' => env('TAMARA_NOTIFICATION_URL'),
    ],

];
