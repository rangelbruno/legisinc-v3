<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuração centralizada para gerenciar APIs do sistema.
    | Facilita a troca entre mock (desenvolvimento) e APIs externas (produção).
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Modo da API
    |--------------------------------------------------------------------------
    |
    | Define qual tipo de API usar:
    | 'mock' - Usa MockApiController interno (recomendado para desenvolvimento)
    | 'external' - Usa API externa (para produção)
    |
    */
    'mode' => env('API_MODE', 'mock'),

    /*
    |--------------------------------------------------------------------------
    | Configurações do Mock
    |--------------------------------------------------------------------------
    |
    | Configurações quando usando modo 'mock'
    |
    */
    'mock' => [
        'enabled' => true,
        'base_url' => env('MOCK_API_BASE_URL', env('APP_URL', 'http://localhost:8000') . '/api/mock-api'),
        'description' => 'Mock API interno - Laravel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações da API Externa
    |--------------------------------------------------------------------------
    |
    | Configurações quando usando modo 'external'
    |
    */
    'external' => [
        'base_url' => env('EXTERNAL_API_URL', 'http://localhost:3000'),
        'timeout' => env('EXTERNAL_API_TIMEOUT', 30),
        'retries' => env('EXTERNAL_API_RETRIES', 3),
        'description' => 'API Externa - Node.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações Gerais
    |--------------------------------------------------------------------------
    |
    | Configurações que se aplicam a ambos os modos
    |
    */
    'cache_ttl' => env('API_CACHE_TTL', 300),
    'default_credentials' => [
        'email' => env('API_DEFAULT_EMAIL', 'bruno@test.com'),
        'password' => env('API_DEFAULT_PASSWORD', 'senha123'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configurações Avançadas
    |--------------------------------------------------------------------------
    */
    'logging' => [
        'enabled' => env('API_LOGGING_ENABLED', true),
        'level' => env('API_LOGGING_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | URLs Disponíveis por Modo
    |--------------------------------------------------------------------------
    |
    | URLs que serão usadas dependendo do modo selecionado
    |
    */
    'urls' => [
        'mock' => [
            'health' => '/api/mock-api/',
            'register' => '/api/mock-api/register',
            'login' => '/api/mock-api/login',
            'users' => '/api/mock-api/users',
        ],
        'external' => [
            'health' => '/',
            'register' => '/register',
            'login' => '/login',
            'users' => '/users',
        ],
    ],
]; 