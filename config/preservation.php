<?php

return [
    'watch' => [
        'include' => [
            'app/**',
            'config/**',
            'resources/views/proposicoes/assinatura/**',
            'resources/views/proposicoes/protocolo/**',
            'resources/views/proposicoes/legislativo/**',
            'resources/views/proposicoes/parlamentar/**',
            'resources/views/proposicoes/pdf/**',
            'resources/views/proposicoes/consulta/**',
        ],
        'exclude' => [
            // Views que mudam frequentemente e não precisam ser preservadas
            'resources/views/proposicoes/show.blade.php',
            'resources/views/proposicoes/**/show.blade.php',
            // Arquivos temporários e de debug
            'storage/logs/**',
            'storage/framework/**',
            'storage/app/public/**',
        ],
    ],
    
    // Evitar rodadas fantasma quando nada mudou
    'skip_if_no_changes' => true,
    
    // Arquivo para rastrear último manifesto
    'manifest_file' => storage_path('preservation/last-manifest.json'),
];