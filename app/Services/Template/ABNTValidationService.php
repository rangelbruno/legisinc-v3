<?php

namespace App\Services\Template;

use Illuminate\Support\Facades\Log;

class ABNTValidationService
{
    /**
     * Configurações ABNT padrão
     */
    public const ABNT_CONFIG = [
        'fonte_principal' => ['Arial', 'Times New Roman'],
        'tamanho_corpo' => 12,
        'tamanho_titulo_1' => 16,
        'tamanho_titulo_2' => 14,
        'tamanho_citacao_longa' => 10,
        'tamanho_nota_rodape' => 10,
        'tamanho_legenda' => 10,
        'espacamento_principal' => 1.5,
        'espacamento_citacao' => 1.0,
        'espacamento_nota' => 1.0,
        'margens' => [
            'superior' => '3cm',
            'esquerda' => '3cm',
            'direita' => '2cm',
            'inferior' => '2cm'
        ]
    ];

    /**
     * Regras de validação tipográfica
     */
    protected array $regrasValidacao = [
        'fonte_legivel' => 'Verificar se a fonte é legível (Arial ou Times New Roman)',
        'corpo_12pt' => 'Texto principal deve ter 12pt',
        'espacamento_1_5' => 'Espaçamento principal deve ser 1,5',
        'reducao_elementos_secundarios' => 'Citações, notas e legendas devem ter 10pt',
        'contraste_minimo' => 'Garantir contraste mínimo WCAG 2.1',
        'margem_abnt' => 'Margens devem seguir padrão ABNT',
        'estrutura_semantica' => 'Usar estrutura semântica adequada'
    ];

    /**
     * Validar documento conforme normas ABNT
     */
    public function validarDocumento(string $conteudo): array
    {
        $validacoes = [];
        
        try {
            // Validar estrutura HTML básica
            $validacoes['estrutura_html'] = $this->validarEstruturaHTML($conteudo);
            
            // Validar configurações tipográficas
            $validacoes['tipografia'] = $this->validarTipografia($conteudo);
            
            // Validar espaçamentos
            $validacoes['espacamentos'] = $this->validarEspacamentos($conteudo);
            
            // Validar margens
            $validacoes['margens'] = $this->validarMargens($conteudo);
            
            // Validar acessibilidade básica
            $validacoes['acessibilidade'] = $this->validarAcessibilidade($conteudo);
            
            // Validar estrutura legislativa
            $validacoes['estrutura_legislativa'] = $this->validarEstruturaLegislativa($conteudo);
            
            // Calcular score geral
            $validacoes['score_geral'] = $this->calcularScoreGeral($validacoes);
            
        } catch (\Exception $e) {
            // Log::error('Erro na validação ABNT', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString()
            // ]);
            
            $validacoes['erro'] = [
                'status' => 'erro',
                'mensagem' => 'Erro interno na validação: ' . $e->getMessage()
            ];
        }
        
        return $validacoes;
    }

    /**
     * Validar estrutura HTML básica
     */
    protected function validarEstruturaHTML(string $conteudo): array
    {
        $resultado = [
            'status' => 'ok',
            'problemas' => [],
            'sugestoes' => []
        ];

        // Verificar se tem DOCTYPE
        if (!preg_match('/<!DOCTYPE\s+html>/i', $conteudo)) {
            $resultado['problemas'][] = 'DOCTYPE HTML5 não encontrado';
            $resultado['sugestoes'][] = 'Adicionar <!DOCTYPE html> no início do documento';
        }

        // Verificar charset UTF-8
        if (!preg_match('/<meta[^>]+charset=["\']?utf-8["\']?[^>]*>/i', $conteudo)) {
            $resultado['problemas'][] = 'Charset UTF-8 não especificado';
            $resultado['sugestoes'][] = 'Adicionar <meta charset="UTF-8"> no head';
        }

        // Verificar idioma
        if (!preg_match('/<html[^>]+lang=["\']?pt-BR["\']?[^>]*>/i', $conteudo)) {
            $resultado['problemas'][] = 'Idioma pt-BR não especificado';
            $resultado['sugestoes'][] = 'Adicionar lang="pt-BR" na tag html';
        }

        $resultado['status'] = empty($resultado['problemas']) ? 'ok' : 'aviso';
        return $resultado;
    }

    /**
     * Validar configurações tipográficas
     */
    protected function validarTipografia(string $conteudo): array
    {
        $resultado = [
            'status' => 'ok',
            'problemas' => [],
            'sugestoes' => []
        ];

        // Verificar fonte principal
        if (!preg_match('/font-family:\s*["\']?(Times New Roman|Arial)["\']?/i', $conteudo)) {
            $resultado['problemas'][] = 'Fonte principal não conforme (deve ser Arial ou Times New Roman)';
            $resultado['sugestoes'][] = 'Usar font-family: "Times New Roman", serif ou font-family: Arial, sans-serif';
        }

        // Verificar tamanho do corpo principal (12pt)
        if (!preg_match('/font-size:\s*12pt/i', $conteudo)) {
            $resultado['problemas'][] = 'Tamanho do corpo principal não é 12pt';
            $resultado['sugestoes'][] = 'Definir font-size: 12pt para o texto principal';
        }

        // Verificar se elementos secundários têm 10pt
        if (preg_match_all('/\.(citacao-longa|nota-rodape|legenda)[^}]*font-size:\s*(\d+)pt/i', $conteudo, $matches)) {
            foreach ($matches[2] as $size) {
                if ($size != '10') {
                    $resultado['problemas'][] = "Elementos secundários devem ter 10pt, encontrado {$size}pt";
                    $resultado['sugestoes'][] = 'Ajustar citações, notas e legendas para 10pt';
                }
            }
        }

        $resultado['status'] = empty($resultado['problemas']) ? 'ok' : 'aviso';
        return $resultado;
    }

    /**
     * Validar espaçamentos
     */
    protected function validarEspacamentos(string $conteudo): array
    {
        $resultado = [
            'status' => 'ok',
            'problemas' => [],
            'sugestoes' => []
        ];

        // Verificar espaçamento principal (1.5)
        if (!preg_match('/line-height:\s*1\.5/i', $conteudo)) {
            $resultado['problemas'][] = 'Espaçamento principal não é 1,5';
            $resultado['sugestoes'][] = 'Definir line-height: 1.5 para o texto principal';
        }

        // Verificar espaçamento de citações (1.0)
        if (preg_match('/\.citacao-longa[^}]*line-height:\s*(\d+(?:\.\d+)?)/i', $conteudo, $matches)) {
            if ($matches[1] != '1.0' && $matches[1] != '1') {
                $resultado['problemas'][] = "Espaçamento de citação deve ser 1.0, encontrado {$matches[1]}";
                $resultado['sugestoes'][] = 'Ajustar line-height das citações longas para 1.0';
            }
        }

        $resultado['status'] = empty($resultado['problemas']) ? 'ok' : 'aviso';
        return $resultado;
    }

    /**
     * Validar margens
     */
    protected function validarMargens(string $conteudo): array
    {
        $resultado = [
            'status' => 'ok',
            'problemas' => [],
            'sugestoes' => []
        ];

        // Verificar margens ABNT (3cm 2cm 2cm 3cm)
        if (preg_match('/margin:\s*([^;]+);/i', $conteudo, $matches)) {
            $margin = trim($matches[1]);
            if ($margin !== '3cm 2cm 2cm 3cm') {
                $resultado['problemas'][] = "Margens não seguem padrão ABNT: {$margin}";
                $resultado['sugestoes'][] = 'Usar margin: 3cm 2cm 2cm 3cm (superior direita inferior esquerda)';
            }
        } else {
            $resultado['problemas'][] = 'Margens não definidas';
            $resultado['sugestoes'][] = 'Definir margin: 3cm 2cm 2cm 3cm no body';
        }

        $resultado['status'] = empty($resultado['problemas']) ? 'ok' : 'aviso';
        return $resultado;
    }

    /**
     * Validar acessibilidade básica
     */
    protected function validarAcessibilidade(string $conteudo): array
    {
        $resultado = [
            'status' => 'ok',
            'problemas' => [],
            'sugestoes' => []
        ];

        // Verificar contraste (básico - cor do texto)
        if (preg_match('/color:\s*#?([a-fA-F0-9]{3,6})/i', $conteudo, $matches)) {
            $cor = $matches[1];
            if (strtolower($cor) !== '000' && strtolower($cor) !== '000000') {
                $resultado['problemas'][] = "Cor do texto pode não ter contraste suficiente: #{$cor}";
                $resultado['sugestoes'][] = 'Usar cor preta (#000) para melhor contraste em documentos impressos';
            }
        }

        // Verificar se há estrutura semântica
        $tagsSemanticas = ['h1', 'h2', 'h3', 'article', 'section', 'header', 'main'];
        $temSemantica = false;
        foreach ($tagsSemanticas as $tag) {
            if (preg_match("/<{$tag}[^>]*>/i", $conteudo)) {
                $temSemantica = true;
                break;
            }
        }
        
        if (!$temSemantica) {
            $resultado['problemas'][] = 'Falta estrutura semântica (h1, h2, section, etc.)';
            $resultado['sugestoes'][] = 'Usar tags semânticas para melhor acessibilidade';
        }

        $resultado['status'] = empty($resultado['problemas']) ? 'ok' : 'aviso';
        return $resultado;
    }

    /**
     * Validar estrutura legislativa específica
     */
    protected function validarEstruturaLegislativa(string $conteudo): array
    {
        $resultado = [
            'status' => 'ok',
            'problemas' => [],
            'sugestoes' => []
        ];

        // Verificar elementos obrigatórios
        $elementosObrigatorios = [
            'epigrafe' => 'Epígrafe (tipo + número + ano)',
            'ementa' => 'Ementa da proposição',
            'preambulo' => 'Preâmbulo legislativo',
            'assinatura' => 'Assinatura do autor'
        ];

        foreach ($elementosObrigatorios as $classe => $descricao) {
            if (!preg_match("/class=[\"']?{$classe}[\"']?/i", $conteudo)) {
                $resultado['problemas'][] = "{$descricao} não encontrada";
                $resultado['sugestoes'][] = "Adicionar elemento com class='{$classe}'";
            }
        }

        // Verificar formatação de artigos
        if (!preg_match('/class=["\']?artigo["\']?/i', $conteudo)) {
            $resultado['problemas'][] = 'Estrutura de artigos não encontrada';
            $resultado['sugestoes'][] = 'Usar class="artigo" para estruturar artigos';
        }

        $resultado['status'] = empty($resultado['problemas']) ? 'ok' : 'aviso';
        return $resultado;
    }

    /**
     * Calcular score geral de conformidade
     */
    protected function calcularScoreGeral(array $validacoes): array
    {
        $totalCategorias = 0;
        $categoriasOk = 0;

        foreach ($validacoes as $categoria => $resultado) {
            if (is_array($resultado) && isset($resultado['status'])) {
                $totalCategorias++;
                if ($resultado['status'] === 'ok') {
                    $categoriasOk++;
                }
            }
        }

        $percentual = $totalCategorias > 0 ? ($categoriasOk / $totalCategorias) * 100 : 0;
        
        $status = 'critico';
        if ($percentual >= 90) {
            $status = 'excelente';
        } elseif ($percentual >= 75) {
            $status = 'bom';
        } elseif ($percentual >= 50) {
            $status = 'regular';
        }

        return [
            'percentual' => round($percentual, 1),
            'status' => $status,
            'categorias_ok' => $categoriasOk,
            'total_categorias' => $totalCategorias,
            'mensagem' => $this->getMensagemScore($status, $percentual)
        ];
    }

    /**
     * Obter mensagem baseada no score
     */
    protected function getMensagemScore(string $status, float $percentual): string
    {
        $mensagens = [
            'excelente' => "Parabéns! Documento em excelente conformidade com as normas ABNT ({$percentual}%)",
            'bom' => "Documento em boa conformidade com as normas ABNT ({$percentual}%). Pequenos ajustes recomendados.",
            'regular' => "Documento parcialmente conforme ({$percentual}%). Revisar pontos destacados.",
            'critico' => "Documento necessita ajustes significativos ({$percentual}%). Revisar normas ABNT."
        ];

        return $mensagens[$status] ?? "Conformidade: {$percentual}%";
    }

    /**
     * Gerar relatório detalhado de validação
     */
    public function gerarRelatorio(array $validacoes): string
    {
        $relatorio = "# Relatório de Validação ABNT\n\n";
        
        // Score geral
        if (isset($validacoes['score_geral'])) {
            $score = $validacoes['score_geral'];
            $relatorio .= "## Score Geral: {$score['percentual']}% - " . ucfirst($score['status']) . "\n";
            $relatorio .= "{$score['mensagem']}\n\n";
        }

        // Detalhes por categoria
        foreach ($validacoes as $categoria => $resultado) {
            if ($categoria === 'score_geral' || $categoria === 'erro') continue;
            
            if (is_array($resultado)) {
                $relatorio .= "### " . ucfirst(str_replace('_', ' ', $categoria)) . " - " . ucfirst($resultado['status']) . "\n";
                
                if (!empty($resultado['problemas'])) {
                    $relatorio .= "**Problemas encontrados:**\n";
                    foreach ($resultado['problemas'] as $problema) {
                        $relatorio .= "- {$problema}\n";
                    }
                }
                
                if (!empty($resultado['sugestoes'])) {
                    $relatorio .= "**Sugestões:**\n";
                    foreach ($resultado['sugestoes'] as $sugestao) {
                        $relatorio .= "- {$sugestao}\n";
                    }
                }
                
                $relatorio .= "\n";
            }
        }

        return $relatorio;
    }

    /**
     * Aplicar correções automáticas simples
     */
    public function aplicarCorrecoesAutomaticas(string $conteudo): array
    {
        $conteudoCorrigido = $conteudo;
        $correcoesAplicadas = [];

        try {
            // Corrigir DOCTYPE se não existir
            if (!preg_match('/<!DOCTYPE\s+html>/i', $conteudoCorrigido)) {
                $conteudoCorrigido = "<!DOCTYPE html>\n" . $conteudoCorrigido;
                $correcoesAplicadas[] = 'Adicionado DOCTYPE HTML5';
            }

            // Corrigir charset UTF-8
            if (!preg_match('/<meta[^>]+charset=["\']?utf-8["\']?[^>]*>/i', $conteudoCorrigido)) {
                $conteudoCorrigido = preg_replace('/(<head[^>]*>)/', '$1' . "\n    <meta charset=\"UTF-8\">", $conteudoCorrigido);
                $correcoesAplicadas[] = 'Adicionado charset UTF-8';
            }

            // Corrigir idioma
            if (!preg_match('/<html[^>]+lang=["\']?pt-BR["\']?[^>]*>/i', $conteudoCorrigido)) {
                $conteudoCorrigido = preg_replace('/<html([^>]*)>/', '<html$1 lang="pt-BR">', $conteudoCorrigido);
                $correcoesAplicadas[] = 'Adicionado idioma pt-BR';
            }

        } catch (\Exception $e) {
            // Log::error('Erro ao aplicar correções automáticas', [
            //     'error' => $e->getMessage()
            // ]);
        }

        return [
            'conteudo' => $conteudoCorrigido,
            'correcoes' => $correcoesAplicadas
        ];
    }
}