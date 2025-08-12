<?php

namespace App\Services\Template;

use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroValor;
use App\Models\Proposicao;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class TemplateParametrosService
{
    /**
     * Obter todos os parâmetros dos módulos Templates e Dados Gerais
     */
    public function obterParametrosTemplates(): array
    {
        // Desabilitar cache temporariamente para debug
        // return Cache::remember('parametros.templates', 3600, function () {
        {
            $parametros = [];
            
            // Obter parâmetros do módulo Templates
            $moduloTemplates = ParametroModulo::where('nome', 'Templates')
                ->with(['submodulos.campos.valores'])
                ->first();
            
            if ($moduloTemplates) {
                foreach ($moduloTemplates->submodulos as $submodulo) {
                    foreach ($submodulo->campos as $campo) {
                        $chave = $submodulo->nome . '.' . $campo->nome;
                        // Usar o valor mais recente válido
                        $valorAtual = $campo->valores()->whereNull('valido_ate')->orWhere('valido_ate', '>', now())->latest()->first();
                        $parametros[$chave] = $valorAtual ? $valorAtual->valor_formatado : $campo->valor_padrao;
                    }
                }
            }
            
            // Obter parâmetros do módulo Dados Gerais
            $moduloDadosGerais = ParametroModulo::where('nome', 'Dados Gerais')
                ->with(['submodulos.campos.valores'])
                ->first();
            
            if ($moduloDadosGerais) {
                foreach ($moduloDadosGerais->submodulos as $submodulo) {
                    foreach ($submodulo->campos as $campo) {
                        $chave = $submodulo->nome . '.' . $campo->nome;
                        // Usar o valor mais recente válido
                        $valorAtual = $campo->valores()->whereNull('valido_ate')->orWhere('valido_ate', '>', now())->latest()->first();
                        $parametros[$chave] = $valorAtual ? $valorAtual->valor_formatado : $campo->valor_padrao;
                    }
                }
            }

            return count($parametros) > 0 ? $parametros : $this->getParametrosPadrao();
        }
    }

    /**
     * Obter variáveis disponíveis para substituição
     */
    public function obterVariaveisDisponiveis(): array
    {
        return [
            // Dados da Proposição
            '${numero_proposicao}' => 'Número da proposição',
            '${tipo_proposicao}' => 'Tipo da proposição',
            '${ementa}' => 'Ementa da proposição',
            '${texto}' => 'Texto principal',
            '${justificativa}' => 'Justificativa',
            '${ano}' => 'Ano da proposição',
            '${protocolo}' => 'Número do protocolo',
            
            // Dados do Autor
            '${autor_nome}' => 'Nome do autor',
            '${autor_cargo}' => 'Cargo do autor',
            '${autor_partido}' => 'Partido do autor',
            
            // Datas
            '${data_atual}' => 'Data atual',
            '${data_criacao}' => 'Data de criação',
            '${data_protocolo}' => 'Data do protocolo',
            '${dia}' => 'Dia atual',
            '${mes}' => 'Mês atual',
            '${ano_atual}' => 'Ano atual',
            '${mes_extenso}' => 'Mês por extenso',
            
            // Dados da Câmara (parâmetros dinâmicos)
            '${nome_camara}' => 'Nome oficial da Câmara',
            '${nome_camara_abreviado}' => 'Sigla da Câmara', 
            '${municipio}' => 'Nome do município',
            '${municipio_uf}' => 'Estado (UF)',
            '${endereco_camara}' => 'Logradouro da Câmara',
            '${endereco_completo}' => 'Endereço completo com número, complemento, bairro e CEP',
            '${endereco_bairro}' => 'Bairro da Câmara',
            '${endereco_cep}' => 'CEP da Câmara',
            '${telefone_camara}' => 'Telefone principal',
            '${telefone_protocolo}' => 'Telefone secundário',
            '${email_camara}' => 'E-mail institucional',
            '${website_camara}' => 'Website oficial',
            '${cnpj_camara}' => 'CNPJ da Câmara',
            '${presidente_nome}' => 'Nome do Presidente',
            '${presidente_partido}' => 'Partido do Presidente',
            '${legislatura_atual}' => 'Legislatura atual',
            '${numero_vereadores}' => 'Número de vereadores',
            '${presidente_tratamento}' => 'Tratamento do Presidente',
            '${horario_funcionamento}' => 'Horário de funcionamento',
            '${horario_atendimento}' => 'Horário de atendimento',
            '${horario_protocolo}' => 'Horário de atendimento ao público',
            '${imagem_cabecalho}' => 'Imagem do cabeçalho',
            
            // Formatação e Templates
            '${assinatura_padrao}' => 'Área de assinatura padrão',
            '${rodape}' => 'Texto do rodapé',
            '${cabecalho_nome_camara}' => 'Nome da câmara no cabeçalho',
            '${cabecalho_endereco}' => 'Endereço no cabeçalho',
            '${cabecalho_telefone}' => 'Telefone no cabeçalho',
            '${cabecalho_website}' => 'Website no cabeçalho',
            '${rodape_texto}' => 'Texto personalizado do rodapé',
            '${formato_fonte}' => 'Fonte padrão dos documentos',
            '${tamanho_fonte}' => 'Tamanho da fonte',
            '${espacamento_linhas}' => 'Espaçamento entre linhas',
            '${margens}' => 'Margens do documento',
            '${prefixo_numeracao}' => 'Prefixo para numeração',
            '${formato_data}' => 'Formato de exibição de datas'
        ];
    }

    /**
     * Processar template com substituição de variáveis
     */
    public function processarTemplate(string $conteudo, array $dados = []): string
    {
        // Obter parâmetros do sistema
        $parametros = $this->obterParametrosTemplates();
        
        // Preparar variáveis de substituição
        $variaveis = $this->prepararVariaveis($dados, $parametros);
        
        // Realizar substituições
        foreach ($variaveis as $chave => $valor) {
            // Converter sequências de escape como \n em quebras de linha reais
            $valorProcessado = str_replace(['\\n', '\\r\\n', '\\t'], ["\n", "\r\n", "\t"], $valor);
            $conteudo = str_replace($chave, $valorProcessado, $conteudo);
        }
        
        return $conteudo;
    }

    /**
     * Preparar variáveis para substituição
     */
    private function prepararVariaveis(array $dados, array $parametros): array
    {
        $variaveis = [];
        
        // Dados da proposição
        if (isset($dados['proposicao']) && $dados['proposicao'] instanceof Proposicao) {
            $proposicao = $dados['proposicao'];
            $variaveis['${numero_proposicao}'] = $proposicao->numero ?? '';
            $variaveis['${tipo_proposicao}'] = $proposicao->tipo ?? '';
            $variaveis['${ementa}'] = $proposicao->ementa ?? '';
            $variaveis['${texto}'] = $proposicao->conteudo ?? '';
            $variaveis['${justificativa}'] = $proposicao->justificativa ?? '';
            $variaveis['${ano}'] = $proposicao->ano ?? date('Y');
            $variaveis['${protocolo}'] = $proposicao->protocolo ?? '';
            $variaveis['${data_criacao}'] = $proposicao->created_at ? $proposicao->created_at->format('d/m/Y') : '';
            $variaveis['${data_protocolo}'] = $proposicao->data_protocolo ? $proposicao->data_protocolo->format('d/m/Y') : '';
        }
        
        // Dados do autor
        if (isset($dados['autor']) && $dados['autor'] instanceof User) {
            $autor = $dados['autor'];
            $variaveis['${autor_nome}'] = $autor->name ?? '';
            $variaveis['${autor_cargo}'] = $autor->cargo ?? 'Vereador(a)';
            $variaveis['${autor_partido}'] = $autor->partido ?? '';
        }
        
        // Datas
        $dataAtual = now();
        $variaveis['${data_atual}'] = $dataAtual->format($parametros['Variáveis Dinâmicas.var_formato_data'] ?? 'd/m/Y');
        $variaveis['${dia}'] = $dataAtual->format('d');
        $variaveis['${mes}'] = $dataAtual->format('m');
        $variaveis['${ano_atual}'] = $dataAtual->format('Y');
        $variaveis['${mes_extenso}'] = $this->mesExtenso($dataAtual->format('n'));
        
        // Dados da Câmara (usando estrutura atual dos parâmetros)
        $variaveis['${nome_camara}'] = $parametros['Identificação.nome_camara'] ?? 
                                      $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL';
        
        $variaveis['${nome_camara_abreviado}'] = $parametros['Identificação.sigla_camara'] ?? 'CÂMARA';
        
        $variaveis['${municipio}'] = $parametros['Endereço.cidade'] ?? 
                                    $this->extrairMunicipio($parametros['Cabeçalho.cabecalho_nome_camara'] ?? '');
        
        $variaveis['${municipio_uf}'] = $parametros['Endereço.estado'] ?? 'SP';
        
        // Endereço completo e componentes
        $logradouro = $parametros['Endereço.endereco'] ?? '';
        $numero = $parametros['Endereço.numero'] ?? '';
        $complemento = $parametros['Endereço.complemento'] ?? '';
        $bairro = $parametros['Endereço.bairro'] ?? '';
        $cep = $parametros['Endereço.cep'] ?? '';
        
        $enderecoCompleto = trim($logradouro . 
                                ($numero ? ", {$numero}" : '') . 
                                ($complemento ? ", {$complemento}" : '') . 
                                ($bairro ? " - {$bairro}" : '') . 
                                ($cep ? " - CEP: {$cep}" : ''));
        
        $variaveis['${endereco_camara}'] = $logradouro ?: ($parametros['Cabeçalho.cabecalho_endereco'] ?? '');
        $variaveis['${endereco_completo}'] = $enderecoCompleto;
        $variaveis['${endereco_bairro}'] = $bairro;
        $variaveis['${endereco_cep}'] = $cep;
        
        // Contatos
        $variaveis['${telefone_camara}'] = $parametros['Contatos.telefone'] ?? 
                                          $parametros['Cabeçalho.cabecalho_telefone'] ?? '';
        
        $variaveis['${telefone_protocolo}'] = $parametros['Contatos.telefone_secundario'] ?? '';
        
        $variaveis['${email_camara}'] = $parametros['Contatos.email_institucional'] ?? '';
        
        $variaveis['${website_camara}'] = $parametros['Contatos.website'] ?? 
                                         $parametros['Cabeçalho.cabecalho_website'] ?? '';
        
        // Dados administrativos  
        $variaveis['${cnpj_camara}'] = $parametros['Identificação.cnpj'] ?? '';
        $variaveis['${presidente_nome}'] = $parametros['Gestão.presidente_nome'] ?? '';
        $variaveis['${presidente_partido}'] = $parametros['Gestão.presidente_partido'] ?? '';
        $variaveis['${legislatura_atual}'] = $parametros['Gestão.legislatura_atual'] ?? '';
        $variaveis['${numero_vereadores}'] = $parametros['Gestão.numero_vereadores'] ?? '';
        $variaveis['${presidente_tratamento}'] = 'Excelentíssimo Senhor';
        
        // Horários
        $variaveis['${horario_funcionamento}'] = $parametros['Funcionamento.horario_funcionamento'] ?? '';
        $variaveis['${horario_atendimento}'] = $parametros['Funcionamento.horario_atendimento'] ?? '';
        $variaveis['${horario_protocolo}'] = $parametros['Funcionamento.horario_atendimento'] ?? '';
        
        // Imagem de cabeçalho - gerar URL completa da imagem
        $imagemCabecalho = $parametros['Cabeçalho.cabecalho_imagem'] ?? '';
        if (!empty($imagemCabecalho)) {
            // Tentar localizar o arquivo de imagem nos diretórios padrão
            $possiveisCaminhos = [
                "template/{$imagemCabecalho}",
                "storage/template/{$imagemCabecalho}",
                $imagemCabecalho
            ];
            
            $caminhoImagem = '';
            foreach ($possiveisCaminhos as $caminho) {
                if (file_exists(public_path($caminho))) {
                    $caminhoImagem = public_path($caminho);
                    break;
                }
            }
            
            // Se não encontrou o arquivo específico, usar imagem padrão
            if (empty($caminhoImagem)) {
                if (file_exists(public_path('template/cabecalho.png'))) {
                    $caminhoImagem = public_path('template/cabecalho.png');
                } else {
                    // Fallback para texto simples se não há imagem
                    $variaveis['${imagem_cabecalho}'] = '[IMAGEM DO CABEÇALHO]';
                }
            }
            
            // Gerar código RTF para inserir a imagem apenas se temos um caminho válido
            if (!empty($caminhoImagem)) {
                $codigoRTFImagem = $this->gerarCodigoRTFImagem($caminhoImagem);
                $variaveis['${imagem_cabecalho}'] = $codigoRTFImagem;
            }
        } else {
            // Se não há imagem, remover o placeholder completamente
            $variaveis['${imagem_cabecalho}'] = '';
        }
        
        // Formatação - só incluir assinatura se não estiver vazia
        $assinaturaPadrao = $parametros['Variáveis Dinâmicas.var_assinatura_padrao'] ?? '';
        if (!empty($assinaturaPadrao)) {
            $variaveis['${assinatura_padrao}'] = $assinaturaPadrao;
        } else {
            // Se não há assinatura, remover o placeholder completamente
            $variaveis['${assinatura_padrao}'] = '';
        }
        
        $variaveis['${rodape}'] = $parametros['Rodapé.rodape_texto'] ?? '';

        // Mapeamentos diretos para as novas variáveis de cabeçalho/rodapé
        $variaveis['${cabecalho_nome_camara}'] = $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL';
        $variaveis['${cabecalho_endereco}'] = $parametros['Cabeçalho.cabecalho_endereco'] ?? '';
        $variaveis['${cabecalho_telefone}'] = $parametros['Cabeçalho.cabecalho_telefone'] ?? '';
        $variaveis['${cabecalho_website}'] = $parametros['Cabeçalho.cabecalho_website'] ?? '';
        $variaveis['${rodape_texto}'] = $parametros['Rodapé.rodape_texto'] ?? '';
        
        // Variáveis de formatação
        $variaveis['${formato_fonte}'] = $parametros['Formatação.format_fonte'] ?? 'Arial';
        $variaveis['${tamanho_fonte}'] = $parametros['Formatação.format_tamanho_fonte'] ?? '12';
        $variaveis['${espacamento_linhas}'] = $parametros['Formatação.format_espacamento'] ?? '1.5';
        $variaveis['${margens}'] = $parametros['Formatação.format_margens'] ?? '2.5, 2.5, 3, 2';
        
        // Variáveis dinâmicas configuráveis
        $variaveis['${prefixo_numeracao}'] = $parametros['Variáveis Dinâmicas.var_prefixo_numeracao'] ?? 'PROP';
        $variaveis['${formato_data}'] = $parametros['Variáveis Dinâmicas.var_formato_data'] ?? 'd/m/Y';
        
        // Adicionar variáveis customizadas passadas diretamente
        if (isset($dados['variaveis'])) {
            foreach ($dados['variaveis'] as $chave => $valor) {
                $variaveis['${' . $chave . '}'] = $valor;
            }
        }
        
        return $variaveis;
    }

    /**
     * Extrair município do nome da câmara
     */
    private function extrairMunicipio(string $nomeCamara): string
    {
        // Remove "CÂMARA MUNICIPAL DE " para obter o município
        $patterns = [
            '/^CÂMARA MUNICIPAL DE /i',
            '/^CÂMARA DE /i',
            '/^CÂMARA /i'
        ];
        
        foreach ($patterns as $pattern) {
            $municipio = preg_replace($pattern, '', $nomeCamara);
            if ($municipio !== $nomeCamara) {
                return $municipio;
            }
        }
        
        return '';
    }

    /**
     * Converter mês para extenso
     */
    private function mesExtenso(int $mes): string
    {
        $meses = [
            1 => 'janeiro',
            2 => 'fevereiro',
            3 => 'março',
            4 => 'abril',
            5 => 'maio',
            6 => 'junho',
            7 => 'julho',
            8 => 'agosto',
            9 => 'setembro',
            10 => 'outubro',
            11 => 'novembro',
            12 => 'dezembro'
        ];
        
        return $meses[$mes] ?? '';
    }

    /**
     * Obter parâmetros padrão caso não existam no banco
     */
    private function getParametrosPadrao(): array
    {
        return [
            'Cabeçalho.cabecalho_nome_camara' => 'CÂMARA MUNICIPAL',
            'Cabeçalho.cabecalho_endereco' => '',
            'Cabeçalho.cabecalho_telefone' => '',
            'Cabeçalho.cabecalho_website' => '',
            'Rodapé.rodape_texto' => 'Documento oficial',
            'Rodapé.rodape_numeracao' => true,
            'Variáveis Dinâmicas.var_prefixo_numeracao' => 'PROP',
            'Variáveis Dinâmicas.var_formato_data' => 'd/m/Y',
            'Variáveis Dinâmicas.var_assinatura_padrao' => "Sala das Sessões, em _____ de _____________ de _______.\n\n\n_________________________________\nVereador(a)",
            'Formatação.format_fonte' => 'Arial',
            'Formatação.format_tamanho_fonte' => '12',
            'Formatação.format_espacamento' => '1.5',
            'Formatação.format_margens' => '2.5, 2.5, 3, 2'
        ];
    }

    /**
     * Limpar cache de parâmetros
     */
    public function limparCache(): void
    {
        Cache::forget('parametros.templates');
    }

    /**
     * Gerar código RTF para inserir uma imagem
     */
    private function gerarCodigoRTFImagem(string $caminhoImagem): string
    {
        try {
            // Verificar se arquivo existe e obter informações
            if (!file_exists($caminhoImagem)) {
                return '[IMAGEM DO CABEÇALHO - ARQUIVO NÃO ENCONTRADO]';
            }
            
            $info = getimagesize($caminhoImagem);
            if (!$info) {
                return '[IMAGEM DO CABEÇALHO - FORMATO INVÁLIDO]';
            }
            
            // Para o OnlyOffice, vamos inserir a imagem usando código RTF específico
            // Primeiro, converter a imagem para formato hexadecimal
            $imagemData = file_get_contents($caminhoImagem);
            $imagemHex = bin2hex($imagemData);
            
            // Obter dimensões da imagem
            $largura = $info[0];
            $altura = $info[1];
            
            // Redimensionar se necessário (máximo 200px de largura para evitar arquivo muito grande)
            if ($largura > 200) {
                $novaLargura = 200;
                $novaAltura = intval(($novaLargura * $altura) / $largura);
            } else {
                $novaLargura = $largura;
                $novaAltura = $altura;
            }
            
            // Converter para twips (1 pixel = 15 twips aprox)
            $larguraTwips = $novaLargura * 15;
            $alturaTwips = $novaAltura * 15;
            
            // Determinar o tipo MIME da imagem
            $tipoImagem = $info['mime'];
            $formatoRTF = match($tipoImagem) {
                'image/png' => 'pngblip',
                'image/jpeg', 'image/jpg' => 'jpegblip',
                default => 'pngblip'
            };
            
            // Gerar código RTF para inserir a imagem
            $rtfImagem = "{\pict\\{$formatoRTF}\\picw{$largura}\\pich{$altura}\\picwgoal{$larguraTwips}\\pichgoal{$alturaTwips} {$imagemHex}}";
            
            // Centralizar a imagem
            return "{\\qc {$rtfImagem}\\par}";
            
        } catch (\Exception $e) {
            // Log::warning('Erro ao gerar código RTF para imagem:', [
            //     'caminho' => $caminhoImagem,
            //     'error' => $e->getMessage()
            // ]);
            
            // Fallback para placeholder se houver erro
            $nomeArquivo = basename($caminhoImagem);
            return "{\\qc\\b\\fs20 [INSERIR IMAGEM: {$nomeArquivo}]\\par}";
        }
    }
}