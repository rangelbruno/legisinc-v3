<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;

class ParametrosTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar módulo Templates
        $moduloTemplates = ParametroModulo::updateOrCreate(
            ['nome' => 'Templates'],
            [
                'descricao' => 'Configurações e parâmetros para templates de documentos',
                'icon' => 'ki-document',
                'ordem' => 6,
                'ativo' => true
            ]
        );

        // Submódulo Cabeçalho
        $submoduloCabecalho = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Cabeçalho'
            ],
            [
                'descricao' => 'Configurações do cabeçalho dos templates',
                'tipo' => 'form',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Campos do Cabeçalho
        $this->criarCamposCabecalho($submoduloCabecalho);

        // Submódulo Rodapé
        $submoduloRodape = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Rodapé'
            ],
            [
                'descricao' => 'Configurações do rodapé dos templates',
                'tipo' => 'form',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Campos do Rodapé
        $this->criarCamposRodape($submoduloRodape);

        // Submódulo Variáveis Dinâmicas
        $submoduloVariaveis = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Variáveis Dinâmicas'
            ],
            [
                'descricao' => 'Variáveis que podem ser usadas nos templates',
                'tipo' => 'form',
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Campos de Variáveis
        $this->criarCamposVariaveis($submoduloVariaveis);

        // Submódulo Formatação
        $submoduloFormatacao = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Formatação'
            ],
            [
                'descricao' => 'Configurações de formatação dos documentos',
                'tipo' => 'form',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Campos de Formatação
        $this->criarCamposFormatacao($submoduloFormatacao);

        // Submódulo Assinatura e QR Code
        $submoduloAssinatura = ParametroSubmodulo::updateOrCreate(
            [
                'modulo_id' => $moduloTemplates->id,
                'nome' => 'Assinatura e QR Code'
            ],
            [
                'descricao' => 'Configurações de posicionamento da assinatura digital e QR Code',
                'tipo' => 'form',
                'ordem' => 5,
                'ativo' => true
            ]
        );

        // Campos de Assinatura e QR Code
        $this->criarCamposAssinaturaQR($submoduloAssinatura);
    }

    private function criarCamposCabecalho($submodulo)
    {
        // Logo/Brasão
        $campoLogo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'cabecalho_imagem'
            ],
            [
                'label' => 'Logo/Brasão da Câmara',
                'tipo_campo' => 'file',
                'descricao' => 'Imagem do brasão ou logo da câmara para o cabeçalho',
                'obrigatorio' => false,
                'placeholder' => 'Selecione a imagem',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Informações da Assinatura Digital
        $campoAssinaturaInfo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_digital_info'
            ],
            [
                'label' => 'Informações da Assinatura Digital',
                'tipo_campo' => 'textarea',
                'descricao' => 'Informações da assinatura digital posicionadas horizontalmente no lado direito',
                'obrigatorio' => false,
                'valor_padrao' => 'Documento assinado digitalmente',
                'placeholder' => 'Texto horizontal da assinatura digital',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // QR Code HTML
        $campoQRCodeHTML = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'qrcode_html'
            ],
            [
                'label' => 'QR Code HTML',
                'tipo_campo' => 'textarea',
                'descricao' => 'QR Code em formato HTML posicionado no canto inferior direito',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'QR Code no canto inferior direito',
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoLogo, 'template/cabecalho.png');
        $this->definirValor($campoAssinaturaInfo, 'Documento assinado digitalmente');
        $this->definirValor($campoQRCodeHTML, '');
    }

    private function criarCamposRodape($submodulo)
    {
        // Texto do Rodapé
        $campoTextoRodape = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'rodape_texto'
            ],
            [
                'label' => 'Texto do Rodapé',
                'tipo_campo' => 'textarea',
                'descricao' => 'Texto que aparece no rodapé dos documentos',
                'obrigatorio' => false,
                'placeholder' => 'Digite o texto do rodapé',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Exibir Numeração de Página
        $campoNumeracao = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'rodape_numeracao'
            ],
            [
                'label' => 'Exibir Numeração de Página',
                'tipo_campo' => 'checkbox',
                'descricao' => 'Mostrar número da página no rodapé',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoTextoRodape, 'Câmara Municipal de Caraguatatuba - Documento Oficial');
        $this->definirValor($campoNumeracao, '1', 'boolean');
    }

    private function criarCamposVariaveis($submodulo)
    {
        // Prefixo de Numeração
        $campoPrefixo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'var_prefixo_numeracao'
            ],
            [
                'label' => 'Prefixo de Numeração',
                'tipo_campo' => 'text',
                'descricao' => 'Prefixo usado na numeração das proposições',
                'obrigatorio' => false,
                'valor_padrao' => 'PROP',
                'placeholder' => 'Ex: PROP, PL, IND',
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Formato de Data
        $campoFormatoData = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'var_formato_data'
            ],
            [
                'label' => 'Formato de Data',
                'tipo_campo' => 'select',
                'descricao' => 'Formato usado para exibir datas',
                'obrigatorio' => true,
                'valor_padrao' => 'd/m/Y',
                'opcoes' => [
                    'd/m/Y' => 'DD/MM/AAAA',
                    'd-m-Y' => 'DD-MM-AAAA',
                    'Y-m-d' => 'AAAA-MM-DD',
                    'd \d\e F \d\e Y' => 'DD de Mês de AAAA'
                ],
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Assinatura Padrão
        $campoAssinatura = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'var_assinatura_padrao'
            ],
            [
                'label' => 'Texto de Assinatura Padrão',
                'tipo_campo' => 'textarea',
                'descricao' => 'Texto padrão para área de assinatura',
                'obrigatorio' => false,
                'valor_padrao' => "Sala das Sessões, em _____ de _____________ de _______.\n\n\n_________________________________\nVereador(a)",
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Definir valores
        $this->definirValor($campoPrefixo, 'PROP');
        $this->definirValor($campoFormatoData, 'd/m/Y');
        $this->definirValor($campoAssinatura, "__________________________________");
    }

    private function criarCamposFormatacao($submodulo)
    {
        // Fonte Padrão
        $campoFonte = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'format_fonte'
            ],
            [
                'label' => 'Fonte Padrão',
                'tipo_campo' => 'select',
                'descricao' => 'Fonte padrão dos documentos',
                'obrigatorio' => true,
                'valor_padrao' => 'Arial',
                'opcoes' => [
                    'Arial' => 'Arial',
                    'Times New Roman' => 'Times New Roman',
                    'Calibri' => 'Calibri',
                    'Verdana' => 'Verdana'
                ],
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Tamanho da Fonte
        $campoTamanhoFonte = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'format_tamanho_fonte'
            ],
            [
                'label' => 'Tamanho da Fonte',
                'tipo_campo' => 'number',
                'descricao' => 'Tamanho padrão da fonte em pt',
                'obrigatorio' => true,
                'valor_padrao' => '12',
                'validacao' => ['min' => 8, 'max' => 24],
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Espaçamento entre Linhas
        $campoEspacamento = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'format_espacamento'
            ],
            [
                'label' => 'Espaçamento entre Linhas',
                'tipo_campo' => 'select',
                'descricao' => 'Espaçamento entre linhas do texto',
                'obrigatorio' => true,
                'valor_padrao' => '1.5',
                'opcoes' => [
                    '1' => 'Simples',
                    '1.5' => '1,5 linhas',
                    '2' => 'Duplo'
                ],
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Margens
        $campoMargens = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'format_margens'
            ],
            [
                'label' => 'Margens (cm)',
                'tipo_campo' => 'text',
                'descricao' => 'Margens do documento (Superior, Inferior, Esquerda, Direita)',
                'obrigatorio' => true,
                'valor_padrao' => '2.5, 2.5, 3, 2',
                'placeholder' => 'Superior, Inferior, Esquerda, Direita',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Definir valores
        $this->definirValor($campoFonte, 'Arial');
        $this->definirValor($campoTamanhoFonte, '12');
        $this->definirValor($campoEspacamento, '1.5');
        $this->definirValor($campoMargens, '2.5, 2.5, 3, 2');
    }

    private function criarCamposAssinaturaQR($submodulo)
    {
        // Posição da Assinatura Digital
        $campoPosicaoAssinatura = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_posicao'
            ],
            [
                'label' => 'Posição da Assinatura Digital',
                'tipo_campo' => 'select',
                'descricao' => 'Define onde a assinatura digital será posicionada no documento',
                'obrigatorio' => true,
                'valor_padrao' => 'rodape_direita',
                'opcoes' => [
                    'rodape_esquerda' => 'Rodapé - Esquerda',
                    'rodape_centro' => 'Rodapé - Centro',
                    'rodape_direita' => 'Rodapé - Direita',
                    'final_documento_esquerda' => 'Final do Documento - Esquerda',
                    'final_documento_centro' => 'Final do Documento - Centro',
                    'final_documento_direita' => 'Final do Documento - Direita',
                    'pagina_separada' => 'Página Separada'
                ],
                'ordem' => 1,
                'ativo' => true
            ]
        );

        // Texto da Assinatura Digital
        $campoTextoAssinatura = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_texto'
            ],
            [
                'label' => 'Texto da Assinatura Digital',
                'tipo_campo' => 'textarea',
                'descricao' => 'Texto que acompanha a assinatura digital',
                'obrigatorio' => false,
                'valor_padrao' => "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}",
                'placeholder' => 'Use {autor_nome}, {autor_cargo}, {data_assinatura} como variáveis',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Posição do QR Code
        $campoPosicaoQR = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'qrcode_posicao'
            ],
            [
                'label' => 'Posição do QR Code',
                'tipo_campo' => 'select',
                'descricao' => 'Define onde o QR Code será posicionado no documento',
                'obrigatorio' => true,
                'valor_padrao' => 'rodape_esquerda',
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
                ],
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Tamanho do QR Code
        $campoTamanhoQR = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'qrcode_tamanho'
            ],
            [
                'label' => 'Tamanho do QR Code (pixels)',
                'tipo_campo' => 'number',
                'descricao' => 'Tamanho do QR Code em pixels',
                'obrigatorio' => true,
                'valor_padrao' => '100',
                'validacao' => ['min' => 50, 'max' => 300],
                'placeholder' => 'Ex: 100',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Texto do QR Code
        $campoTextoQR = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'qrcode_texto'
            ],
            [
                'label' => 'Texto do QR Code',
                'tipo_campo' => 'textarea',
                'descricao' => 'Texto explicativo que acompanha o QR Code',
                'obrigatorio' => false,
                'valor_padrao' => "Consulte este documento online:\nProtocolo: {numero_protocolo}",
                'placeholder' => 'Use {numero_protocolo}, {numero_proposicao} como variáveis',
                'ordem' => 5,
                'ativo' => true
            ]
        );

        // Formato da URL do QR Code
        $campoURLQR = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'qrcode_url_formato'
            ],
            [
                'label' => 'Formato da URL do QR Code',
                'tipo_campo' => 'text',
                'descricao' => 'Formato da URL que será codificada no QR Code',
                'obrigatorio' => true,
                'valor_padrao' => '{base_url}/proposicoes/consulta/{numero_protocolo}',
                'placeholder' => 'Use {base_url}, {numero_protocolo}, {numero_proposicao}',
                'ordem' => 6,
                'ativo' => true
            ]
        );

        // Mostrar Assinatura Apenas Após Protocolo
        $campoAssinaturaAposProtocolo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'assinatura_apenas_protocolo'
            ],
            [
                'label' => 'Mostrar Assinatura Apenas Após Protocolo',
                'tipo_campo' => 'checkbox',
                'descricao' => 'A assinatura digital só aparece no documento após ser protocolado',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 7,
                'ativo' => true
            ]
        );

        // Mostrar QR Code Apenas Após Protocolo
        $campoQRAposProtocolo = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'qrcode_apenas_protocolo'
            ],
            [
                'label' => 'Mostrar QR Code Apenas Após Protocolo',
                'tipo_campo' => 'checkbox',
                'descricao' => 'O QR Code só aparece no documento após ser protocolado',
                'obrigatorio' => false,
                'valor_padrao' => '1',
                'ordem' => 8,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoPosicaoAssinatura, 'rodape_direita');
        $this->definirValor($campoTextoAssinatura, "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}");
        $this->definirValor($campoPosicaoQR, 'rodape_esquerda');
        $this->definirValor($campoTamanhoQR, '100');
        $this->definirValor($campoTextoQR, "Consulte este documento online:\nProtocolo: {numero_protocolo}");
        $this->definirValor($campoURLQR, '{base_url}/proposicoes/consulta/{numero_protocolo}');
        $this->definirValor($campoAssinaturaAposProtocolo, '1', 'boolean');
        $this->definirValor($campoQRAposProtocolo, '1', 'boolean');
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