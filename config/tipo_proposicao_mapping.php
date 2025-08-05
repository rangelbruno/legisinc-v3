<?php

return [
    'mappings' => [
        // Proposta de Emenda à Constituição / Lei Orgânica
        'pec' => [
            'nome' => 'Proposta de Emenda à Constituição',
            'codigo' => 'proposta_emenda_constituicao',
            'icone' => 'ki-shield-tick',
            'cor' => 'danger',
            'ordem' => 1,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'quorum_especial' => '3/5',
                'tramitacao_especial' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'texto_integral'],
                'prazos' => ['discussao' => 60, 'emendas' => 30]
            ]
        ],
        'pelom' => [
            'nome' => 'Proposta de Emenda à Lei Orgânica Municipal',
            'codigo' => 'proposta_emenda_lei_organica',
            'icone' => 'ki-shield-tick',
            'cor' => 'danger',
            'ordem' => 2,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'quorum_especial' => '2/3',
                'tramitacao_especial' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'texto_integral'],
                'prazos' => ['discussao' => 60, 'emendas' => 30]
            ]
        ],
        
        // Projetos de Lei
        'pl' => [
            'nome' => 'Projeto de Lei Ordinária',
            'codigo' => 'projeto_lei_ordinaria',
            'icone' => 'ki-document',
            'cor' => 'primary',
            'ordem' => 3,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'tramitacao_obrigatoria' => true,
                'quorum' => 'maioria_simples',
                'campos_obrigatorios' => ['ementa', 'justificativa'],
                'prazos' => ['apresentacao' => 30, 'emendas' => 15]
            ]
        ],
        'plc' => [
            'nome' => 'Projeto de Lei Complementar',
            'codigo' => 'projeto_lei_complementar',
            'icone' => 'ki-document-edit',
            'cor' => 'info',
            'ordem' => 4,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'quorum_especial' => 'maioria_absoluta',
                'tramitacao_obrigatoria' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'base_legal'],
                'prazos' => ['apresentacao' => 45, 'emendas' => 20]
            ]
        ],
        'plp' => [
            'nome' => 'Projeto de Lei Complementar',
            'codigo' => 'projeto_lei_complementar',
            'icone' => 'ki-document-edit',
            'cor' => 'info',
            'ordem' => 4,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'quorum_especial' => 'maioria_absoluta',
                'tramitacao_obrigatoria' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'base_legal'],
                'prazos' => ['apresentacao' => 45, 'emendas' => 20]
            ]
        ],
        'pld' => [
            'nome' => 'Projeto de Lei Delegada',
            'codigo' => 'projeto_lei_delegada',
            'icone' => 'ki-document-folder',
            'cor' => 'secondary',
            'ordem' => 5,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'iniciativa' => 'executivo',
                'delegacao_legislativa' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'termo_delegacao']
            ]
        ],
        
        // Medida Provisória
        'mp' => [
            'nome' => 'Medida Provisória',
            'codigo' => 'medida_provisoria',
            'icone' => 'ki-time',
            'cor' => 'warning',
            'ordem' => 6,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'iniciativa_exclusiva' => 'executivo',
                'prazo_vigencia' => 120,
                'forca_lei' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'urgencia_relevancia']
            ]
        ],
        
        // Projetos Especiais
        'pdl' => [
            'nome' => 'Projeto de Decreto Legislativo',
            'codigo' => 'projeto_decreto_legislativo',
            'icone' => 'ki-shield-search',
            'cor' => 'success',
            'ordem' => 7,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'competencia_exclusiva' => true,
                'efeitos_externos' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa'],
                'tipos' => ['contas_prefeito', 'concessao_titulo', 'autorizacao']
            ]
        ],
        'pdc' => [
            'nome' => 'Projeto de Decreto do Congresso',
            'codigo' => 'projeto_decreto_congresso',
            'icone' => 'ki-shield-search',
            'cor' => 'success',
            'ordem' => 8,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'competencia_exclusiva' => true,
                'efeitos_externos' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa']
            ]
        ],
        'pr' => [
            'nome' => 'Projeto de Resolução',
            'codigo' => 'projeto_resolucao',
            'icone' => 'ki-home',
            'cor' => 'dark',
            'ordem' => 9,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'efeitos_internos' => true,
                'materia' => ['regimento_interno', 'organizacao_administrativa'],
                'campos_obrigatorios' => ['ementa', 'justificativa']
            ]
        ],
        
        // Requerimentos
        'req' => [
            'nome' => 'Requerimento',
            'codigo' => 'requerimento',
            'icone' => 'ki-questionnaire-tablet',
            'cor' => 'info',
            'ordem' => 10,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'resposta_obrigatoria' => true,
                'prazo_resposta' => 30,
                'campos_obrigatorios' => ['ementa', 'justificativa'],
                'subtipos' => [
                    'informacao' => 'Requerimento de Informação',
                    'urgencia' => 'Requerimento de Urgência',
                    'licenca' => 'Requerimento de Licença',
                    'cpi' => 'Requerimento de CPI',
                    'audiencia' => 'Requerimento de Audiência Pública',
                    'convocacao' => 'Requerimento de Convocação',
                    'voto_louvor' => 'Requerimento de Voto de Louvor',
                    'voto_pesar' => 'Requerimento de Voto de Pesar'
                ]
            ]
        ],
        
        // Indicações
        'ind' => [
            'nome' => 'Indicação',
            'codigo' => 'indicacao',
            'icone' => 'ki-send',
            'cor' => 'primary',
            'ordem' => 11,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'destinatario' => 'executivo',
                'carater' => 'sugestivo',
                'campos_obrigatorios' => ['ementa', 'justificativa']
            ]
        ],
        
        // Moções
        'moc' => [
            'nome' => 'Moção',
            'codigo' => 'mocao',
            'icone' => 'ki-message-text-2',
            'cor' => 'warning',
            'ordem' => 12,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'tramitacao_simplificada' => true,
                'campos_obrigatorios' => ['ementa'],
                'tipos' => ['aplauso', 'pesar', 'repudio', 'louvor', 'congratulacao', 'apoio']
            ]
        ],
        
        // Emendas
        'eme' => [
            'nome' => 'Emenda',
            'codigo' => 'emenda',
            'icone' => 'ki-pencil',
            'cor' => 'secondary',
            'ordem' => 13,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'vinculada_proposicao' => true,
                'campos_obrigatorios' => ['texto', 'justificativa'],
                'tipos' => ['supressiva', 'aditiva', 'substitutiva', 'modificativa', 'aglutinativa']
            ]
        ],
        'sub' => [
            'nome' => 'Subemenda',
            'codigo' => 'subemenda',
            'icone' => 'ki-pencil',
            'cor' => 'light',
            'ordem' => 14,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'vinculada_emenda' => true,
                'campos_obrigatorios' => ['texto', 'justificativa']
            ]
        ],
        'substitutivo' => [
            'nome' => 'Substitutivo',
            'codigo' => 'substitutivo',
            'icone' => 'ki-arrows-circle',
            'cor' => 'info',
            'ordem' => 15,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'texto_integral' => true,
                'vinculada_proposicao' => true,
                'campos_obrigatorios' => ['texto_integral', 'justificativa']
            ]
        ],
        
        // Pareceres e Relatórios
        'par' => [
            'nome' => 'Parecer de Comissão',
            'codigo' => 'parecer_comissao',
            'icone' => 'ki-clipboard-check',
            'cor' => 'success',
            'ordem' => 16,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'vinculado_comissao' => true,
                'campos_obrigatorios' => ['parecer', 'conclusao'],
                'tipos' => ['constitucionalidade', 'merito', 'financas', 'redacao_final']
            ]
        ],
        'rel' => [
            'nome' => 'Relatório',
            'codigo' => 'relatorio',
            'icone' => 'ki-document-text',
            'cor' => 'dark',
            'ordem' => 17,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'campos_obrigatorios' => ['conteudo'],
                'tipos' => ['cpi', 'comissao_especial', 'comissao_mista']
            ]
        ],
        
        // Outros
        'rec' => [
            'nome' => 'Recurso',
            'codigo' => 'recurso',
            'icone' => 'ki-arrow-circle-right',
            'cor' => 'danger',
            'ordem' => 18,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'prazo_interposicao' => 5,
                'campos_obrigatorios' => ['ementa', 'fundamentacao'],
                'contra' => ['decisao_mesa', 'decisao_comissao', 'decisao_presidencia']
            ]
        ],
        'veto' => [
            'nome' => 'Veto',
            'codigo' => 'veto',
            'icone' => 'ki-cross-circle',
            'cor' => 'danger',
            'ordem' => 19,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'origem' => 'executivo',
                'prazo_apreciacao' => 30,
                'campos_obrigatorios' => ['razoes_veto'],
                'tipos' => ['total', 'parcial']
            ]
        ],
        'destaque' => [
            'nome' => 'Destaque',
            'codigo' => 'destaque',
            'icone' => 'ki-filter-search',
            'cor' => 'warning',
            'ordem' => 20,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'votacao_separada' => true,
                'campos_obrigatorios' => ['dispositivo', 'justificativa']
            ]
        ],
        'ofi' => [
            'nome' => 'Ofício',
            'codigo' => 'oficio',
            'icone' => 'ki-sms',
            'cor' => 'primary',
            'ordem' => 21,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'comunicacao_oficial' => true,
                'campos_obrigatorios' => ['destinatario', 'assunto', 'conteudo']
            ]
        ],
        'msg' => [
            'nome' => 'Mensagem do Executivo',
            'codigo' => 'mensagem_executivo',
            'icone' => 'ki-message-programming',
            'cor' => 'info',
            'ordem' => 22,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'origem' => 'executivo',
                'campos_obrigatorios' => ['assunto', 'conteudo'],
                'tipos' => ['envio_projeto', 'sancao', 'veto', 'informacao']
            ]
        ],
        
        // Projeto de Consolidação
        'pcl' => [
            'nome' => 'Projeto de Consolidação das Leis',
            'codigo' => 'projeto_consolidacao_leis',
            'icone' => 'ki-book',
            'cor' => 'secondary',
            'ordem' => 23,
            'configuracoes' => [
                'numeracao_automatica' => true,
                'reuniao_diplomas' => true,
                'campos_obrigatorios' => ['ementa', 'justificativa', 'leis_consolidadas']
            ]
        ]
    ],
    
    // Variações e sinônimos adicionais
    'aliases' => [
        // Variações de PEC/PELOM
        'proposta de emenda' => 'pec',
        'emenda constitucional' => 'pec',
        'emenda à constituição' => 'pec',
        'emenda lei organica' => 'pelom',
        'emenda à lei orgânica' => 'pelom',
        
        // Variações de Projeto de Lei
        'projeto de lei' => 'pl',
        'projeto lei' => 'pl',
        'lei ordinaria' => 'pl',
        'lei ordinária' => 'pl',
        'lei complementar' => 'plc',
        'projeto lei complementar' => 'plc',
        'lei delegada' => 'pld',
        
        // Variações de Decreto Legislativo
        'decreto legislativo' => 'pdl',
        'projeto decreto' => 'pdl',
        'decreto do congresso' => 'pdc',
        
        // Variações de Resolução
        'resolucao' => 'pr',
        'resolução' => 'pr',
        'projeto resolucao' => 'pr',
        'projeto resolução' => 'pr',
        
        // Variações de Moção
        'mocao' => 'moc',
        'moção' => 'moc',
        'mocao aplauso' => 'moc',
        'mocao pesar' => 'moc',
        'mocao repudio' => 'moc',
        'moção aplauso' => 'moc',
        'moção pesar' => 'moc',
        'moção repúdio' => 'moc',
        'mocao louvor' => 'moc',
        'mocao congratulacao' => 'moc',
        'moção louvor' => 'moc',
        'moção congratulação' => 'moc',
        
        // Variações de Requerimento
        'requerimento' => 'req',
        'requerimento informacao' => 'req',
        'requerimento informação' => 'req',
        'requerimento urgencia' => 'req',
        'requerimento urgência' => 'req',
        'requerimento cpi' => 'req',
        'requerimento audiencia' => 'req',
        'requerimento audiência' => 'req',
        
        // Variações de Indicação
        'indicacao' => 'ind',
        'indicação' => 'ind',
        
        // Variações de Emenda
        'emenda' => 'eme',
        'emenda supressiva' => 'eme',
        'emenda aditiva' => 'eme',
        'emenda substitutiva' => 'eme',
        'emenda modificativa' => 'eme',
        'subemenda' => 'sub',
        
        // Variações de Parecer
        'parecer' => 'par',
        'parecer comissao' => 'par',
        'parecer comissão' => 'par',
        
        // Variações de Relatório
        'relatorio' => 'rel',
        'relatório' => 'rel',
        'relatorio cpi' => 'rel',
        'relatório cpi' => 'rel',
        
        // Outros
        'oficio' => 'ofi',
        'ofício' => 'ofi',
        'mensagem' => 'msg',
        'mensagem executivo' => 'msg',
        'medida provisoria' => 'mp',
        'medida provisória' => 'mp'
    ]
];