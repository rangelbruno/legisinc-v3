<?php

namespace App\Services\Template;

use App\Services\Parametro\ParametroService;
use Illuminate\Support\Facades\Log;

class TemplateVariableService
{
    protected ParametroService $parametroService;

    public function __construct(ParametroService $parametroService)
    {
        $this->parametroService = $parametroService;
    }

    /**
     * Obter todas as variáveis disponíveis para templates
     * Mapeia os parâmetros configurados para variáveis de template
     */
    public function getTemplateVariables(): array
    {
        $variables = [];

        try {
            // Variáveis do módulo Templates - Cabeçalho
            $variables['cabecalho_imagem'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_imagem') ?: 'template/cabecalho.png';
            $variables['imagem_cabecalho'] = $variables['cabecalho_imagem']; // Compatibilidade para templates RTF
            $variables['cabecalho_nome_camara'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_nome_camara') ?: 'CÂMARA MUNICIPAL';
            $variables['cabecalho_endereco'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_endereco') ?: '';
            $variables['cabecalho_telefone'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_telefone') ?: '';
            $variables['cabecalho_website'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_website') ?: '';

            // Variáveis do módulo Templates - Rodapé
            $variables['rodape_texto'] = $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto') ?: 'Documento oficial da Câmara Municipal';
            $variables['rodape_numeracao'] = $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_numeracao') ?: '1';

            // Variáveis do módulo Templates - Variáveis Dinâmicas
            $variables['var_prefixo_numeracao'] = $this->parametroService->obterValor('Templates', 'Variáveis Dinâmicas', 'var_prefixo_numeracao') ?: 'PROP';
            $variables['var_formato_data'] = $this->parametroService->obterValor('Templates', 'Variáveis Dinâmicas', 'var_formato_data') ?: 'd/m/Y';
            $variables['var_assinatura_padrao'] = $this->parametroService->obterValor('Templates', 'Variáveis Dinâmicas', 'var_assinatura_padrao') ?: "Sala das Sessões, em _____ de _____________ de _______.\n\n\n_________________________________\nVereador(a)";
            $variables['assinatura_padrao'] = $variables['var_assinatura_padrao']; // Compatibilidade para templates RTF

            // Variáveis do módulo Templates - Formatação
            $variables['format_fonte'] = $this->parametroService->obterValor('Templates', 'Formatação', 'format_fonte') ?: 'Arial';
            $variables['format_tamanho_fonte'] = $this->parametroService->obterValor('Templates', 'Formatação', 'format_tamanho_fonte') ?: '12';
            $variables['format_espacamento'] = $this->parametroService->obterValor('Templates', 'Formatação', 'format_espacamento') ?: '1.5';
            $variables['format_margens'] = $this->parametroService->obterValor('Templates', 'Formatação', 'format_margens') ?: '2.5, 2.5, 3, 2';

            // Variáveis do módulo Templates - Assinatura e QR Code
            $variables['assinatura_posicao'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_posicao') ?: 'rodape_direita';
            $variables['assinatura_texto'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_texto') ?: "Documento assinado digitalmente por:\n{autor_nome}\n{autor_cargo}\nEm {data_assinatura}";
            $variables['qrcode_posicao'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_posicao') ?: 'rodape_esquerda';
            $variables['qrcode_tamanho'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_tamanho') ?: '100';
            $variables['qrcode_texto'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_texto') ?: "Consulte este documento online:\nProtocolo: {numero_protocolo}";
            $variables['qrcode_url_formato'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_url_formato') ?: '{base_url}/proposicoes/consulta/{numero_protocolo}';
            $variables['assinatura_apenas_protocolo'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'assinatura_apenas_protocolo') ?: '1';
            $variables['qrcode_apenas_protocolo'] = $this->parametroService->obterValor('Templates', 'Assinatura e QR Code', 'qrcode_apenas_protocolo') ?: '1';

            // Variáveis do módulo Dados Gerais - Identificação
            $variables['nome_camara'] = $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'nome_camara') ?: 'Câmara Municipal';
            $variables['sigla_camara'] = $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'sigla_camara') ?: 'CM';
            $variables['cnpj'] = $this->parametroService->obterValor('Dados Gerais', 'Identificação', 'cnpj') ?: '';

            // Variáveis do módulo Dados Gerais - Endereço
            $variables['endereco'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'endereco') ?: '';
            $variables['numero'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'numero') ?: '';
            $variables['complemento'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'complemento') ?: '';
            $variables['bairro'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'bairro') ?: '';
            $variables['cidade'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'cidade') ?: '';
            $variables['municipio'] = $variables['cidade'] ?: 'Caraguatatuba'; // Alias para compatibilidade
            $variables['estado'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'estado') ?: 'SP';
            $variables['cep'] = $this->parametroService->obterValor('Dados Gerais', 'Endereço', 'cep') ?: '';

            // Variáveis do módulo Dados Gerais - Contatos
            $variables['telefone'] = $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'telefone') ?: '';
            $variables['telefone_secundario'] = $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'telefone_secundario') ?: '';
            $variables['email_institucional'] = $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'email_institucional') ?: '';
            $variables['email_contato'] = $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'email_contato') ?: '';
            $variables['website'] = $this->parametroService->obterValor('Dados Gerais', 'Contatos', 'website') ?: '';

            // Variáveis do módulo Dados Gerais - Funcionamento
            $variables['horario_funcionamento'] = $this->parametroService->obterValor('Dados Gerais', 'Funcionamento', 'horario_funcionamento') ?: 'Segunda a Sexta, 8h às 17h';
            $variables['horario_atendimento'] = $this->parametroService->obterValor('Dados Gerais', 'Funcionamento', 'horario_atendimento') ?: 'Segunda a Sexta, 8h às 16h';

            // Variáveis do módulo Dados Gerais - Gestão
            $variables['presidente_nome'] = $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'presidente_nome') ?: '';
            $variables['presidente_partido'] = $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'presidente_partido') ?: '';
            $variables['legislatura_atual'] = $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'legislatura_atual') ?: '2021-2024';
            $variables['numero_vereadores'] = $this->parametroService->obterValor('Dados Gerais', 'Gestão', 'numero_vereadores') ?: '9';

            // Variáveis compostas (endereço completo)
            $enderecoCompleto = [];
            if ($variables['endereco']) {
                $enderecoCompleto[] = $variables['endereco'];
                if ($variables['numero']) {
                    $enderecoCompleto[0] .= ', ' . $variables['numero'];
                }
                if ($variables['complemento']) {
                    $enderecoCompleto[0] .= ' - ' . $variables['complemento'];
                }
            }
            if ($variables['bairro']) {
                $enderecoCompleto[] = $variables['bairro'];
            }
            if ($variables['cidade'] && $variables['estado']) {
                $enderecoCompleto[] = $variables['cidade'] . '/' . $variables['estado'];
            }
            if ($variables['cep']) {
                $enderecoCompleto[] = 'CEP: ' . $variables['cep'];
            }
            $variables['endereco_completo'] = implode("\n", $enderecoCompleto);

            // Variáveis de data e hora
            $variables['data_atual'] = date($variables['var_formato_data']);
            $variables['ano'] = date('Y');
            $variables['ano_atual'] = date('Y'); // Compatibilidade para templates RTF
            $variables['mes'] = date('m');
            $variables['dia'] = date('d');
            
            // Mês por extenso em português
            $meses = ['', 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 
                     'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
            $variables['mes_extenso'] = $meses[(int)date('n')];
            
            // Variáveis específicas para templates RTF (serão substituídas dinamicamente nas proposições)
            $variables['numero_proposicao'] = '{numero_proposicao}';
            $variables['ementa'] = '{ementa}';
            $variables['texto'] = '{texto}';
            $variables['justificativa'] = '{justificativa}';
            $variables['autor_nome'] = '{autor_nome}';
            $variables['autor_cargo'] = '{autor_cargo}';
            
            // Variáveis específicas de assinatura digital e QR Code (dinâmicas)
            $variables['assinatura_digital_info'] = '{assinatura_digital_info}'; // Bloco completo da assinatura
            $variables['qrcode_html'] = '{qrcode_html}'; // HTML do QR Code
            $variables['data_assinatura'] = '{data_assinatura}'; // Data da assinatura digital
            $variables['certificado_digital'] = '{certificado_digital}'; // Info do certificado
            $variables['ip_assinatura'] = '{ip_assinatura}'; // IP de onde foi assinado

            // Variáveis de paginação (serão substituídas dinamicamente)
            $variables['pagina_atual'] = '1';
            $variables['total_paginas'] = '1';

        } catch (\Exception $e) {
            // Log::error('Erro ao obter variáveis de template', [
            //     'error' => $e->getMessage()
            // ]);
        }

        return $variables;
    }

    /**
     * Substituir variáveis no conteúdo do template
     */
    public function replaceVariables(string $content, array $customVariables = []): string
    {
        // Obter variáveis padrão
        $variables = $this->getTemplateVariables();
        
        // Mesclar com variáveis customizadas
        $variables = array_merge($variables, $customVariables);

        // Substituir as variáveis no formato ${variavel}
        foreach ($variables as $key => $value) {
            $content = str_replace('${' . $key . '}', $value, $content);
        }

        return $content;
    }

    /**
     * Listar todas as variáveis disponíveis com suas descrições
     */
    public function listAvailableVariables(): array
    {
        return [
            // Cabeçalho
            '${cabecalho_imagem}' => 'Imagem do brasão ou logo da câmara',
            '${imagem_cabecalho}' => 'Imagem do brasão ou logo da câmara (alias RTF)',
            '${cabecalho_nome_camara}' => 'Nome completo da câmara municipal',
            '${cabecalho_endereco}' => 'Endereço da câmara no cabeçalho',
            '${cabecalho_telefone}' => 'Telefone no cabeçalho',
            '${cabecalho_website}' => 'Website no cabeçalho',
            
            // Rodapé
            '${rodape_texto}' => 'Texto do rodapé',
            '${rodape_numeracao}' => 'Exibir numeração de página',
            
            // Variáveis Dinâmicas
            '${var_prefixo_numeracao}' => 'Prefixo da numeração de proposições',
            '${var_formato_data}' => 'Formato de data',
            '${var_assinatura_padrao}' => 'Texto padrão de assinatura',
            '${assinatura_padrao}' => 'Área de assinatura padrão (alias RTF)',
            
            // Formatação
            '${format_fonte}' => 'Fonte padrão',
            '${format_tamanho_fonte}' => 'Tamanho da fonte',
            '${format_espacamento}' => 'Espaçamento entre linhas',
            '${format_margens}' => 'Margens do documento',
            
            // Assinatura Digital e QR Code
            '${assinatura_posicao}' => 'Posição da assinatura digital no documento',
            '${assinatura_texto}' => 'Texto que acompanha a assinatura digital',
            '${qrcode_posicao}' => 'Posição do QR Code no documento',
            '${qrcode_tamanho}' => 'Tamanho do QR Code em pixels',
            '${qrcode_texto}' => 'Texto explicativo do QR Code',
            '${qrcode_url_formato}' => 'Formato da URL codificada no QR Code',
            '${assinatura_apenas_protocolo}' => 'Exibir assinatura apenas após protocolo',
            '${qrcode_apenas_protocolo}' => 'Exibir QR Code apenas após protocolo',
            
            // Dados da Câmara
            '${nome_camara}' => 'Nome da Câmara Municipal',
            '${sigla_camara}' => 'Sigla da Câmara',
            '${cnpj}' => 'CNPJ da Câmara',
            
            // Endereço
            '${endereco}' => 'Logradouro',
            '${numero}' => 'Número',
            '${complemento}' => 'Complemento',
            '${bairro}' => 'Bairro',
            '${cidade}' => 'Cidade',
            '${municipio}' => 'Município (alias de cidade)',
            '${estado}' => 'Estado (UF)',
            '${cep}' => 'CEP',
            '${endereco_completo}' => 'Endereço completo formatado',
            
            // Contatos
            '${telefone}' => 'Telefone principal',
            '${telefone_secundario}' => 'Telefone secundário',
            '${email_institucional}' => 'E-mail institucional',
            '${email_contato}' => 'E-mail de contato',
            '${website}' => 'Website oficial',
            
            // Funcionamento
            '${horario_funcionamento}' => 'Horário de funcionamento',
            '${horario_atendimento}' => 'Horário de atendimento ao público',
            
            // Gestão
            '${presidente_nome}' => 'Nome do presidente da Câmara',
            '${presidente_partido}' => 'Partido do presidente',
            '${legislatura_atual}' => 'Período da legislatura atual',
            '${numero_vereadores}' => 'Número de vereadores',
            
            // Data e Hora
            '${data_atual}' => 'Data atual formatada',
            '${ano}' => 'Ano atual',
            '${mes}' => 'Mês atual',
            '${dia}' => 'Dia atual',
            
            // Paginação
            '${pagina_atual}' => 'Número da página atual',
            '${total_paginas}' => 'Total de páginas',
            
            // Proposição (preenchidas dinamicamente)
            '${numero_proposicao}' => 'Número da proposição',
            '${ementa}' => 'Ementa da proposição',
            '${texto}' => 'Texto/conteúdo da proposição',
            '${justificativa}' => 'Justificativa da proposição',
            '${autor_nome}' => 'Nome do autor da proposição',
            '${autor_cargo}' => 'Cargo do autor (ex: Vereador)',
            
            // Assinatura Digital e QR Code (dinâmicas - processadas após assinatura/protocolo)
            '${assinatura_digital_info}' => 'Bloco completo da assinatura digital formatado',
            '${qrcode_html}' => 'HTML do QR Code para consulta online',
            '${data_assinatura}' => 'Data e hora da assinatura digital',
            '${certificado_digital}' => 'Informações do certificado digital usado',
            '${ip_assinatura}' => 'Endereço IP de onde foi realizada a assinatura',
        ];
    }
}