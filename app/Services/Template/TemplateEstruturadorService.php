<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TipoProposicao;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Support\Facades\Log;

class TemplateEstruturadorService
{
    protected $parametrosService;

    public function __construct(TemplateParametrosService $parametrosService)
    {
        $this->parametrosService = $parametrosService;
    }

    /**
     * Estruturar proposição conforme LC 95/1998
     */
    public function estruturarProposicao(array $dadosProposicao, TipoProposicao $tipo): array
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        
        $estrutura = [
            'epigrafe' => $this->gerarEpigrafe($dadosProposicao, $tipo, $parametros),
            'ementa' => $this->formatarEmenta($dadosProposicao['ementa'] ?? '', $parametros),
            'preambulo' => $this->gerarPreambulo($parametros),
            'corpo_articulado' => $this->estruturarCorpoArticulado($dadosProposicao['texto'] ?? ''),
            'clausula_vigencia' => $this->gerarClausulaVigencia($parametros),
            'metadados' => $this->gerarMetadados($dadosProposicao, $tipo, $parametros),
            'validacoes' => $this->validarEstrutura($dadosProposicao)
        ];

        return $estrutura;
    }

    /**
     * Gerar epígrafe conforme padrão configurado
     */
    public function gerarEpigrafe(array $dados, TipoProposicao $tipo, array $parametros): string
    {
        $formato = $parametros['Estrutura Legal.estrutura_formato_epigrafe'] ?? 'tipo_espaco_numero_barra_ano';
        $numero = $dados['numero'] ?? '000';
        $ano = $dados['ano'] ?? date('Y');
        
        // Garantir numeração com zeros à esquerda conforme configurado
        $digitosMinimos = (int)($parametros['Numeração Unificada.numeracao_digitos_minimos'] ?? 3);
        $numeroFormatado = str_pad($numero, $digitosMinimos, '0', STR_PAD_LEFT);

        return match($formato) {
            'tipo_espaco_numero_barra_ano' => strtoupper($tipo->codigo) . " Nº {$numeroFormatado}/{$ano}",
            'tipo_numero_barra_ano' => strtoupper($tipo->codigo) . " {$numeroFormatado}/{$ano}",
            'tipo_espaco_numero_ano' => strtoupper($tipo->codigo) . " Nº {$numeroFormatado} DE {$ano}",
            default => strtoupper($tipo->codigo) . " Nº {$numeroFormatado}/{$ano}"
        };
    }

    /**
     * Formatar ementa conforme boas práticas
     */
    public function formatarEmenta(string $ementa, array $parametros): string
    {
        if (empty($ementa)) {
            return $parametros['Estrutura Legal.estrutura_padrao_ementa'] ?? 'Dispõe sobre [OBJETO] e dá outras providências.';
        }

        // Validar se ementa termina com ponto
        if (!str_ends_with(trim($ementa), '.')) {
            $ementa = trim($ementa) . '.';
        }

        // Capitalizar primeira letra
        $ementa = ucfirst($ementa);

        return $ementa;
    }

    /**
     * Gerar preâmbulo conforme configuração
     */
    public function gerarPreambulo(array $parametros): string
    {
        $tipoPreambulo = $parametros['Estrutura Legal.estrutura_preambulo'] ?? 'camara_municipal';
        $nomeCamara = $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'CÂMARA MUNICIPAL';
        
        // Extrair município do nome da câmara
        $municipio = $this->extrairMunicipio($nomeCamara);

        return match($tipoPreambulo) {
            'camara_municipal' => "A CÂMARA MUNICIPAL DE {$municipio} DECRETA:",
            'prefeito' => "O PREFEITO MUNICIPAL DE {$municipio} DECRETA:",
            'congresso' => 'O CONGRESSO NACIONAL DECRETA:',
            default => "A CÂMARA MUNICIPAL DE {$municipio} DECRETA:"
        };
    }

    /**
     * Estruturar corpo articulado conforme LC 95/1998
     */
    public function estruturarCorpoArticulado(string $texto): array
    {
        if (empty($texto)) {
            return [
                'artigos' => [
                    [
                        'numero' => '1º',
                        'texto' => '[Conteúdo do artigo]',
                        'paragrafos' => [],
                        'incisos' => [],
                        'alineas' => []
                    ]
                ],
                'alertas' => ['Texto vazio - estrutura básica criada']
            ];
        }

        // Analisar texto existente para identificar artigos
        $artigos = $this->extrairArtigos($texto);
        $alertas = $this->validarNumeracaoArtigos($artigos);

        return [
            'artigos' => $artigos,
            'alertas' => $alertas
        ];
    }

    /**
     * Gerar cláusula de vigência
     */
    public function gerarClausulaVigencia(array $parametros): string
    {
        $tipoVigencia = $parametros['Estrutura Legal.estrutura_clausula_vigencia'] ?? 'imediata';

        $clausulas = [
            'imediata' => 'Esta lei entra em vigor na data de sua publicação.',
            'vacatio_30' => 'Esta lei entra em vigor após 30 (trinta) dias de sua publicação.',
            'vacatio_60' => 'Esta lei entra em vigor após 60 (sessenta) dias de sua publicação.',
            'vacatio_90' => 'Esta lei entra em vigor após 90 (noventa) dias de sua publicação.',
            'data_especifica' => 'Esta lei entra em vigor em [DATA].',
        ];

        return $clausulas[$tipoVigencia] ?? $clausulas['imediata'];
    }

    /**
     * Gerar metadados Dublin Core e LexML
     */
    public function gerarMetadados(array $dados, TipoProposicao $tipo, array $parametros): array
    {
        $metadados = [];

        // Dublin Core
        if (($parametros['Metadados.metadados_dublin_core'] ?? '1') === '1') {
            $metadados['dublin_core'] = [
                'dc:title' => $dados['ementa'] ?? 'Proposição legislativa',
                'dc:creator' => $dados['autor_nome'] ?? 'Vereador',
                'dc:subject' => $tipo->nome,
                'dc:description' => $dados['ementa'] ?? '',
                'dc:date' => date('Y-m-d'),
                'dc:type' => 'Texto Legal',
                'dc:format' => 'text/html',
                'dc:language' => 'pt-BR',
                'dc:rights' => 'Domínio Público'
            ];
        }

        // LexML
        if (($parametros['Metadados.metadados_lexml'] ?? '1') === '1') {
            $autoridade = $parametros['Metadados.metadados_autoridade_lexml'] ?? 'br.municipio.camara';
            $numero = str_pad($dados['numero'] ?? 1, 3, '0', STR_PAD_LEFT);
            $ano = $dados['ano'] ?? date('Y');
            
            $metadados['lexml'] = [
                'urn' => "urn:lex:{$autoridade}:" . strtolower($tipo->codigo) . ":{$numero};{$ano}",
                'vocabulary' => 'LexML',
                'authority' => $autoridade,
                'type' => strtolower($tipo->codigo),
                'number' => $numero,
                'year' => $ano
            ];
        }

        return $metadados;
    }

    /**
     * Validar estrutura da proposição
     */
    public function validarEstrutura(array $dados): array
    {
        $erros = [];
        $avisos = [];

        // Validações obrigatórias LC 95/1998
        if (empty($dados['ementa'])) {
            $erros[] = 'Ementa é obrigatória conforme LC 95/1998';
        }

        if (!empty($dados['ementa'])) {
            // Validar ementa - deve ser frase única
            if (substr_count($dados['ementa'], '.') > 1) {
                $avisos[] = 'Ementa deve ser uma frase única (LC 95/1998, Art. 7º)';
            }

            // Verificar se começa com verbo no indicativo
            if (!$this->verificarVerboIndicativo($dados['ementa'])) {
                $avisos[] = 'Ementa deve começar com verbo no indicativo (dispõe, autoriza, institui, etc.)';
            }
        }

        if (empty($dados['texto'])) {
            $erros[] = 'Texto/corpo da proposição é obrigatório';
        }

        // Validar numeração
        if (!empty($dados['numero'])) {
            if (!is_numeric($dados['numero']) || $dados['numero'] <= 0) {
                $erros[] = 'Número da proposição deve ser um valor positivo';
            }
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'valida' => empty($erros)
        ];
    }

    /**
     * Extrair artigos do texto
     */
    private function extrairArtigos(string $texto): array
    {
        $artigos = [];
        
        // Regex para encontrar artigos
        $padraoArtigo = '/(?:Art\.?\s*|Artigo\s+)(\d+º?|[IVXLCDM]+)\s*[.-]\s*([^(?:Art\.?)]*?)(?=(?:Art\.?\s*|Artigo\s+)\d+|$)/is';
        
        preg_match_all($padraoArtigo, $texto, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            // Se não encontrou artigos estruturados, criar um artigo único
            return [
                [
                    'numero' => '1º',
                    'texto' => trim($texto),
                    'paragrafos' => [],
                    'incisos' => [],
                    'alineas' => []
                ]
            ];
        }

        foreach ($matches as $match) {
            $numero = $match[1];
            $conteudo = trim($match[2]);

            $artigos[] = [
                'numero' => $numero,
                'texto' => $conteudo,
                'paragrafos' => $this->extrairParagrafos($conteudo),
                'incisos' => $this->extrairIncisos($conteudo),
                'alineas' => $this->extrairAlineas($conteudo)
            ];
        }

        return $artigos;
    }

    /**
     * Extrair parágrafos do artigo
     */
    private function extrairParagrafos(string $conteudo): array
    {
        $paragrafos = [];
        $padrao = '/§\s*(\d+º?)\s*[.-]\s*([^§]*?)(?=§\s*\d+|$)/is';
        
        preg_match_all($padrao, $conteudo, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $paragrafos[] = [
                'numero' => $match[1],
                'texto' => trim($match[2])
            ];
        }

        return $paragrafos;
    }

    /**
     * Extrair incisos do conteúdo
     */
    private function extrairIncisos(string $conteudo): array
    {
        $incisos = [];
        $padrao = '/([IVXLCDM]+)\s*[.-]\s*([^IVXLCDM]*?)(?=[IVXLCDM]+\s*[.-]|$)/is';
        
        preg_match_all($padrao, $conteudo, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            if (strlen($match[1]) <= 4) { // Evitar matches falsos
                $incisos[] = [
                    'numero' => $match[1],
                    'texto' => trim($match[2])
                ];
            }
        }

        return $incisos;
    }

    /**
     * Extrair alíneas do conteúdo
     */
    private function extrairAlineas(string $conteudo): array
    {
        $alineas = [];
        $padrao = '/([a-z])\)\s*([^a-z\)]*?)(?=[a-z]\)|$)/is';
        
        preg_match_all($padrao, $conteudo, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            if (strlen($match[1]) === 1) { // Apenas uma letra
                $alineas[] = [
                    'letra' => $match[1],
                    'texto' => trim($match[2])
                ];
            }
        }

        return $alineas;
    }

    /**
     * Validar numeração sequencial de artigos
     */
    private function validarNumeracaoArtigos(array $artigos): array
    {
        $alertas = [];
        
        for ($i = 0; $i < count($artigos); $i++) {
            $numeroEsperado = $i + 1;
            $numeroAtual = $this->extrairNumeroArtigo($artigos[$i]['numero']);
            
            if ($numeroAtual !== $numeroEsperado) {
                $alertas[] = "Numeração incorreta no artigo {$artigos[$i]['numero']}. Esperado: {$numeroEsperado}";
            }
        }

        return $alertas;
    }

    /**
     * Extrair número do artigo (remover ordinais, romanos, etc.)
     */
    private function extrairNumeroArtigo(string $numero): int
    {
        // Remover ordinais (º)
        $numero = str_replace('º', '', $numero);
        
        // Se for romano, converter
        if (preg_match('/^[IVXLCDM]+$/i', $numero)) {
            return $this->romanoParaArabico($numero);
        }
        
        // Extrair apenas números
        preg_match('/\d+/', $numero, $matches);
        return (int)($matches[0] ?? 0);
    }

    /**
     * Converter número romano para arábico
     */
    private function romanoParaArabico(string $romano): int
    {
        $valores = [
            'I' => 1, 'V' => 5, 'X' => 10, 'L' => 50,
            'C' => 100, 'D' => 500, 'M' => 1000
        ];
        
        $romano = strtoupper($romano);
        $resultado = 0;
        $anterior = 0;
        
        for ($i = strlen($romano) - 1; $i >= 0; $i--) {
            $atual = $valores[$romano[$i]] ?? 0;
            
            if ($atual < $anterior) {
                $resultado -= $atual;
            } else {
                $resultado += $atual;
            }
            
            $anterior = $atual;
        }
        
        return $resultado;
    }

    /**
     * Verificar se ementa começa com verbo no indicativo
     */
    private function verificarVerboIndicativo(string $ementa): bool
    {
        $verbos = [
            'dispõe', 'autoriza', 'institui', 'cria', 'estabelece',
            'altera', 'revoga', 'acrescenta', 'modifica', 'fixa',
            'determina', 'concede', 'permite', 'define', 'regula',
            'disciplina', 'denomina', 'designa', 'aprova'
        ];

        $palavraInicial = strtolower(explode(' ', trim($ementa))[0] ?? '');
        
        return in_array($palavraInicial, $verbos);
    }

    /**
     * Extrair município do nome da câmara
     */
    private function extrairMunicipio(string $nomeCamara): string
    {
        $patterns = [
            '/CÂMARA MUNICIPAL DE (.+)/i',
            '/CÂMARA DE (.+)/i',
            '/CÂMARA (.+)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $nomeCamara, $matches)) {
                return strtoupper(trim($matches[1]));
            }
        }
        
        return 'MUNICÍPIO';
    }

    /**
     * Gerar template estruturado completo
     */
    public function gerarTemplateEstruturado(array $dadosProposicao, TipoProposicao $tipo): string
    {
        $estrutura = $this->estruturarProposicao($dadosProposicao, $tipo);
        
        $template = "{$estrutura['epigrafe']}\n\n";
        $template .= "{$estrutura['ementa']}\n\n";
        $template .= "{$estrutura['preambulo']}\n\n";
        
        // Corpo articulado
        foreach ($estrutura['corpo_articulado']['artigos'] as $artigo) {
            $template .= "Art. {$artigo['numero']} {$artigo['texto']}\n";
            
            foreach ($artigo['paragrafos'] as $paragrafo) {
                $template .= "§ {$paragrafo['numero']} {$paragrafo['texto']}\n";
            }
            
            foreach ($artigo['incisos'] as $inciso) {
                $template .= "{$inciso['numero']} - {$inciso['texto']}\n";
            }
            
            $template .= "\n";
        }
        
        // Cláusula de vigência como último artigo
        $ultimoArtigo = count($estrutura['corpo_articulado']['artigos']) + 1;
        $numeroUltimo = $ultimoArtigo === 2 ? '2º' : (string)$ultimoArtigo;
        
        $template .= "Art. {$numeroUltimo} {$estrutura['clausula_vigencia']}\n\n";
        
        return $template;
    }
}