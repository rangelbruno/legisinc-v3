<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;

class ParametrosPadroesLegaisSeeder extends Seeder
{
    /**
     * Configurar parâmetros para atender aos padrões da LC 95/1998
     * e requisitos de documentos oficiais brasileiros
     */
    public function run(): void
    {
        // Obter módulo Templates
        $moduloTemplates = ParametroModulo::where('nome', 'Templates')->first();
        
        if (!$moduloTemplates) {
            $this->command->error('Módulo Templates não encontrado. Execute primeiro o ParametrosTemplatesSeeder.');
            return;
        }

        // 1. Submódulo: Estrutura Legal (LC 95/1998)
        $this->criarSubmoduloEstruturaLegal($moduloTemplates);
        
        // 2. Submódulo: Metadados e Interoperabilidade
        $this->criarSubmoduloMetadados($moduloTemplates);
        
        // 3. Submódulo: Numeração Unificada
        $this->criarSubmoduloNumeracao($moduloTemplates);
        
        // 4. Submódulo: Acessibilidade
        $this->criarSubmoduloAcessibilidade($moduloTemplates);
        
        // 5. Submódulo: Assinatura Digital
        $this->criarSubmoduloAssinaturaDigital($moduloTemplates);

        $this->command->info('Parâmetros de padrões legais configurados com sucesso!');
    }

    private function criarSubmoduloEstruturaLegal($modulo)
    {
        $submodulo = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $modulo->id,
                'nome' => 'Estrutura Legal'
            ],
            [
                'descricao' => 'Configurações para atender LC 95/1998 - Estrutura obrigatória de proposições',
                'tipo' => 'form',
                'ordem' => 5,
                'ativo' => true
            ]
        );

        // Campo: Formato de Epígrafe
        $campoEpigrafe = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'estrutura_formato_epigrafe'
            ],
            [
                'label' => 'Formato da Epígrafe',
                'tipo_campo' => 'select',
                'descricao' => 'Formato padrão para a epígrafe (Tipo + Número/Ano)',
                'obrigatorio' => true,
                'valor_padrao' => 'tipo_espaco_numero_barra_ano',
                'opcoes' => [
                    'tipo_espaco_numero_barra_ano' => 'TIPO Nº 000/AAAA',
                    'tipo_numero_barra_ano' => 'TIPO 000/AAAA', 
                    'tipo_espaco_numero_ano' => 'TIPO Nº 000 DE AAAA'
                ],
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Campo: Padrão de Ementa
        $campoEmenta = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'estrutura_padrao_ementa'
            ],
            [
                'label' => 'Padrão da Ementa',
                'tipo_campo' => 'textarea',
                'descricao' => 'Modelo padrão para ementas (verbo no indicativo, frase única)',
                'obrigatorio' => false,
                'valor_padrao' => 'Dispõe sobre [OBJETO] e dá outras providências.',
                'placeholder' => 'Ex: Dispõe sobre... | Autoriza... | Institui... | Altera...',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Campo: Preâmbulo Padrão
        $campoPreambulo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'estrutura_preambulo'
            ],
            [
                'label' => 'Preâmbulo',
                'tipo_campo' => 'select',
                'descricao' => 'Fórmula de promulgação conforme a origem da proposição',
                'obrigatorio' => true,
                'valor_padrao' => 'camara_municipal',
                'opcoes' => [
                    'camara_municipal' => 'A CÂMARA MUNICIPAL DE [MUNICÍPIO] DECRETA:',
                    'prefeito' => 'O PREFEITO MUNICIPAL DE [MUNICÍPIO] DECRETA:',
                    'congresso' => 'O CONGRESSO NACIONAL DECRETA:',
                    'personalizado' => 'Personalizado'
                ],
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Campo: Numeração de Artigos
        $campoNumeracaoArtigos = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'estrutura_numeracao_artigos'
            ],
            [
                'label' => 'Padrão de Numeração de Artigos',
                'tipo_campo' => 'select',
                'descricao' => 'Formato para numeração de artigos conforme LC 95/1998',
                'obrigatorio' => true,
                'valor_padrao' => 'art_ordinal_ate_nove',
                'opcoes' => [
                    'art_ordinal_ate_nove' => 'Art. 1º, 2º... (ordinal até 9º, depois cardinal)',
                    'art_cardinal' => 'Art. 1, 2, 3... (sempre cardinal)',
                    'art_ordinal_completo' => 'Art. 1º, 2º, 3º... (sempre ordinal)'
                ],
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Campo: Cláusula de Vigência
        $campoVigencia = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'estrutura_clausula_vigencia'
            ],
            [
                'label' => 'Cláusula de Vigência',
                'tipo_campo' => 'select',
                'descricao' => 'Padrão para cláusula de vigência',
                'obrigatorio' => true,
                'valor_padrao' => 'imediata',
                'opcoes' => [
                    'imediata' => 'Esta lei entra em vigor na data de sua publicação.',
                    'vacatio_30' => 'Esta lei entra em vigor após 30 (trinta) dias de sua publicação.',
                    'vacatio_60' => 'Esta lei entra em vigor após 60 (sessenta) dias de sua publicação.',
                    'vacatio_90' => 'Esta lei entra em vigor após 90 (noventa) dias de sua publicação.',
                    'data_especifica' => 'Esta lei entra em vigor em [DATA].',
                    'personalizada' => 'Personalizada'
                ],
                'ordem' => 5,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoEpigrafe, 'tipo_espaco_numero_barra_ano');
        $this->definirValor($campoEmenta, 'Dispõe sobre [OBJETO] e dá outras providências.');
        $this->definirValor($campoPreambulo, 'camara_municipal');
        $this->definirValor($campoNumeracaoArtigos, 'art_ordinal_ate_nove');
        $this->definirValor($campoVigencia, 'imediata');
    }

    private function criarSubmoduloMetadados($modulo)
    {
        $submodulo = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $modulo->id,
                'nome' => 'Metadados'
            ],
            [
                'descricao' => 'Configurações de metadados Dublin Core e LexML para interoperabilidade',
                'tipo' => 'form',
                'ordem' => 6,
                'ativo' => true
            ]
        );

        // Campo: Autoridade LexML
        $campoAutoridade = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'metadados_autoridade_lexml'
            ],
            [
                'label' => 'Autoridade LexML',
                'tipo_campo' => 'text',
                'descricao' => 'Identificador da autoridade para URN LexML (ex: br.sp.saopaulo.camara)',
                'obrigatorio' => true,
                'valor_padrao' => 'br.[UF].[MUNICIPIO].camara',
                'placeholder' => 'br.sp.saopaulo.camara',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Campo: Habilitar Dublin Core
        $campoDublinCore = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'metadados_dublin_core'
            ],
            [
                'label' => 'Habilitar Metadados Dublin Core',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Incluir metadados Dublin Core nos documentos',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Campo: Habilitar LexML
        $campoLexML = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'metadados_lexml'
            ],
            [
                'label' => 'Habilitar LexML',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Gerar identificadores URN LexML',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Campo: URL Base OAI-PMH
        $campoOAI = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'metadados_oai_pmh_url'
            ],
            [
                'label' => 'URL Base OAI-PMH',
                'tipo_campo' => 'text',
                'descricao' => 'URL para exposição de metadados via OAI-PMH',
                'obrigatorio' => false,
                'placeholder' => 'https://camara.municipio.gov.br/oai-pmh',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoAutoridade, 'br.sp.saopaulo.camara');
        $this->definirValor($campoDublinCore, '1', 'boolean');
        $this->definirValor($campoLexML, '1', 'boolean');
    }

    private function criarSubmoduloNumeracao($modulo)
    {
        $submodulo = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $modulo->id,
                'nome' => 'Numeração Unificada'
            ],
            [
                'descricao' => 'Configurações para numeração unificada por tipo e ano (padrão desde 2019)',
                'tipo' => 'form',
                'ordem' => 7,
                'ativo' => true
            ]
        );

        // Campo: Sistema de Numeração
        $campoSistema = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'numeracao_sistema'
            ],
            [
                'label' => 'Sistema de Numeração',
                'tipo_campo' => 'select',
                'descricao' => 'Sistema de numeração adotado',
                'obrigatorio' => true,
                'valor_padrao' => 'unificada_anual',
                'opcoes' => [
                    'unificada_anual' => 'Unificada por tipo e ano (padrão desde 2019)',
                    'sequencial_tipo' => 'Sequencial por tipo (sem reiniciar no ano)',
                    'geral_anual' => 'Geral por ano (independente do tipo)'
                ],
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Campo: Reiniciar Numeração
        $campoReiniciar = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'numeracao_reiniciar_ano'
            ],
            [
                'label' => 'Reiniciar Numeração a Cada Ano',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Reinicia a numeração no início de cada ano legislativo',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Campo: Dígitos Mínimos
        $campoDigitos = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'numeracao_digitos_minimos'
            ],
            [
                'label' => 'Dígitos Mínimos',
                'tipo_campo' => 'number',
                'descricao' => 'Número mínimo de dígitos na numeração (com zeros à esquerda)',
                'obrigatorio' => true,
                'valor_padrao' => '3',
                'validacao' => ['min' => 1, 'max' => 6],
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Campo: Ano Fiscal/Legislativo
        $campoAnoFiscal = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'numeracao_inicio_ano_fiscal'
            ],
            [
                'label' => 'Início do Ano Legislativo',
                'tipo_campo' => 'text',
                'descricao' => 'Data de início do ano legislativo (formato MM-DD)',
                'obrigatorio' => true,
                'valor_padrao' => '01-01',
                'placeholder' => '01-01 (1º de janeiro)',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoSistema, 'unificada_anual');
        $this->definirValor($campoReiniciar, '1', 'boolean');
        $this->definirValor($campoDigitos, '3');
        $this->definirValor($campoAnoFiscal, '01-01');
    }

    private function criarSubmoduloAcessibilidade($modulo)
    {
        $submodulo = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $modulo->id,
                'nome' => 'Acessibilidade'
            ],
            [
                'descricao' => 'Configurações para acessibilidade conforme WCAG 2.1 AA e PDF/UA',
                'tipo' => 'form',
                'ordem' => 8,
                'ativo' => true
            ]
        );

        // Campo: PDF/UA
        $campoPDFUA = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'acessibilidade_pdf_ua'
            ],
            [
                'label' => 'Habilitar PDF/UA',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Gerar PDFs com marcação semântica para leitores de tela',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Campo: Alt Text Automático
        $campoAltText = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'acessibilidade_alt_text_automatico'
            ],
            [
                'label' => 'Texto Alternativo Automático',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Gerar texto alternativo automático para imagens',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Campo: Linguagem Simples
        $campoLinguagem = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'acessibilidade_linguagem_simples'
            ],
            [
                'label' => 'Sugestões de Linguagem Simples',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Exibir sugestões para linguagem simples nas ementas',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Campo: Contraste
        $campoContraste = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'acessibilidade_verificar_contraste'
            ],
            [
                'label' => 'Verificar Contraste WCAG',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Verificar contraste de cores conforme WCAG 2.1 AA',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoPDFUA, '1', 'boolean');
        $this->definirValor($campoAltText, '1', 'boolean');
        $this->definirValor($campoLinguagem, '1', 'boolean');
        $this->definirValor($campoContraste, '1', 'boolean');
    }

    private function criarSubmoduloAssinaturaDigital($modulo)
    {
        $submodulo = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $modulo->id,
                'nome' => 'Assinatura Digital'
            ],
            [
                'descricao' => 'Configurações para assinatura digital ICP-Brasil e eIDAS',
                'tipo' => 'form',
                'ordem' => 9,
                'ativo' => true
            ]
        );

        // Campo: Habilitar Assinatura
        $campoHabilitar = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_habilitar'
            ],
            [
                'label' => 'Habilitar Assinatura Digital',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Ativar funcionalidade de assinatura digital',
                'obrigatorio' => false,
                'valor_padrao' => '0',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Campo: Padrão de Assinatura
        $campoPadrao = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_padrao'
            ],
            [
                'label' => 'Padrão de Assinatura',
                'tipo_campo' => 'select',
                'descricao' => 'Padrão técnico para assinatura digital',
                'obrigatorio' => true,
                'valor_padrao' => 'pades_b_lta',
                'opcoes' => [
                    'pades_b_lta' => 'PAdES-B-LTA (ETSI EN 319 142-2)',
                    'pades_b' => 'PAdES-B (Básico)',
                    'cades' => 'CAdES (Anexo)',
                    'xades' => 'XAdES (XML)'
                ],
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Campo: Autoridade Certificadora
        $campoAC = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_autoridade_certificadora'
            ],
            [
                'label' => 'Autoridade Certificadora',
                'tipo_campo' => 'select',
                'descricao' => 'Cadeia de certificação aceita',
                'obrigatorio' => true,
                'valor_padrao' => 'icp_brasil',
                'opcoes' => [
                    'icp_brasil' => 'ICP-Brasil (Padrão Nacional)',
                    'eidas' => 'eIDAS (União Europeia)',
                    'ambas' => 'ICP-Brasil e eIDAS',
                    'personalizada' => 'Configuração Personalizada'
                ],
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Campo: Carimbo de Tempo
        $campoCarimbo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_carimbo_tempo'
            ],
            [
                'label' => 'Carimbo de Tempo',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Incluir carimbo de tempo qualificado nas assinaturas',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Campo: URL Verificação
        $campoVerificacao = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_url_verificacao'
            ],
            [
                'label' => 'URL de Verificação',
                'tipo_campo' => 'text',
                'descricao' => 'URL pública para verificação de assinaturas',
                'obrigatorio' => false,
                'placeholder' => 'https://verificacao.camara.municipio.gov.br',
                'ordem' => 5,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoHabilitar, '0', 'boolean');
        $this->definirValor($campoPadrao, 'pades_b_lta');
        $this->definirValor($campoAC, 'icp_brasil');
        $this->definirValor($campoCarimbo, '1', 'boolean');
    }

    private function definirValor($campo, $valor, $tipo = 'string')
    {
        ParametroValor::updateOrCreate(
            ['campo_id' => $campo->id],
            [
                'valor' => $valor,
                'tipo_valor' => $tipo,
                'user_id' => null
            ]
        );
    }
}