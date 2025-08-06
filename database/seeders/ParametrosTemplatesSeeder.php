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

        // Nome da Câmara
        $campoNomeCamara = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'cabecalho_nome_camara'
            ],
            [
                'label' => 'Nome da Câmara',
                'tipo_campo' => 'text',
                'descricao' => 'Nome completo da câmara municipal',
                'obrigatorio' => true,
                'valor_padrao' => 'CÂMARA MUNICIPAL',
                'placeholder' => 'Ex: CÂMARA MUNICIPAL DE SÃO PAULO',
                'ordem' => 2,
                'ativo' => true
            ]
        );

        // Endereço
        $campoEndereco = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'cabecalho_endereco'
            ],
            [
                'label' => 'Endereço',
                'tipo_campo' => 'textarea',
                'descricao' => 'Endereço completo da câmara',
                'obrigatorio' => false,
                'placeholder' => 'Rua, número, bairro, cidade, estado, CEP',
                'ordem' => 3,
                'ativo' => true
            ]
        );

        // Telefone
        $campoTelefone = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'cabecalho_telefone'
            ],
            [
                'label' => 'Telefone',
                'tipo_campo' => 'text',
                'descricao' => 'Telefone de contato',
                'obrigatorio' => false,
                'placeholder' => '(00) 0000-0000',
                'ordem' => 4,
                'ativo' => true
            ]
        );

        // Website
        $campoWebsite = ParametroCampo::updateOrCreate(
            [
                'submodulo_id' => $submodulo->id,
                'nome' => 'cabecalho_website'
            ],
            [
                'label' => 'Website',
                'tipo_campo' => 'text',
                'descricao' => 'Site da câmara',
                'obrigatorio' => false,
                'placeholder' => 'www.camara.gov.br',
                'ordem' => 5,
                'ativo' => true
            ]
        );

        // Definir valores padrão
        $this->definirValor($campoNomeCamara, 'CÂMARA MUNICIPAL DE SÃO PAULO');
        $this->definirValor($campoEndereco, "Viaduto Jacareí, 100\nBela Vista - São Paulo/SP\nCEP: 01319-900");
        $this->definirValor($campoTelefone, '(11) 3396-4000');
        $this->definirValor($campoWebsite, 'www.saopaulo.sp.leg.br');
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
        $this->definirValor($campoTextoRodape, 'Documento oficial da Câmara Municipal');
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
        $this->definirValor($campoAssinatura, "Sala das Sessões, em _____ de _____________ de _______.\n\n\n_________________________________\nVereador(a)");
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