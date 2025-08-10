<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Parametro\ParametroService;

class TemplateProposicaoParametroSeeder extends Seeder
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    public function run(): void
    {
        // Criar módulo "Templates"
        $moduloData = [
            'nome' => 'Templates',
            'descricao' => 'Configurações de templates para proposições legislativas',
            'icon' => 'ki-document',
            'ordem' => 5,
            'ativo' => true
        ];

        $modulo = $this->parametroService->criarModulo($moduloData);

        // Criar submódulo "Cabeçalho"
        $submoduloData = [
            'modulo_id' => $modulo->id,
            'nome' => 'Cabeçalho',
            'descricao' => 'Configurações do cabeçalho padrão das proposições',
            'tipo' => 'form',
            'ordem' => 1,
            'ativo' => true
        ];

        $submodulo = $this->parametroService->criarSubmodulo($submoduloData);

        // Criar campos do submódulo
        $campos = [
            [
                'nome' => 'cabecalho_imagem',
                'label' => 'Imagem do Cabeçalho',
                'descricao' => 'Imagem utilizada no cabeçalho das proposições',
                'tipo_campo' => 'file',
                'valor_padrao' => 'template/cabecalho.png',
                'obrigatorio' => true,
                'ordem' => 1,
                'placeholder' => 'Selecione uma imagem PNG ou JPG',
                'validacao' => [
                    'accepted_types' => ['image/png', 'image/jpeg', 'image/jpg'],
                    'max_size' => 2048 // 2MB
                ],
                'opcoes' => [
                    'storage_path' => 'public/template',
                    'default_file' => 'template/cabecalho.png'
                ]
            ],
            [
                'nome' => 'usar_cabecalho_padrao',
                'label' => 'Usar Cabeçalho Padrão',
                'descricao' => 'Aplicar automaticamente o cabeçalho padrão em todas as proposições',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'ordem' => 2,
                'placeholder' => 'Ativar cabeçalho automático'
            ],
            [
                'nome' => 'cabecalho_altura',
                'label' => 'Altura do Cabeçalho',
                'descricao' => 'Altura do cabeçalho em pixels',
                'tipo_campo' => 'number',
                'valor_padrao' => '150',
                'obrigatorio' => true,
                'ordem' => 3,
                'placeholder' => 'Altura em pixels (ex: 150)',
                'validacao' => [
                    'min' => 50,
                    'max' => 300
                ]
            ],
            [
                'nome' => 'cabecalho_posicao',
                'label' => 'Posição do Cabeçalho',
                'descricao' => 'Posição do cabeçalho no documento',
                'tipo_campo' => 'select',
                'valor_padrao' => 'topo',
                'obrigatorio' => true,
                'ordem' => 4,
                'placeholder' => 'Selecione a posição',
                'opcoes' => [
                    'topo' => 'Topo do documento',
                    'header' => 'Cabeçalho da página',
                    'marca_dagua' => 'Marca d\'água'
                ]
            ]
        ];

        foreach ($campos as $campoData) {
            $campoData['submodulo_id'] = $submodulo->id;
            $this->parametroService->criarCampo($campoData);
        }

        // Criar submódulo "Marca D'água"
        $submoduloMarcaDagua = [
            'modulo_id' => $modulo->id,
            'nome' => 'Marca D\'água',
            'descricao' => 'Configurações da marca d\'água dos documentos',
            'tipo' => 'form',
            'ordem' => 2,
            'ativo' => true
        ];

        $submodulo2 = $this->parametroService->criarSubmodulo($submoduloMarcaDagua);

        // Criar campos do submódulo Marca D'água
        $camposMarcaDagua = [
            [
                'nome' => 'usar_marca_dagua',
                'label' => 'Usar Marca D\'água',
                'descricao' => 'Aplicar marca d\'água nos documentos',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '0',
                'obrigatorio' => false,
                'ordem' => 1,
                'placeholder' => 'Ativar marca d\'água'
            ],
            [
                'nome' => 'marca_dagua_tipo',
                'label' => 'Tipo de Marca D\'água',
                'descricao' => 'Tipo da marca d\'água: imagem ou texto',
                'tipo_campo' => 'select',
                'valor_padrao' => 'imagem',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => 'Selecione o tipo',
                'opcoes' => [
                    'imagem' => 'Imagem',
                    'texto' => 'Texto'
                ]
            ],
            [
                'nome' => 'marca_dagua_texto',
                'label' => 'Texto da Marca D\'água',
                'descricao' => 'Texto usado como marca d\'água',
                'tipo_campo' => 'text',
                'valor_padrao' => 'CONFIDENCIAL',
                'obrigatorio' => false,
                'ordem' => 3,
                'placeholder' => 'Ex: CONFIDENCIAL'
            ],
            [
                'nome' => 'marca_dagua_opacidade',
                'label' => 'Opacidade',
                'descricao' => 'Opacidade da marca d\'água (10-100%)',
                'tipo_campo' => 'number',
                'valor_padrao' => '30',
                'obrigatorio' => true,
                'ordem' => 4,
                'placeholder' => '30',
                'validacao' => [
                    'min' => 10,
                    'max' => 100
                ]
            ],
            [
                'nome' => 'marca_dagua_posicao',
                'label' => 'Posição',
                'descricao' => 'Posição da marca d\'água no documento',
                'tipo_campo' => 'select',
                'valor_padrao' => 'centro',
                'obrigatorio' => true,
                'ordem' => 5,
                'placeholder' => 'Selecione a posição',
                'opcoes' => [
                    'centro' => 'Centro',
                    'superior_direita' => 'Superior Direita',
                    'superior_esquerda' => 'Superior Esquerda',
                    'inferior_direita' => 'Inferior Direita',
                    'inferior_esquerda' => 'Inferior Esquerda'
                ]
            ],
            [
                'nome' => 'marca_dagua_tamanho',
                'label' => 'Tamanho',
                'descricao' => 'Tamanho da marca d\'água em pixels',
                'tipo_campo' => 'number',
                'valor_padrao' => '100',
                'obrigatorio' => true,
                'ordem' => 6,
                'placeholder' => '100',
                'validacao' => [
                    'min' => 50,
                    'max' => 300
                ]
            ]
        ];

        foreach ($camposMarcaDagua as $campoData) {
            $campoData['submodulo_id'] = $submodulo2->id;
            $this->parametroService->criarCampo($campoData);
        }

        // Criar submódulo "Texto Padrão"
        $submoduloTextoPadrao = [
            'modulo_id' => $modulo->id,
            'nome' => 'Texto Padrão',
            'descricao' => 'Configurações de texto padrão dos documentos',
            'tipo' => 'form',
            'ordem' => 3,
            'ativo' => true
        ];

        $submodulo3 = $this->parametroService->criarSubmodulo($submoduloTextoPadrao);

        // Criar campos do submódulo Texto Padrão
        $camposTextoPadrao = [
            [
                'nome' => 'usar_texto_padrao',
                'label' => 'Usar Texto Padrão',
                'descricao' => 'Aplicar textos padrão nos documentos',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '0',
                'obrigatorio' => false,
                'ordem' => 1,
                'placeholder' => 'Ativar texto padrão'
            ],
            [
                'nome' => 'texto_introducao',
                'label' => 'Texto de Introdução',
                'descricao' => 'Texto padrão para introdução dos documentos',
                'tipo_campo' => 'textarea',
                'valor_padrao' => 'Este documento apresenta proposta de lei que visa...',
                'obrigatorio' => false,
                'ordem' => 2,
                'placeholder' => 'Texto de introdução',
                'validacao' => [
                    'max_length' => 1000
                ]
            ],
            [
                'nome' => 'texto_justificativa',
                'label' => 'Texto de Justificativa',
                'descricao' => 'Texto padrão para justificativa dos documentos',
                'tipo_campo' => 'textarea',
                'valor_padrao' => 'A presente proposição justifica-se pela necessidade de...',
                'obrigatorio' => false,
                'ordem' => 3,
                'placeholder' => 'Texto de justificativa',
                'validacao' => [
                    'max_length' => 2000
                ]
            ],
            [
                'nome' => 'texto_conclusao',
                'label' => 'Texto de Conclusão',
                'descricao' => 'Texto padrão para conclusão dos documentos',
                'tipo_campo' => 'textarea',
                'valor_padrao' => 'Diante do exposto, submetemos esta proposição à apreciação dos nobres pares desta Casa.',
                'obrigatorio' => false,
                'ordem' => 4,
                'placeholder' => 'Texto de conclusão',
                'validacao' => [
                    'max_length' => 1000
                ]
            ],
            [
                'nome' => 'assinatura_cargo',
                'label' => 'Cargo para Assinatura',
                'descricao' => 'Cargo padrão para assinatura dos documentos',
                'tipo_campo' => 'text',
                'valor_padrao' => 'Vereador(a)',
                'obrigatorio' => false,
                'ordem' => 5,
                'placeholder' => 'Ex: Vereador(a)'
            ],
            [
                'nome' => 'assinatura_nome',
                'label' => 'Nome para Assinatura',
                'descricao' => 'Nome padrão para assinatura dos documentos',
                'tipo_campo' => 'text',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 6,
                'placeholder' => 'Nome do responsável'
            ],
            [
                'nome' => 'assinatura_departamento',
                'label' => 'Departamento',
                'descricao' => 'Departamento ou órgão responsável',
                'tipo_campo' => 'text',
                'valor_padrao' => 'Câmara Municipal',
                'obrigatorio' => false,
                'ordem' => 7,
                'placeholder' => 'Ex: Câmara Municipal'
            ]
        ];

        foreach ($camposTextoPadrao as $campoData) {
            $campoData['submodulo_id'] = $submodulo3->id;
            $this->parametroService->criarCampo($campoData);
        }

        // Criar submódulo "Rodapé"
        $submoduloRodape = [
            'modulo_id' => $modulo->id,
            'nome' => 'Rodapé',
            'descricao' => 'Configurações do rodapé dos documentos',
            'tipo' => 'form',
            'ordem' => 4,
            'ativo' => true
        ];

        $submodulo4 = $this->parametroService->criarSubmodulo($submoduloRodape);

        // Criar campos do submódulo Rodapé
        $camposRodape = [
            [
                'nome' => 'usar_rodape',
                'label' => 'Usar Rodapé',
                'descricao' => 'Aplicar rodapé padrão nos documentos',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'ordem' => 1,
                'placeholder' => 'Ativar rodapé automático'
            ],
            [
                'nome' => 'rodape_tipo',
                'label' => 'Tipo de Rodapé',
                'descricao' => 'Tipo do rodapé: texto ou imagem',
                'tipo_campo' => 'select',
                'valor_padrao' => 'texto',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => 'Selecione o tipo',
                'opcoes' => [
                    'texto' => 'Texto',
                    'imagem' => 'Imagem',
                    'misto' => 'Texto + Imagem'
                ]
            ],
            [
                'nome' => 'rodape_texto',
                'label' => 'Texto do Rodapé',
                'descricao' => 'Texto padrão do rodapé dos documentos',
                'tipo_campo' => 'textarea',
                'valor_padrao' => 'Este documento foi gerado automaticamente pelo Sistema Legislativo.',
                'obrigatorio' => false,
                'ordem' => 3,
                'placeholder' => 'Texto do rodapé',
                'validacao' => [
                    'max_length' => 500
                ]
            ],
            [
                'nome' => 'rodape_imagem',
                'label' => 'Imagem do Rodapé',
                'descricao' => 'Imagem utilizada no rodapé dos documentos',
                'tipo_campo' => 'file',
                'valor_padrao' => 'template/rodape.png',
                'obrigatorio' => false,
                'ordem' => 4,
                'placeholder' => 'Selecione uma imagem PNG ou JPG',
                'validacao' => [
                    'accepted_types' => ['image/png', 'image/jpeg', 'image/jpg'],
                    'max_size' => 2048 // 2MB
                ],
                'opcoes' => [
                    'storage_path' => 'public/template',
                    'default_file' => 'template/rodape.png'
                ]
            ],
            [
                'nome' => 'rodape_posicao',
                'label' => 'Posição do Rodapé',
                'descricao' => 'Posição do rodapé no documento',
                'tipo_campo' => 'select',
                'valor_padrao' => 'rodape',
                'obrigatorio' => true,
                'ordem' => 5,
                'placeholder' => 'Selecione a posição',
                'opcoes' => [
                    'rodape' => 'Rodapé da página',
                    'final' => 'Final do documento',
                    'todas_paginas' => 'Todas as páginas'
                ]
            ],
            [
                'nome' => 'rodape_alinhamento',
                'label' => 'Alinhamento',
                'descricao' => 'Alinhamento do conteúdo do rodapé',
                'tipo_campo' => 'select',
                'valor_padrao' => 'centro',
                'obrigatorio' => true,
                'ordem' => 6,
                'placeholder' => 'Selecione o alinhamento',
                'opcoes' => [
                    'esquerda' => 'Esquerda',
                    'centro' => 'Centro',
                    'direita' => 'Direita'
                ]
            ],
            [
                'nome' => 'rodape_numeracao',
                'label' => 'Incluir Numeração',
                'descricao' => 'Incluir numeração de páginas no rodapé',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'ordem' => 7,
                'placeholder' => 'Ativar numeração de páginas'
            ]
        ];

        foreach ($camposRodape as $campoData) {
            $campoData['submodulo_id'] = $submodulo4->id;
            $this->parametroService->criarCampo($campoData);
        }

        // Verificar se o módulo "Dados Gerais" já existe
        $moduloDadosGerais = \App\Models\Parametro\ParametroModulo::where('nome', 'Dados Gerais')->first();
        if (!$moduloDadosGerais) {
            // Criar módulo "Dados Gerais" se não existir
            $moduloDadosGeraisData = [
                'nome' => 'Dados Gerais',
                'descricao' => 'Configurações gerais da Câmara Municipal',
                'icon' => 'ki-office-bag',
                'ordem' => 1,
                'ativo' => true
            ];
            $moduloDadosGerais = $this->parametroService->criarModulo($moduloDadosGeraisData);
        }

        // Criar submódulo "Dados da Câmara" no módulo Dados Gerais
        $submoduloDadosCamara = [
            'modulo_id' => $moduloDadosGerais->id,
            'nome' => 'Dados da Câmara',
            'descricao' => 'Informações institucionais da Câmara Municipal para uso em templates',
            'tipo' => 'form',
            'ordem' => 1,
            'ativo' => true
        ];

        $submodulo5 = $this->parametroService->criarSubmodulo($submoduloDadosCamara);

        // Criar campos do submódulo Dados da Câmara
        $camposDadosCamara = [
            [
                'nome' => 'nome_camara',
                'label' => 'Nome da Câmara',
                'descricao' => 'Nome oficial da Câmara Municipal (variável: ${nome_camara})',
                'tipo_campo' => 'text',
                'valor_padrao' => 'CÂMARA MUNICIPAL DE SÃO PAULO',
                'obrigatorio' => true,
                'ordem' => 1,
                'placeholder' => 'Ex: CÂMARA MUNICIPAL DE SÃO PAULO'
            ],
            [
                'nome' => 'endereco_completo',
                'label' => 'Endereço Completo',
                'descricao' => 'Endereço completo da Câmara Municipal (variável: ${endereco_completo})',
                'tipo_campo' => 'textarea',
                'valor_padrao' => 'Viaduto Jacareí, 100, Bela Vista - CEP: 01319-900\nSão Paulo/SP',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => 'Endereço completo com CEP e cidade'
            ],
            [
                'nome' => 'endereco_cep',
                'label' => 'CEP',
                'descricao' => 'CEP da Câmara Municipal (variável: ${endereco_cep})',
                'tipo_campo' => 'text',
                'valor_padrao' => '01319-900',
                'obrigatorio' => true,
                'ordem' => 3,
                'placeholder' => '00000-000'
            ],
            [
                'nome' => 'telefone_camara',
                'label' => 'Telefone da Câmara',
                'descricao' => 'Telefone principal da Câmara (variável: ${telefone_camara})',
                'tipo_campo' => 'text',
                'valor_padrao' => '(11) 3396-4000',
                'obrigatorio' => true,
                'ordem' => 4,
                'placeholder' => '(11) 0000-0000'
            ],
            [
                'nome' => 'website_camara',
                'label' => 'Website da Câmara',
                'descricao' => 'Website oficial da Câmara (variável: ${website_camara})',
                'tipo_campo' => 'text',
                'valor_padrao' => 'www.camara.sp.gov.br',
                'obrigatorio' => false,
                'ordem' => 5,
                'placeholder' => 'www.camara.gov.br'
            ],
        ];

        foreach ($camposDadosCamara as $campoData) {
            $campoData['submodulo_id'] = $submodulo5->id;
            $this->parametroService->criarCampo($campoData);
        }

        $this->command->info('Módulo de Templates com todos os submódulos criado com sucesso!');
    }
}