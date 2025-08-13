<?php

namespace App\Services\Template;

use App\Services\TemplateVariablesService;
use App\Services\Parametro\ParametroService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TemplatePadraoABNTService
{
    protected TemplateVariablesService $templateVariablesService;
    protected ParametroService $parametroService;
    protected ABNTValidationService $abntValidationService;

    public function __construct(
        TemplateVariablesService $templateVariablesService,
        ParametroService $parametroService,
        ABNTValidationService $abntValidationService
    ) {
        $this->templateVariablesService = $templateVariablesService;
        $this->parametroService = $parametroService;
        $this->abntValidationService = $abntValidationService;
    }

    /**
     * Gerar documento usando template padrão ABNT
     */
    public function gerarDocumento(array $dadosProposicao, array $variaveisUsuario = []): array
    {
        
        try {
            // Carregar template padrão ABNT
            $templatePath = storage_path('templates/template_padrao_abnt.html');
            if (!file_exists($templatePath)) {
                return [
                    'success' => false,
                    'message' => 'Template padrão ABNT não encontrado'
                ];
            }

            $templateHTML = file_get_contents($templatePath);
            
            // Processar variáveis
            $variaveisProcessadas = $this->processarVariaveis($dadosProposicao, $variaveisUsuario);
            
            // Substituir variáveis no template
            $documentoHTML = $this->substituirVariaveis($templateHTML, $variaveisProcessadas);
            
            // Validar documento ABNT
            $validacao = $this->abntValidationService->validarDocumento($documentoHTML);
            
            // Aplicar correções automáticas se necessário
            if ($validacao['score_geral']['percentual'] < 90) {
                $correcaoResult = $this->abntValidationService->aplicarCorrecoesAutomaticas($documentoHTML);
                $documentoHTML = $correcaoResult['conteudo'];
                $variaveisProcessadas['correcoes_aplicadas'] = $correcaoResult['correcoes'];
                
                // Re-validar após correções
                $validacao = $this->abntValidationService->validarDocumento($documentoHTML);
            }

            // Log::info('Documento gerado com template padrão ABNT', [
            //     'tipo_proposicao' => $dadosProposicao['tipo'] ?? 'desconhecido',
            //     'score_abnt' => $validacao['score_geral']['percentual'] ?? 0,
            //     'variaveis_processadas' => count($variaveisProcessadas)
            // ]);

            return [
                'success' => true,
                'documento_html' => $documentoHTML,
                'variaveis_utilizadas' => $variaveisProcessadas,
                'validacao_abnt' => $validacao,
                'template_usado' => 'Template Padrão ABNT v1.0'
            ];

        } catch (\Exception $e) {
            // Log::error('Erro ao gerar documento com template padrão ABNT', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'dados_proposicao' => $dadosProposicao
            // ]);

            return [
                'success' => false,
                'message' => 'Erro ao gerar documento: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Processar variáveis do template
     */
    protected function processarVariaveis(array $dadosProposicao, array $variaveisUsuario = []): array
    {
        $variaveis = [];

        try {
            // Variáveis automáticas do sistema
            $variaveis = array_merge($variaveis, $this->obterVariaveisAutomaticas($dadosProposicao));
            
            // Variáveis fornecidas pelo usuário
            $variaveis = array_merge($variaveis, $variaveisUsuario);
            
            // Variáveis de parâmetros do sistema
            $variaveis = array_merge($variaveis, $this->obterVariaveisParametros());
            
            // Variáveis calculadas/derivadas
            $variaveis = array_merge($variaveis, $this->obterVariaveisCalculadas($dadosProposicao, $variaveis));
            
        } catch (\Exception $e) {
            // Log::warning('Erro ao processar variáveis', [
            //     'error' => $e->getMessage()
            // ]);
        }

        return $variaveis;
    }

    /**
     * Obter variáveis automáticas do sistema
     */
    protected function obterVariaveisAutomaticas(array $dadosProposicao): array
    {
        $now = now();
        
        return [
            // Dados da proposição
            'tipo_proposicao' => $this->formatarTipoProposicao($dadosProposicao['tipo'] ?? ''),
            'numero_proposicao' => $dadosProposicao['numero'] ?? 'S/N',
            'status_proposicao' => $dadosProposicao['status'] ?? 'Rascunho',
            'ementa' => $dadosProposicao['ementa'] ?? '',
            'texto' => $this->formatarTextoArticulado($dadosProposicao['conteudo'] ?? $dadosProposicao['texto'] ?? ''),
            
            // Datas
            'data_atual' => $now->format('d/m/Y'),
            'data_extenso' => $this->formatarDataExtenso($now),
            'hora_atual' => $now->format('H:i'),
            'dia_atual' => $now->format('d'),
            'mes_atual' => $this->formatarMesPortugues($now->format('n')),
            'ano_atual' => $now->format('Y'),
            'data_criacao' => isset($dadosProposicao['created_at']) ? 
                \Carbon\Carbon::parse($dadosProposicao['created_at'])->format('d/m/Y') : 
                $now->format('d/m/Y'),
            
            // Autor (se disponível)
            'autor_nome' => $dadosProposicao['autor_nome'] ?? '',
            'nome_parlamentar' => $dadosProposicao['nome_parlamentar'] ?? $dadosProposicao['autor_nome'] ?? '',
            'cargo_parlamentar' => $dadosProposicao['cargo_parlamentar'] ?? 'Vereador(a)',
            'email_parlamentar' => $dadosProposicao['email_parlamentar'] ?? '',
            'partido_parlamentar' => $dadosProposicao['partido_parlamentar'] ?? '',
            
            // Artigos numerados
            'numero_artigo_final' => $this->calcularNumeroArtigoFinal($dadosProposicao['conteudo'] ?? $dadosProposicao['texto'] ?? ''),
            'numero_artigo_revogacao' => $this->calcularNumeroArtigoFinal($dadosProposicao['conteudo'] ?? $dadosProposicao['texto'] ?? '') + 1
        ];
    }

    /**
     * Obter variáveis dos parâmetros do sistema
     */
    protected function obterVariaveisParametros(): array
    {
        $variaveis = [];
        
        try {
            // Dados institucionais
            $variaveis['municipio'] = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'municipio') ?? '';
            $variaveis['nome_camara'] = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'nome_camara') ?? '';
            $variaveis['endereco_camara'] = $this->parametroService->obterValor('Dados Gerais', 'Informações da Câmara', 'endereco_camara') ?? '';
            $variaveis['legislatura_atual'] = $this->parametroService->obterValor('Dados Gerais', 'Legislatura', 'legislatura_atual') ?? date('Y');
            $variaveis['sessao_legislativa'] = $this->parametroService->obterValor('Dados Gerais', 'Legislatura', 'sessao_legislativa') ?? date('Y');
            
            // Variáveis específicas de cabeçalho dos parâmetros Templates
            $variaveis['cabecalho_nome_camara'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_nome_camara') ?? 'CÂMARA MUNICIPAL';
            $variaveis['cabecalho_endereco'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_endereco') ?? '';
            $variaveis['cabecalho_telefone'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_telefone') ?? '';
            $variaveis['cabecalho_website'] = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_website') ?? '';
            
            // Variáveis de rodapé e outras configurações
            $variaveis['rodape_texto'] = $this->parametroService->obterValor('Templates', 'Rodapé', 'rodape_texto') ?? '';
            $variaveis['assinatura_padrao'] = $this->parametroService->obterValor('Templates', 'Variáveis Dinâmicas', 'var_assinatura_padrao') ?? '';
            
            // Imagens
            $imagemCabecalho = $this->parametroService->obterValor('Templates', 'Cabeçalho', 'cabecalho_imagem');
            if ($imagemCabecalho) {
                // Gerar código RTF para a imagem ao invés de HTML
                $caminhoCompleto = public_path($imagemCabecalho);
                if (file_exists($caminhoCompleto)) {
                    $variaveis['imagem_cabecalho'] = $this->gerarCodigoRTFImagem($caminhoCompleto);
                } else {
                    $variaveis['imagem_cabecalho'] = '';
                }
            } else {
                $variaveis['imagem_cabecalho'] = '';
            }
                
        } catch (\Exception $e) {
            // Log::warning('Erro ao obter variáveis de parâmetros', [
            //     'error' => $e->getMessage()
            // ]);
        }
        
        return $variaveis;
    }

    /**
     * Obter variáveis calculadas/derivadas
     */
    protected function obterVariaveisCalculadas(array $dadosProposicao, array $variaveisExistentes): array
    {
        return [
            // Campos específicos que podem ser derivados
            'finalidade' => $dadosProposicao['finalidade'] ?? 
                $this->extrairFinalidadeDoTexto($dadosProposicao['conteudo'] ?? ''),
            'justificativa' => $dadosProposicao['justificativa'] ?? 
                $this->gerarJustificativaPadrao($dadosProposicao),
            'considerandos' => $dadosProposicao['considerandos'] ?? '',
            'observacoes' => $dadosProposicao['observacoes'] ?? ''
        ];
    }

    /**
     * Substituir variáveis no template
     */
    protected function substituirVariaveis(string $template, array $variaveis): string
    {
        $templateProcessado = $template;
        
        foreach ($variaveis as $nome => $valor) {
            // Substituir padrões ${variavel}
            $templateProcessado = str_replace('${' . $nome . '}', $valor, $templateProcessado);
            
            // Substituir padrões $variavel (sem chaves)
            $templateProcessado = preg_replace('/\$' . $nome . '\b/', $valor, $templateProcessado);
        }
        
        // Remover variáveis não preenchidas (marcá-las visivelmente)
        $templateProcessado = preg_replace_callback('/\$\{([^}]+)\}/', function($matches) {
            return '<span class="variavel-nao-preenchida" title="Variável não definida: ' . $matches[1] . '">[' . strtoupper($matches[1]) . ']</span>';
        }, $templateProcessado);
        
        return $templateProcessado;
    }

    /**
     * Formatar tipo de proposição
     */
    protected function formatarTipoProposicao(string $tipo): string
    {
        $tipos = [
            'projeto_lei_ordinaria' => 'Projeto de Lei Ordinária',
            'projeto_lei_complementar' => 'Projeto de Lei Complementar',
            'indicacao' => 'Indicação',
            'projeto_decreto_legislativo' => 'Projeto de Decreto Legislativo',
            'projeto_resolucao' => 'Projeto de Resolução',
            'mocao' => 'Moção',
            'requerimento' => 'Requerimento',
            'emenda' => 'Emenda'
        ];

        return $tipos[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
    }

    /**
     * Formatar texto com estrutura articulada
     */
    protected function formatarTextoArticulado(string $texto): string
    {
        if (empty($texto)) {
            return '<div class="artigo"><span class="artigo-numero">Art. 1º</span> [INSERIR TEXTO DA PROPOSIÇÃO]</div>';
        }

        // Se já tem estrutura HTML, manter
        if (strpos($texto, '<') !== false) {
            return $texto;
        }

        // Converter texto simples em estrutura articulada
        $linhas = explode("\n", $texto);
        $textoFormatado = '';
        $numeroArtigo = 1;

        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if (empty($linha)) continue;

            // Detectar se já é um artigo
            if (preg_match('/^Art\.\s*(\d+)/i', $linha)) {
                $textoFormatado .= '<div class="artigo">' . $linha . '</div>' . "\n";
            } else {
                // Transformar em artigo
                $textoFormatado .= '<div class="artigo"><span class="artigo-numero">Art. ' . $numeroArtigo . 'º</span> ' . $linha . '</div>' . "\n";
                $numeroArtigo++;
            }
        }

        return $textoFormatado;
    }

    /**
     * Formatar data por extenso
     */
    protected function formatarDataExtenso(\Carbon\Carbon $data): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];

        return $data->format('d') . ' de ' . $meses[$data->format('n')] . ' de ' . $data->format('Y');
    }

    /**
     * Formatar mês em português
     */
    protected function formatarMesPortugues(int $numeroMes): string
    {
        $meses = [
            1 => 'janeiro', 2 => 'fevereiro', 3 => 'março', 4 => 'abril',
            5 => 'maio', 6 => 'junho', 7 => 'julho', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'dezembro'
        ];

        return $meses[$numeroMes] ?? 'mês inválido';
    }

    /**
     * Calcular número do último artigo
     */
    protected function calcularNumeroArtigoFinal(string $texto): int
    {
        if (preg_match_all('/Art\.\s*(\d+)/i', $texto, $matches)) {
            $numeros = array_map('intval', $matches[1]);
            return max($numeros) + 1;
        }
        
        // Se não há artigos, começar do 2 (assumindo que o texto será o Art. 1º)
        return 2;
    }

    /**
     * Extrair finalidade do texto
     */
    protected function extrairFinalidadeDoTexto(string $texto): string
    {
        // Lógica simples para extrair possível finalidade
        if (preg_match('/finalidade[:\s]*([^.]+)/i', $texto, $matches)) {
            return trim($matches[1]);
        }
        
        return '';
    }

    /**
     * Gerar justificativa padrão
     */
    protected function gerarJustificativaPadrao(array $dadosProposicao): string
    {
        $tipo = $dadosProposicao['tipo'] ?? '';
        $ementa = $dadosProposicao['ementa'] ?? '';
        
        if (empty($ementa)) {
            return 'A presente proposição tem como objetivo atender às necessidades da população e promover o bem comum, conforme preceitos constitucionais e legais vigentes.';
        }
        
        return "A presente proposição justifica-se pela necessidade de {$ementa}. A medida proposta visa atender ao interesse público e social, promovendo melhorias para a comunidade e garantindo o cumprimento dos princípios da administração pública.";
    }

    /**
     * Obter estatísticas do template
     */
    public function obterEstatisticas(): array
    {
        try {
            $templatePath = storage_path('templates/template_padrao_abnt.html');
            
            if (!file_exists($templatePath)) {
                return [
                    'template_existe' => false,
                    'erro' => 'Template não encontrado'
                ];
            }
            
            $templateHTML = file_get_contents($templatePath);
            $validacao = $this->abntValidationService->validarDocumento($templateHTML);
            
            // Extrair variáveis do template
            $variaveisEncontradas = [];
            if (preg_match_all('/\$\{([^}]+)\}/', $templateHTML, $matches)) {
                $variaveisEncontradas = array_unique($matches[1]);
            }
            
            return [
                'template_existe' => true,
                'tamanho_arquivo' => strlen($templateHTML),
                'total_variaveis' => count($variaveisEncontradas),
                'variaveis_encontradas' => $variaveisEncontradas,
                'validacao_abnt' => $validacao,
                'ultima_modificacao' => date('d/m/Y H:i:s', filemtime($templatePath))
            ];
            
        } catch (\Exception $e) {
            return [
                'template_existe' => false,
                'erro' => $e->getMessage()
            ];
        }
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
            // Fallback para placeholder se houver erro
            $nomeArquivo = basename($caminhoImagem);
            return "{\\qc\\b\\fs20 [INSERIR IMAGEM: {$nomeArquivo}]\\par}";
        }
    }
}