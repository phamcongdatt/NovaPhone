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

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI', env('APP_URL').'/auth/google/callback'),
        'guzzle' => [
            'verify' => storage_path('certs/cacert.pem'),
        ],
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI', env('APP_URL').'/auth/facebook/callback'),
        'guzzle' => [
            'verify' => storage_path('certs/cacert.pem'),
        ],
    ],

    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE'),
        'hash_secret' => env('VNPAY_HASH_SECRET'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'return_url' => env('VNPAY_RETURN_URL', env('APP_URL').'/checkout/vnpay/return'),
        'refund_url' => env('VNPAY_REFUND_URL', 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction'),
        'ca_bundle' => env('VNPAY_CA_BUNDLE', storage_path('certs/cacert.pem')),
        'server_ip' => env('VNPAY_SERVER_IP', '127.0.0.1'),
        'refund_query_interval_minutes' => (int) env('VNPAY_REFUND_QUERY_INTERVAL_MINUTES', 15),
        'refund_review_after_hours' => (int) env('VNPAY_REFUND_REVIEW_AFTER_HOURS', 24),
    ],
    'administrative' => [
        'base_url' => env('ADMINISTRATIVE_API_BASE_URL', 'https://provinces.open-api.vn/api/v2'),
        'source_version' => env('ADMINISTRATIVE_SOURCE_VERSION', '2025-07-01'),
        'ca_bundle' => env('ADMINISTRATIVE_CA_BUNDLE', storage_path('certs/cacert.pem')),
    ],
    'gemini' => ['key' => env('GEMINI_API_KEY')],

];
