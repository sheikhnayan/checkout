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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'facebook' => [
        'client_id' => env('FACEBOOK_CLIENT_ID'),
        'client_secret' => env('FACEBOOK_CLIENT_SECRET'),
        'redirect' => env('FACEBOOK_REDIRECT_URI'),
    ],

    'recaptcha' => [
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'threshold' => env('RECAPTCHA_THRESHOLD', 0.5),
    ],

    'aloware' => [
        'api_key' => env('ALOWARE_API_KEY'),
        'api_url' => env('ALOWARE_API_URL', 'https://app.aloware.io/api/v1/webhook/sms-gateway/send'),
        'from_number' => env('ALOWARE_FROM_NUMBER'),
        'enabled' => env('ALOWARE_ENABLED', false),
        'default_country_code' => env('ALOWARE_DEFAULT_COUNTRY_CODE', '1'),
    ],

    'telnyx' => [
        'api_key' => env('TELNYX_API_KEY'),
        'api_url' => env('TELNYX_API_URL', 'https://api.telnyx.com/v2/messages'),
        'from_number' => env('TELNYX_FROM_NUMBER'), // Your Telnyx phone number in E.164 format (e.g., +15551234567)
        'enabled' => env('TELNYX_ENABLED', true),
        'default_country_code' => env('TELNYX_DEFAULT_COUNTRY_CODE', '1'), // 1=US, 44=UK, 33=France, 52=Mexico, 61=Australia, 971=UAE, etc
    ],

    'clublifter' => [
        'key' => env('CLUBLIFTER_API_KEY'),
        'base_url' => env('CLUBLIFTER_BASE_URL', 'https://www.clublifter.com'),
        'enabled' => env('CLUBLIFTER_ENABLED', true),
    ],

];
