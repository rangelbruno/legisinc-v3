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
        // Verificar se o módulo Templates já existe
        $modulo = \App\Models\Parametro\ParametroModulo::where('nome', 'Templates')->first();
        
        if (!$modulo) {
            // Criar módulo "Templates" se não existir
            $moduloData = [
                'nome' => 'Templates',
                'descricao' => 'Configurações e parâmetros para templates de documentos',
                'icon' => 'ki-document',
                'ordem' => 6,
                'ativo' => true
            ];

            $modulo = $this->parametroService->criarModulo($moduloData);
        }

        // Verificar ou criar submódulo "Cabeçalho"
        $submodulo = \App\Models\Parametro\ParametroSubmodulo::where('modulo_id', $modulo->id)
            ->where('nome', 'Cabeçalho')
            ->first();
            
        if (!$submodulo) {
            $submoduloData = [
                'modulo_id' => $modulo->id,
                'nome' => 'Cabeçalho',
                'descricao' => 'Configurações do cabeçalho dos templates',
                'tipo' => 'form',
                'ordem' => 1,
                'ativo' => true
            ];
            $submodulo = $this->parametroService->criarSubmodulo($submoduloData);
        }

        // Criar ou atualizar campos do submódulo Cabeçalho
        // Sincronizado com os parâmetros existentes
        $campos = [
            [
                'nome' => 'cabecalho_imagem',
                'label' => 'Logo/Brasão da Câmara',
                'descricao' => 'Imagem do brasão ou logo da câmara para o cabeçalho (variável: ${cabecalho_imagem})',
                'tipo_campo' => 'file',
                'valor_padrao' => '',
                'obrigatorio' => false,
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
                'nome' => 'cabecalho_nome_camara',
                'label' => 'Nome da Câmara',
                'descricao' => 'Nome completo da câmara municipal (variável: ${cabecalho_nome_camara})',
                'tipo_campo' => 'text',
                'valor_padrao' => 'CÂMARA MUNICIPAL',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => 'Ex: CÂMARA MUNICIPAL DE SÃO PAULO'
            ],
            [
                'nome' => 'cabecalho_endereco',
                'label' => 'Endereço',
                'descricao' => 'Endereço completo da câmara (variável: ${cabecalho_endereco})',
                'tipo_campo' => 'textarea',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 3,
                'placeholder' => 'Endereço completo com CEP'
            ],
            [
                'nome' => 'cabecalho_telefone',
                'label' => 'Telefone',
                'descricao' => 'Telefone de contato (variável: ${cabecalho_telefone})',
                'tipo_campo' => 'text',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 4,
                'placeholder' => '(00) 0000-0000'
            ],
            [
                'nome' => 'cabecalho_website',
                'label' => 'Website',
                'descricao' => 'Site da câmara (variável: ${cabecalho_website})',
                'tipo_campo' => 'text',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 5,
                'placeholder' => 'www.camara.gov.br'
            ]
        ];

        foreach ($campos as $campoData) {
            $campo = \App\Models\Parametro\ParametroCampo::where('submodulo_id', $submodulo->id)
                ->where('nome', $campoData['nome'])
                ->first();
                
            if (!$campo) {
                $campoData['submodulo_id'] = $submodulo->id;
                $this->parametroService->criarCampo($campoData);
            }
        }

        // Removido submódulos "Marca D'água" e "Texto Padrão" pois não existem nos parâmetros atuais

        // Verificar ou criar submódulo "Rodapé"
        $submodulo4 = \App\Models\Parametro\ParametroSubmodulo::where('modulo_id', $modulo->id)
            ->where('nome', 'Rodapé')
            ->first();
            
        if (!$submodulo4) {
            $submoduloRodape = [
                'modulo_id' => $modulo->id,
                'nome' => 'Rodapé',
                'descricao' => 'Configurações do rodapé dos templates',
                'tipo' => 'form',
                'ordem' => 2,
                'ativo' => true
            ];
            $submodulo4 = $this->parametroService->criarSubmodulo($submoduloRodape);
        }

        // Criar ou atualizar campos do submódulo Rodapé
        // Sincronizado com os parâmetros existentes
        $camposRodape = [
            [
                'nome' => 'rodape_texto',
                'label' => 'Texto do Rodapé',
                'descricao' => 'Texto que aparece no rodapé dos documentos (variável: ${rodape_texto})',
                'tipo_campo' => 'textarea',
                'valor_padrao' => '',
                'obrigatorio' => false,
                'ordem' => 1,
                'placeholder' => 'Texto do rodapé'
            ],
            [
                'nome' => 'rodape_numeracao',
                'label' => 'Exibir Numeração de Página',
                'descricao' => 'Mostrar número da página no rodapé (variável: ${rodape_numeracao})',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'ordem' => 2,
                'placeholder' => 'Ativar numeração de páginas'
            ]
        ];

        foreach ($camposRodape as $campoData) {
            $campo = \App\Models\Parametro\ParametroCampo::where('submodulo_id', $submodulo4->id)
                ->where('nome', $campoData['nome'])
                ->first();
                
            if (!$campo) {
                $campoData['submodulo_id'] = $submodulo4->id;
                $this->parametroService->criarCampo($campoData);
            }
        }

        // Verificar ou criar submódulo "Variáveis Dinâmicas"
        $submodulo5 = \App\Models\Parametro\ParametroSubmodulo::where('modulo_id', $modulo->id)
            ->where('nome', 'Variáveis Dinâmicas')
            ->first();
            
        if (!$submodulo5) {
            $submoduloVariaveis = [
                'modulo_id' => $modulo->id,
                'nome' => 'Variáveis Dinâmicas',
                'descricao' => 'Variáveis que podem ser usadas nos templates',
                'tipo' => 'form',
                'ordem' => 3,
                'ativo' => true
            ];
            $submodulo5 = $this->parametroService->criarSubmodulo($submoduloVariaveis);
        }

        // Criar ou atualizar campos do submódulo Variáveis Dinâmicas
        $camposVariaveis = [
            [
                'nome' => 'var_prefixo_numeracao',
                'label' => 'Prefixo de Numeração',
                'descricao' => 'Prefixo usado na numeração das proposições (variável: ${var_prefixo_numeracao})',
                'tipo_campo' => 'text',
                'valor_padrao' => 'PROP',
                'obrigatorio' => false,
                'ordem' => 1,
                'placeholder' => 'Ex: PROP, PL, PLC'
            ],
            [
                'nome' => 'var_formato_data',
                'label' => 'Formato de Data',
                'descricao' => 'Formato usado para exibir datas (variável: ${var_formato_data})',
                'tipo_campo' => 'select',
                'valor_padrao' => 'd/m/Y',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => 'Selecione o formato',
                'opcoes' => [
                    'd/m/Y' => 'DD/MM/AAAA',
                    'd-m-Y' => 'DD-MM-AAAA',
                    'Y-m-d' => 'AAAA-MM-DD',
                    'd \\d\\e F \\d\\e Y' => 'DD de Mês de AAAA'
                ]
            ],
            [
                'nome' => 'var_assinatura_padrao',
                'label' => 'Texto de Assinatura Padrão',
                'descricao' => 'Texto padrão para área de assinatura (variável: ${var_assinatura_padrao})',
                'tipo_campo' => 'textarea',
                'valor_padrao' => "Sala das Sessões, em _____ de _____________ de _______.\n\n\n_________________________________\nVereador(a)",
                'obrigatorio' => false,
                'ordem' => 3,
                'placeholder' => 'Texto padrão de assinatura'
            ]
        ];

        foreach ($camposVariaveis as $campoData) {
            $campo = \App\Models\Parametro\ParametroCampo::where('submodulo_id', $submodulo5->id)
                ->where('nome', $campoData['nome'])
                ->first();
                
            if (!$campo) {
                $campoData['submodulo_id'] = $submodulo5->id;
                $this->parametroService->criarCampo($campoData);
            }
        }

        // Verificar ou criar submódulo "Formatação"
        $submodulo6 = \App\Models\Parametro\ParametroSubmodulo::where('modulo_id', $modulo->id)
            ->where('nome', 'Formatação')
            ->first();
            
        if (!$submodulo6) {
            $submoduloFormatacao = [
                'modulo_id' => $modulo->id,
                'nome' => 'Formatação',
                'descricao' => 'Configurações de formatação dos documentos',
                'tipo' => 'form',
                'ordem' => 4,
                'ativo' => true
            ];
            $submodulo6 = $this->parametroService->criarSubmodulo($submoduloFormatacao);
        }

        // Criar ou atualizar campos do submódulo Formatação
        $camposFormatacao = [
            [
                'nome' => 'format_fonte',
                'label' => 'Fonte Padrão',
                'descricao' => 'Fonte padrão dos documentos (variável: ${format_fonte})',
                'tipo_campo' => 'select',
                'valor_padrao' => 'Arial',
                'obrigatorio' => true,
                'ordem' => 1,
                'placeholder' => 'Selecione a fonte',
                'opcoes' => [
                    'Arial' => 'Arial',
                    'Times New Roman' => 'Times New Roman',
                    'Calibri' => 'Calibri',
                    'Verdana' => 'Verdana',
                    'Helvetica' => 'Helvetica'
                ]
            ],
            [
                'nome' => 'format_tamanho_fonte',
                'label' => 'Tamanho da Fonte',
                'descricao' => 'Tamanho padrão da fonte em pt (variável: ${format_tamanho_fonte})',
                'tipo_campo' => 'number',
                'valor_padrao' => '12',
                'obrigatorio' => true,
                'ordem' => 2,
                'placeholder' => '12',
                'validacao' => [
                    'min' => 8,
                    'max' => 24
                ]
            ],
            [
                'nome' => 'format_espacamento',
                'label' => 'Espaçamento entre Linhas',
                'descricao' => 'Espaçamento entre linhas do texto (variável: ${format_espacamento})',
                'tipo_campo' => 'select',
                'valor_padrao' => '1.5',
                'obrigatorio' => true,
                'ordem' => 3,
                'placeholder' => 'Selecione o espaçamento',
                'opcoes' => [
                    '1' => 'Simples',
                    '1.5' => '1,5 linhas',
                    '2' => 'Duplo',
                    '2.5' => '2,5 linhas'
                ]
            ],
            [
                'nome' => 'format_margens',
                'label' => 'Margens (cm)',
                'descricao' => 'Margens do documento (Superior, Inferior, Esquerda, Direita) (variável: ${format_margens})',
                'tipo_campo' => 'text',
                'valor_padrao' => '2.5, 2.5, 3, 2',
                'obrigatorio' => true,
                'ordem' => 4,
                'placeholder' => 'Superior, Inferior, Esquerda, Direita'
            ]
        ];

        foreach ($camposFormatacao as $campoData) {
            $campo = \App\Models\Parametro\ParametroCampo::where('submodulo_id', $submodulo6->id)
                ->where('nome', $campoData['nome'])
                ->first();
                
            if (!$campo) {
                $campoData['submodulo_id'] = $submodulo6->id;
                $this->parametroService->criarCampo($campoData);
            }
        }

        // Verificar ou criar submódulo "Assinatura e QR Code"
        $submodulo7 = \App\Models\Parametro\ParametroSubmodulo::where('modulo_id', $modulo->id)
            ->where('nome', 'Assinatura e QR Code')
            ->first();
            
        if (!$submodulo7) {
            $submoduloAssinatura = [
                'modulo_id' => $modulo->id,
                'nome' => 'Assinatura e QR Code',
                'descricao' => 'Configurações de posicionamento da assinatura digital e QR Code',
                'tipo' => 'form',
                'ordem' => 5,
                'ativo' => true
            ];
            $submodulo7 = $this->parametroService->criarSubmodulo($submoduloAssinatura);
        }

        // Criar ou atualizar campos do submódulo Assinatura e QR Code
        $camposAssinatura = [
            [
                'nome' => 'assinatura_posicao',
                'label' => 'Posição da Assinatura Digital',
                'descricao' => 'Define onde a assinatura digital será posicionada no documento (variável: ${assinatura_posicao})',
                'tipo_campo' => 'select',
                'valor_padrao' => 'rodape_direita',
                'obrigatorio' => true,
                'ordem' => 1,
                'placeholder' => 'Escolha a posição',
                'opcoes' => [
                    'rodape_esquerda' => 'Rodapé - Esquerda',
                    'rodape_centro' => 'Rodapé - Centro',
                    'rodape_direita' => 'Rodapé - Direita',
                    'final_documento_esquerda' => 'Final do Documento - Esquerda',
                    'final_documento_centro' => 'Final do Documento - Centro',
                    'final_documento_direita' => 'Final do Documento - Direita',
                    'pagina_separada' => 'Página Separada'
                ]
            ],
            [
                'nome' => 'assinatura_texto',
                'label' => 'Texto da Assinatura Digital',
                'descricao' => 'Texto que acompanha a assinatura digital. Use {autor_nome}, {autor_cargo}, {data_assinatura} (variável: ${assinatura_texto})',
                'tipo_campo' => 'textarea',
                'valor_padrao' => "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}",
                'obrigatorio' => false,
                'ordem' => 2,
                'placeholder' => 'Use {autor_nome}, {autor_cargo}, {data_assinatura} como variáveis'
            ],
            [
                'nome' => 'qrcode_posicao',
                'label' => 'Posição do QR Code',
                'descricao' => 'Define onde o QR Code será posicionado no documento (variável: ${qrcode_posicao})',
                'tipo_campo' => 'select',
                'valor_padrao' => 'rodape_esquerda',
                'obrigatorio' => true,
                'ordem' => 3,
                'placeholder' => 'Escolha a posição',
                'opcoes' => [
                    'rodape_esquerda' => 'Rodapé - Esquerda',
                    'rodape_centro' => 'Rodapé - Centro',
                    'rodape_direita' => 'Rodapé - Direita',
                    'cabecalho_esquerda' => 'Cabeçalho - Esquerda',
                    'cabecalho_direita' => 'Cabeçalho - Direita',
                    'final_documento_esquerda' => 'Final do Documento - Esquerda',
                    'final_documento_centro' => 'Final do Documento - Centro',
                    'final_documento_direita' => 'Final do Documento - Direita',
                    'lateral_direita' => 'Lateral Direita (Margem)',
                    'desabilitado' => 'Não Exibir QR Code'
                ]
            ],
            [
                'nome' => 'qrcode_tamanho',
                'label' => 'Tamanho do QR Code (pixels)',
                'descricao' => 'Tamanho do QR Code em pixels (variável: ${qrcode_tamanho})',
                'tipo_campo' => 'number',
                'valor_padrao' => '100',
                'obrigatorio' => true,
                'ordem' => 4,
                'placeholder' => 'Ex: 100',
                'validacao' => [
                    'min' => 50,
                    'max' => 300
                ]
            ],
            [
                'nome' => 'qrcode_texto',
                'label' => 'Texto do QR Code',
                'descricao' => 'Texto explicativo que acompanha o QR Code. Use {numero_protocolo}, {numero_proposicao} (variável: ${qrcode_texto})',
                'tipo_campo' => 'textarea',
                'valor_padrao' => "Consulte este documento online:\nProtocolo: {numero_protocolo}",
                'obrigatorio' => false,
                'ordem' => 5,
                'placeholder' => 'Use {numero_protocolo}, {numero_proposicao} como variáveis'
            ],
            [
                'nome' => 'qrcode_url_formato',
                'label' => 'Formato da URL do QR Code',
                'descricao' => 'Formato da URL que será codificada no QR Code. Use {base_url}, {numero_protocolo}, {numero_proposicao} (variável: ${qrcode_url_formato})',
                'tipo_campo' => 'text',
                'valor_padrao' => '{base_url}/proposicoes/consulta/{numero_protocolo}',
                'obrigatorio' => true,
                'ordem' => 6,
                'placeholder' => 'Use {base_url}, {numero_protocolo}, {numero_proposicao}'
            ],
            [
                'nome' => 'assinatura_apenas_protocolo',
                'label' => 'Mostrar Assinatura Apenas Após Protocolo',
                'descricao' => 'A assinatura digital só aparece no documento após ser protocolado (variável: ${assinatura_apenas_protocolo})',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'ordem' => 7,
                'placeholder' => 'Controlar exibição da assinatura'
            ],
            [
                'nome' => 'qrcode_apenas_protocolo',
                'label' => 'Mostrar QR Code Apenas Após Protocolo',
                'descricao' => 'O QR Code só aparece no documento após ser protocolado (variável: ${qrcode_apenas_protocolo})',
                'tipo_campo' => 'checkbox',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'ordem' => 8,
                'placeholder' => 'Controlar exibição do QR Code'
            ]
        ];

        foreach ($camposAssinatura as $campoData) {
            $campo = \App\Models\Parametro\ParametroCampo::where('submodulo_id', $submodulo7->id)
                ->where('nome', $campoData['nome'])
                ->first();
                
            if (!$campo) {
                $campoData['submodulo_id'] = $submodulo7->id;
                $this->parametroService->criarCampo($campoData);
            }
        }

        $this->command->info('Módulo de Templates com todos os submódulos criado com sucesso!');
        $this->command->info('✅ Submódulo "Assinatura e QR Code" adicionado com 8 campos configuráveis');
    }
}