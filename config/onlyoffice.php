<?php

return [
    'server_url' => env('ONLYOFFICE_SERVER_URL', 'http://localhost:8080'),
    'internal_url' => env('ONLYOFFICE_INTERNAL_URL', env('ONLYOFFICE_SERVER_URL', 'http://localhost:8080')),
    'jwt_secret' => env('ONLYOFFICE_JWT_SECRET'),
    'storage_path' => env('ONLYOFFICE_STORAGE_PATH', 'storage/onlyoffice'),
    'callback_url' => env('ONLYOFFICE_CALLBACK_URL'),
    
    'document_types' => [
        'text' => ['docx', 'doc', 'odt', 'rtf', 'txt'],
        'spreadsheet' => ['xlsx', 'xls', 'ods', 'csv'],
        'presentation' => ['pptx', 'ppt', 'odp']
    ],
    
    'default_permissions' => [
        'comment' => true,
        'copy' => true,
        'download' => true,
        'edit' => true,
        'fillForms' => true,
        'modifyFilter' => true,
        'modifyContentControl' => true,
        'review' => true,
        'chat' => true,
    ],
    
    'user_groups' => [
        'admin' => 'administrators',
        'legislativo' => 'legislative',
        'parlamentar' => 'parliamentarians',
        'assessor' => 'assistants'
    ],
    
    'locale' => [
        'lang' => 'pt-BR',
        'region' => 'pt-BR',
        'spellcheck' => ['pt-BR'],
        'timezone' => 'America/Sao_Paulo'
    ]
];