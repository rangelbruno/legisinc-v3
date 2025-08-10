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

        // Criar submódulo "Dados Gerais da Câmara"
        $submoduloDadosCamara = [
            'modulo_id' => $modulo->id,
            'nome' => 'Dados Gerais da Câmara',
            'descricao' => 'Informações institucionais da Câmara Municipal',
            'tipo' => 'form',
            'ordem' => 5,
            'ativo' => true
        ];

        $submodulo5 = $this->parametroService->criarSubmodulo($submoduloDadosCamara);

        // Criar campos do submódulo Dados Gerais da Câmara
        $camposDadosCamara = [
            [
                'nome' => 'nome_camara_oficial',
                'label' => 'Nome Oficial da Câmara',
                'descricao' => 'Nome oficial completo da Câmara Municipal',
                'tipo_campo' => 'text',
                'valor_padrao' => 'CÂMARA MUNICIPAL DE SÃO PAULO',
                'obrigatorio' => true,
                'ordem' => 1,
                'placeholder' => 'Ex: CÂMARA MUNICIPAL DE SÃO PAULO'
            ],
            [
                'nome' => 'nome_camara_abreviado',
                'label' => 'Nome Abreviado',
                'descricao' => 'Sigla ou nome abreviado da Câmara',
                'tipo_campo' => 'text',
                'valor_padrao' => 'CMSP',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => 'Ex: CMSP'
            ],
            [
                'nome' => 'cnpj',
                'label' => 'CNPJ',
                'descricao' => 'CNPJ da Câmara Municipal',
                'tipo_campo' => 'text',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 3,
                'placeholder' => '00.000.000/0001-00',
                'validacao' => [
                    'pattern' => '\d{2}\.\d{3}\.\d{3}/\d{4}-\d{2}'
                ]
            ],
            [
                'nome' => 'municipio_nome',
                'label' => 'Nome do Município',
                'descricao' => 'Nome do município onde a Câmara está localizada',
                'tipo_campo' => 'text',
                'valor_padrao' => 'São Paulo',
                'obrigatorio' => true,
                'ordem' => 4,
                'placeholder' => 'Ex: São Paulo'
            ],
            [
                'nome' => 'municipio_uf',
                'label' => 'UF',
                'descricao' => 'Unidade Federativa (Estado)',
                'tipo_campo' => 'select',
                'valor_padrao' => 'SP',
                'obrigatorio' => true,
                'ordem' => 5,
                'placeholder' => 'Selecione o estado',
                'opcoes' => [
                    'AC' => 'Acre',
                    'AL' => 'Alagoas',
                    'AP' => 'Amapá',
                    'AM' => 'Amazonas',
                    'BA' => 'Bahia',
                    'CE' => 'Ceará',
                    'DF' => 'Distrito Federal',
                    'ES' => 'Espírito Santo',
                    'GO' => 'Goiás',
                    'MA' => 'Maranhão',
                    'MT' => 'Mato Grosso',
                    'MS' => 'Mato Grosso do Sul',
                    'MG' => 'Minas Gerais',
                    'PA' => 'Pará',
                    'PB' => 'Paraíba',
                    'PR' => 'Paraná',
                    'PE' => 'Pernambuco',
                    'PI' => 'Piauí',
                    'RJ' => 'Rio de Janeiro',
                    'RN' => 'Rio Grande do Norte',
                    'RS' => 'Rio Grande do Sul',
                    'RO' => 'Rondônia',
                    'RR' => 'Roraima',
                    'SC' => 'Santa Catarina',
                    'SP' => 'São Paulo',
                    'SE' => 'Sergipe',
                    'TO' => 'Tocantins'
                ]
            ],
            [
                'nome' => 'endereco_logradouro',
                'label' => 'Logradouro',
                'descricao' => 'Endereço completo da Câmara (rua, número)',
                'tipo_campo' => 'text',
                'valor_padrao' => 'Viaduto Jacareí, 100',
                'obrigatorio' => true,
                'ordem' => 6,
                'placeholder' => 'Ex: Rua das Flores, 123'
            ],
            [
                'nome' => 'endereco_bairro',
                'label' => 'Bairro',
                'descricao' => 'Bairro onde a Câmara está localizada',
                'tipo_campo' => 'text',
                'valor_padrao' => 'Bela Vista',
                'obrigatorio' => true,
                'ordem' => 7,
                'placeholder' => 'Ex: Centro'
            ],
            [
                'nome' => 'endereco_cep',
                'label' => 'CEP',
                'descricao' => 'Código de Endereçamento Postal',
                'tipo_campo' => 'text',
                'valor_padrao' => '01319-900',
                'obrigatorio' => true,
                'ordem' => 8,
                'placeholder' => '00000-000',
                'validacao' => [
                    'pattern' => '\d{5}-\d{3}'
                ]
            ],
            [
                'nome' => 'telefone_principal',
                'label' => 'Telefone Principal',
                'descricao' => 'Telefone principal da Câmara',
                'tipo_campo' => 'text',
                'valor_padrao' => '(11) 3396-4000',
                'obrigatorio' => true,
                'ordem' => 9,
                'placeholder' => '(11) 0000-0000'
            ],
            [
                'nome' => 'telefone_protocolo',
                'label' => 'Telefone do Protocolo',
                'descricao' => 'Telefone específico do protocolo/atendimento',
                'tipo_campo' => 'text',
                'valor_padrao' => '(11) 3396-4100',
                'obrigatorio' => false,
                'ordem' => 10,
                'placeholder' => '(11) 0000-0000'
            ],
            [
                'nome' => 'email_oficial',
                'label' => 'E-mail Oficial',
                'descricao' => 'E-mail oficial da Câmara',
                'tipo_campo' => 'email',
                'valor_padrao' => 'contato@saopaulo.sp.leg.br',
                'obrigatorio' => true,
                'ordem' => 11,
                'placeholder' => 'contato@camara.gov.br'
            ],
            [
                'nome' => 'website',
                'label' => 'Website',
                'descricao' => 'Site oficial da Câmara',
                'tipo_campo' => 'text',
                'valor_padrao' => 'www.saopaulo.sp.leg.br',
                'obrigatorio' => false,
                'ordem' => 12,
                'placeholder' => 'www.camara.gov.br'
            ],
            [
                'nome' => 'presidente_nome',
                'label' => 'Nome do Presidente',
                'descricao' => 'Nome do atual presidente da Câmara',
                'tipo_campo' => 'text',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 13,
                'placeholder' => 'Nome do Presidente'
            ],
            [
                'nome' => 'presidente_tratamento',
                'label' => 'Tratamento do Presidente',
                'descricao' => 'Forma de tratamento para o presidente',
                'tipo_campo' => 'select',
                'valor_padrao' => 'Excelentíssimo Senhor',
                'obrigatorio' => false,
                'ordem' => 14,
                'placeholder' => 'Selecione o tratamento',
                'opcoes' => [
                    'Excelentíssimo Senhor' => 'Excelentíssimo Senhor',
                    'Excelentíssima Senhora' => 'Excelentíssima Senhora',
                    'Senhor' => 'Senhor',
                    'Senhora' => 'Senhora'
                ]
            ],
            [
                'nome' => 'horario_funcionamento',
                'label' => 'Horário de Funcionamento',
                'descricao' => 'Horário de funcionamento geral da Câmara',
                'tipo_campo' => 'text',
                'valor_padrao' => 'Segunda a Sexta: 8h às 17h',
                'obrigatorio' => false,
                'ordem' => 15,
                'placeholder' => 'Ex: Segunda a Sexta: 8h às 17h'
            ],
            [
                'nome' => 'horario_protocolo',
                'label' => 'Horário do Protocolo',
                'descricao' => 'Horário de funcionamento específico do protocolo',
                'tipo_campo' => 'text',
                'valor_padrao' => 'Segunda a Sexta: 9h às 16h',
                'obrigatorio' => false,
                'ordem' => 16,
                'placeholder' => 'Ex: Segunda a Sexta: 9h às 16h'
            ],
            [
                'nome' => 'legislatura_atual',
                'label' => 'Legislatura Atual',
                'descricao' => 'Período da legislatura atual',
                'tipo_campo' => 'text',
                'valor_padrao' => '2021-2024',
                'obrigatorio' => false,
                'ordem' => 17,
                'placeholder' => 'Ex: 2021-2024'
            ],
            [
                'nome' => 'sessao_atual',
                'label' => 'Sessão Atual',
                'descricao' => 'Ano da sessão legislativa atual',
                'tipo_campo' => 'text',
                'valor_padrao' => '2025',
                'obrigatorio' => false,
                'ordem' => 18,
                'placeholder' => 'Ex: 2025'
            ]
        ];

        foreach ($camposDadosCamara as $campoData) {
            $campoData['submodulo_id'] = $submodulo5->id;
            $this->parametroService->criarCampo($campoData);
        }

        $this->command->info('Módulo de Templates com todos os submódulos criado com sucesso!');
    }
}