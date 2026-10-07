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
    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID', null),
        'project_id_production' => env('FCM_PROJECT_ID_PROD', null),
    ],

    'recaptcha' => [
        'secret' => env('SECRET_G_CAPTCHA'),
    ],

    'geetest' => [
        'key' => env('SECRET_G_CAPTCHA', null)
    ],

    'verihubs' => [
        'app_id' => env('VERIHUBS_APP_ID'),
        'api_key' => env('VERIHUBS_API_KEY'),
        'base_url' => env('VERIHUBS_BASE_URL', 'https://api.verihubs.com'),
        'otp_path' => '/v2/whatsapp/otp/send',
        'otp_template' => env('VERIHUBS_OTP_TEMPLATE', 'otp_tomi'),
        'lang_code' => env('VERIHUBS_LANG_CODE', 'id'),
        'time_limit' => env('VERIHUBS_TIME_LIMIT', 60),
    ],

    'buylink' => [
        'base_url' => env('BUYLINK_BASE_URL', 'https://mitra.buylink.id'),
        'user_token' => env('BUYLINK_USER_TOKEN'),
        'leader_token' => env('BUYLINK_LEADER_TOKEN'),
    ],

    'privy_digital_id' => [
        'application_id' => env('PRIVY_DIGITAL_ID_APPLICATION_ID'),
        'api_base_url' => env('PRIVY_DIGITAL_ID_API_BASE_URL', env('PRIVY_URL')),
        'web_base_url' => env('PRIVY_DIGITAL_ID_WEB_BASE_URL', 'https://stg-digitalid.privy.id'),
        'client_id' => env('PRIVY_DIGITAL_ID_CLIENT_ID'),
        'client_secret' => env('PRIVY_DIGITAL_ID_CLIENT_SECRET'),
        'private_key' => env('PRIVY_DIGITAL_ID_PRIVATE_KEY'),
        'public_key' => env('PRIVY_DIGITAL_ID_PUBLIC_KEY'),
        'merchant_id' => env('PRIVY_DIGITAL_ID_MERCHANT_ID'),
        'merchant_api_key' => env('PRIVY_DIGITAL_ID_MERCHANT_API_KEY'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
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

];
