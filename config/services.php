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

    /*
    |--------------------------------------------------------------------------
    | API Clients Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for external API clients. Each provider has its own
    | configuration section with credentials and settings.
    |
    */

    'api_clients' => [
        'jsonplaceholder' => [
            'base_url' => env('JSONPLACEHOLDER_BASE_URL', 'https://jsonplaceholder.typicode.com'),
            'token' => env('JSONPLACEHOLDER_TOKEN', ''),
            'timeout' => env('JSONPLACEHOLDER_TIMEOUT', 30),
            'retries' => env('JSONPLACEHOLDER_RETRIES', 3),
            'cache_ttl' => env('JSONPLACEHOLDER_CACHE_TTL', 300),
            'provider_name' => 'jsonplaceholder',
        ],
        'example_api' => [
            'base_url' => env('EXAMPLE_API_BASE_URL', 'https://api.example.com'),
            'token' => env('EXAMPLE_API_TOKEN', ''),
            'timeout' => env('EXAMPLE_API_TIMEOUT', 30),
            'retries' => env('EXAMPLE_API_RETRIES', 3),
            'cache_ttl' => env('EXAMPLE_API_CACHE_TTL', 300),
            'provider_name' => 'example_api',
        ],
        'node_api' => [
            'base_url' => env('NODE_API_BASE_URL', 'http://localhost:3000'),
            'token' => env('NODE_API_TOKEN', ''), // JWT será gerenciado automaticamente
            'timeout' => env('NODE_API_TIMEOUT', 30),
            'retries' => env('NODE_API_RETRIES', 3),
            'cache_ttl' => env('NODE_API_CACHE_TTL', 300),
            'provider_name' => 'node_api',
            'default_email' => env('NODE_API_DEFAULT_EMAIL', 'bruno@test.com'),
            'default_password' => env('NODE_API_DEFAULT_PASSWORD', 'senha123'),
        ],
    ],

    // Provider ativo - pode ser alterado via environment
    'api_provider' => env('API_PROVIDER', 'jsonplaceholder'),

];
