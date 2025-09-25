<?php

return [
    /**
     * Perfis de assinatura digital para diferentes tipos de documento
     * Layout padronizado com faixa lateral direita + QR code
     */

    'legisinc_v2_lateral' => [
        'id' => 'legisinc_v2_lateral',
        'name' => 'LegisInc V2 - Layout Lateral Padrão',
        'description' => 'Faixa lateral direita (120pt) com texto vertical e QR code no rodapé',

        // Configurações da sidebar
        'sidebar_width_pt' => 120,
        'padding_pt' => 16,
        'qr_size_pt' => 88,
        'qr_margin_bottom_pt' => 16,
        'text_rotation_deg' => 90,

        // Coordenadas para A4 retrato (595×842 pt)
        'coordinates_a4' => [
            'sidebar' => ['x' => 475, 'y' => 0, 'w' => 120, 'h' => 842],
            'inner' => ['x' => 491, 'y' => 16, 'w' => 88, 'h' => 810],
            'qr' => ['x' => 491, 'y' => 16, 'w' => 88, 'h' => 88],
            'text_area' => ['x' => 491, 'y' => 120, 'w' => 88, 'h' => 706] // Área acima do QR
        ],

        // Elementos visuais
        'blocks' => [
            [
                'type' => 'vertical_text',
                'source' => 'template',
                'template' => '{{tipo}} Nº {{numero}}/{{ano}} - Protocolo nº {{protocolo}} recebido em {{data_hora}} - Esta é uma cópia do original assinado digitalmente por {{signatario}}. Para validar o documento, leia o código QR ou acesse {{url_short}} e informe o código {{codigo}}.',
                'font' => 'Helvetica',
                'font_size' => 8,
                'color' => '#333333',
                'align' => 'center',
                'area' => 'text_area',
                'rotation' => 90,
                'word_wrap' => true,
                'line_height' => 1.2
            ],
            [
                'type' => 'qrcode',
                'source' => 'url',
                'value' => '{{url_qr}}',
                'area' => 'qr',
                'error_correction' => 'M',
                'border' => 1,
                'quiet_zone' => 2
            ]
        ],

        // Configuração PAdES
        'pades' => [
            'visible_widget' => false, // Carimbo visual já está embutido
            'widget_rect' => [491, 140, 579, 200], // Fallback se precisar de widget
            'reason' => 'Assinatura digital de {{tipo}}',
            'location' => 'LegisInc - Sistema Legislativo',
            'contact_info' => null
        ],

        // Páginas que recebem carimbo
        'target_pages' => [1], // Apenas primeira página

        // Comportamento para diferentes orientações
        'page_handling' => [
            'portrait' => 'use_coordinates_a4',
            'landscape' => 'recalculate_proportional',
            'non_a4' => 'fixed_width_120pt'
        ]
    ],

    'legisinc_lei' => [
        'id' => 'legisinc_lei',
        'name' => 'LegisInc - Layout para Leis',
        'description' => 'Layout específico para documentos de Lei',

        'sidebar_width_pt' => 120,
        'padding_pt' => 16,
        'qr_size_pt' => 88,
        'qr_margin_bottom_pt' => 16,
        'text_rotation_deg' => 90,

        'coordinates_a4' => [
            'sidebar' => ['x' => 475, 'y' => 0, 'w' => 120, 'h' => 842],
            'inner' => ['x' => 491, 'y' => 16, 'w' => 88, 'h' => 810],
            'qr' => ['x' => 491, 'y' => 16, 'w' => 88, 'h' => 88],
            'text_area' => ['x' => 491, 'y' => 120, 'w' => 88, 'h' => 706]
        ],

        'blocks' => [
            [
                'type' => 'vertical_text',
                'source' => 'template',
                'template' => 'LEI Nº {{numero}}/{{ano}} - {{ementa_short}} - Assinado digitalmente por {{signatario}} em {{data_hora}}. Validação em {{url_short}} código {{codigo}}.',
                'font' => 'Helvetica',
                'font_size' => 7,
                'color' => '#1a1a1a',
                'align' => 'center',
                'area' => 'text_area',
                'rotation' => 90,
                'word_wrap' => true,
                'line_height' => 1.3
            ],
            [
                'type' => 'qrcode',
                'source' => 'url',
                'value' => '{{url_qr}}',
                'area' => 'qr',
                'error_correction' => 'M',
                'border' => 1,
                'quiet_zone' => 2
            ]
        ],

        'pades' => [
            'visible_widget' => false,
            'widget_rect' => [491, 140, 579, 200],
            'reason' => 'Sanção de Lei Municipal',
            'location' => 'Gabinete do Prefeito',
            'contact_info' => null
        ],

        'target_pages' => [1],

        'page_handling' => [
            'portrait' => 'use_coordinates_a4',
            'landscape' => 'recalculate_proportional',
            'non_a4' => 'fixed_width_120pt'
        ]
    ],

    'legisinc_indicacao' => [
        'id' => 'legisinc_indicacao',
        'name' => 'LegisInc - Layout para Indicações',
        'description' => 'Layout específico para documentos de Indicação',

        'sidebar_width_pt' => 120,
        'padding_pt' => 16,
        'qr_size_pt' => 88,
        'qr_margin_bottom_pt' => 16,
        'text_rotation_deg' => 90,

        'coordinates_a4' => [
            'sidebar' => ['x' => 475, 'y' => 0, 'w' => 120, 'h' => 842],
            'inner' => ['x' => 491, 'y' => 16, 'w' => 88, 'h' => 810],
            'qr' => ['x' => 491, 'y' => 16, 'w' => 88, 'h' => 88],
            'text_area' => ['x' => 491, 'y' => 120, 'w' => 88, 'h' => 706]
        ],

        'blocks' => [
            [
                'type' => 'vertical_text',
                'source' => 'template',
                'template' => 'INDICAÇÃO Nº {{numero}}/{{ano}} - Protocolo nº {{protocolo}} recebido em {{data_hora}} - Esta é uma cópia do original assinado digitalmente por {{signatario}}. Para validar o documento, leia o código QR ou acesse {{url_short}} e informe o código {{codigo}}.',
                'font' => 'Helvetica',
                'font_size' => 8,
                'color' => '#333333',
                'align' => 'center',
                'area' => 'text_area',
                'rotation' => 90,
                'word_wrap' => true,
                'line_height' => 1.2
            ],
            [
                'type' => 'qrcode',
                'source' => 'url',
                'value' => '{{url_qr}}',
                'area' => 'qr',
                'error_correction' => 'M',
                'border' => 1,
                'quiet_zone' => 2
            ]
        ],

        'pades' => [
            'visible_widget' => false,
            'widget_rect' => [491, 140, 579, 200],
            'reason' => 'Assinatura digital de Indicação',
            'location' => 'LegisInc - Sistema Legislativo',
            'contact_info' => null
        ],

        'target_pages' => [1],

        'page_handling' => [
            'portrait' => 'use_coordinates_a4',
            'landscape' => 'recalculate_proportional',
            'non_a4' => 'fixed_width_120pt'
        ]
    ],

    /**
     * Configurações globais
     */
    'global_settings' => [
        'default_profile' => 'legisinc_v2_lateral',
        'fallback_font' => 'Helvetica',
        'max_text_length' => 500,
        'qr_max_data_length' => 250,
        'cache_stamped_pdfs' => true,
        'cache_ttl_hours' => 24,
        'enable_thumbnails' => true,
        'thumbnail_dpi' => 150,
        'logs_enabled' => true,
        'lock_timeout_seconds' => 30
    ],

    /**
     * Mapeamento por tipo de proposição
     */
    'type_mapping' => [
        'lei' => 'legisinc_lei',
        'indicacao' => 'legisinc_indicacao',
        'requerimento' => 'legisinc_v2_lateral',
        'projeto_lei' => 'legisinc_v2_lateral',
        'default' => 'legisinc_v2_lateral'
    ]
];