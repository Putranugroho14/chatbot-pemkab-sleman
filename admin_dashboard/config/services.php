<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS, RAGFlow and more.
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

    'ragflow' => [
        'api_url' => env('RAGFLOW_API_URL', 'http://localhost:9380/api/v1'),
        'api_token' => env('RAGFLOW_API_TOKEN', ''),
        'agent_kominfo' => env('RAGFLOW_AGENT_KOMINFO', 'agent_kominfo_id_01'),
        'agent_pariwisata' => env('RAGFLOW_AGENT_PARIWISATA', 'agent_pariwisata_id_02'),
        'agent_dukcapil' => env('RAGFLOW_AGENT_DUKCAPIL', 'agent_dukcapil_id_03'),
    ],

    'sbot_api_url' => env('SBOT_API_URL', 'http://localhost:3000'),

];
