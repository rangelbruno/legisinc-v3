<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TipoProposicao;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Support\Facades\Log;

class TemplateMetadadosService
{
    protected $parametrosService;

    public function __construct(TemplateParametrosService $parametrosService)
    {
        $this->parametrosService = $parametrosService;
    }

    /**
     * Gerar metadados Dublin Core completos
     */
    public function gerarDublinCore(Proposicao $proposicao): array
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        
        if (($parametros['Metadados.metadados_dublin_core'] ?? '1') !== '1') {
            return [];
        }

        $metadata = [
            // Elementos obrigatórios Dublin Core
            'dc:title' => $this->gerarTitulo($proposicao),
            'dc:creator' => $this->gerarCriador($proposicao),
            'dc:subject' => $this->gerarAssunto($proposicao),
            'dc:description' => $proposicao->ementa,
            'dc:publisher' => $this->gerarPublicador($parametros),
            'dc:contributor' => $this->gerarContribuidor($proposicao),
            'dc:date' => $this->gerarData($proposicao),
            'dc:type' => 'Texto Legislativo',
            'dc:format' => 'application/pdf',
            'dc:identifier' => $this->gerarIdentificador($proposicao, $parametros),
            'dc:source' => $this->gerarFonte($parametros),
            'dc:language' => 'pt-BR',
            'dc:relation' => $this->gerarRelacao($proposicao),
            'dc:coverage' => $this->gerarCobertura($parametros),
            'dc:rights' => 'Domínio Público - Documento Oficial'
        ];

        // Metadados estendidos específicos para legislação
        $metadata = array_merge($metadata, [
            'dcterms:created' => $proposicao->created_at?->toISOString(),
            'dcterms:modified' => $proposicao->updated_at?->toISOString(),
            'dcterms:issued' => $proposicao->data_protocolo?->toISOString(),
            'dcterms:valid' => $this->gerarValidez($proposicao),
            'dcterms:audience' => 'Público Geral',
            'dcterms:extent' => $this->calcularExtensao($proposicao),
            'dcterms:medium' => 'Documento Digital',
            'dcterms:provenance' => $this->gerarProveniencia($parametros)
        ]);

        return array_filter($metadata); // Remove valores vazios
    }

    /**
     * Gerar identificador LexML URN
     */
    public function gerarLexMLURN(Proposicao $proposicao, array $parametros = null): string
    {
        if (!$parametros) {
            $parametros = $this->parametrosService->obterParametrosTemplates();
        }

        if (($parametros['Metadados.metadados_lexml'] ?? '1') !== '1') {
            return '';
        }

        $autoridade = $parametros['Metadados.metadados_autoridade_lexml'] ?? 'br.municipio.camara';
        $tipo = strtolower($proposicao->tipoProposicao->codigo ?? 'prop');
        
        // Formatar número com zeros à esquerda
        $digitosMinimos = (int)($parametros['Numeração Unificada.numeracao_digitos_minimos'] ?? 3);
        $numero = str_pad($proposicao->numero ?? 1, $digitosMinimos, '0', STR_PAD_LEFT);
        
        $ano = $proposicao->ano ?? date('Y');
        $data = $proposicao->data_protocolo ? $proposicao->data_protocolo->format('m-d') : '';

        // URN LexML: urn:lex:autoridade:tipo:numero;ano-mes-dia
        $urn = "urn:lex:{$autoridade}:{$tipo}:{$numero};{$ano}";
        
        if (!empty($data)) {
            $urn .= "-{$data}";
        }

        return $urn;
    }

    /**
     * Gerar metadados LexML completos
     */
    public function gerarLexML(Proposicao $proposicao): array
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        
        if (($parametros['Metadados.metadados_lexml'] ?? '1') !== '1') {
            return [];
        }

        $urn = $this->gerarLexMLURN($proposicao, $parametros);
        
        return [
            'urn' => $urn,
            'vocabulary' => 'LexML BR',
            'version' => '1.0',
            'authority' => $parametros['Metadados.metadados_autoridade_lexml'] ?? 'br.municipio.camara',
            'type' => strtolower($proposicao->tipoProposicao->codigo ?? 'prop'),
            'subtype' => $this->determinarSubtipo($proposicao),
            'number' => str_pad($proposicao->numero ?? 1, 3, '0', STR_PAD_LEFT),
            'year' => $proposicao->ano ?? date('Y'),
            'date' => $proposicao->data_protocolo?->format('Y-m-d'),
            'status' => $this->mapearStatusLexML($proposicao->status ?? 'rascunho'),
            'format' => 'texto',
            'language' => 'pt-BR'
        ];
    }

    /**
     * Gerar metadados para OAI-PMH
     */
    public function gerarOAIPMH(Proposicao $proposicao): array
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $dublinCore = $this->gerarDublinCore($proposicao);
        $lexML = $this->gerarLexML($proposicao);

        $autoridade = $parametros['Metadados.metadados_autoridade_lexml'] ?? 'camara.municipio.gov.br';
        $codigoTipo = $proposicao->tipoProposicao->codigo ?? 'proposicoes';
        
        return [
            'oai' => [
                'identifier' => "oai:{$autoridade}:{$proposicao->id}",
                'datestamp' => $proposicao->updated_at?->toISOString(),
                'set' => strtolower($codigoTipo),
            ],
            'dublin_core' => $dublinCore,
            'lexml' => $lexML
        ];
    }

    /**
     * Gerar XML Akoma Ntoso básico
     */
    public function gerarAkomaNtosoXML(Proposicao $proposicao): string
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $urn = $this->gerarLexMLURN($proposicao, $parametros);
        $dublinCore = $this->gerarDublinCore($proposicao);

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<akomaNtoso xmlns="http://docs.oasis-open.org/legaldocml/ns/akn/3.0">' . "\n";
        $xml .= '  <bill contains="originalVersion">' . "\n";
        
        // Meta section
        $xml .= '    <meta>' . "\n";
        $xml .= '      <identification source="#editor">' . "\n";
        $xml .= '        <FRBRWork>' . "\n";
        $xml .= "          <FRBRthis value=\"{$urn}\"/>" . "\n";
        $xml .= "          <FRBRuri value=\"{$urn}\"/>" . "\n";
        $xml .= "          <FRBRdate date=\"" . ($proposicao->data_protocolo?->format('Y-m-d') ?? date('Y-m-d')) . "\"/>" . "\n";
        $xml .= "          <FRBRauthor href=\"#{$this->gerarAutorId($proposicao)}\"/>" . "\n";
        $xml .= '          <FRBRcountry value="br"/>' . "\n";
        $xml .= '        </FRBRWork>' . "\n";
        $xml .= '        <FRBRExpression>' . "\n";
        $xml .= "          <FRBRthis value=\"{$urn}/por@\"/>" . "\n";
        $xml .= "          <FRBRuri value=\"{$urn}/por@\"/>" . "\n";
        $xml .= "          <FRBRdate date=\"" . ($proposicao->created_at?->format('Y-m-d') ?? date('Y-m-d')) . "\"/>" . "\n";
        $xml .= "          <FRBRauthor href=\"#{$this->gerarAutorId($proposicao)}\"/>" . "\n";
        $xml .= '          <FRBRlanguage language="por"/>' . "\n";
        $xml .= '        </FRBRExpression>' . "\n";
        $xml .= '        <FRBRManifestation>' . "\n";
        $xml .= "          <FRBRthis value=\"{$urn}/por@.xml\"/>" . "\n";
        $xml .= "          <FRBRuri value=\"{$urn}/por@.xml\"/>" . "\n";
        $xml .= "          <FRBRdate date=\"" . date('Y-m-d') . "\"/>" . "\n";
        $xml .= "          <FRBRauthor href=\"#sistema\"/>" . "\n";
        $xml .= '        </FRBRManifestation>' . "\n";
        $xml .= '      </identification>' . "\n";
        
        // Dublin Core
        $xml .= '      <publication>' . "\n";
        foreach ($dublinCore as $key => $value) {
            if (!empty($value)) {
                $xml .= "        <{$key}>" . htmlspecialchars($value) . "</{$key}>" . "\n";
            }
        }
        $xml .= '      </publication>' . "\n";
        
        $xml .= '    </meta>' . "\n";
        
        // Preface (Epígrafe e Ementa)
        $xml .= '    <preface>' . "\n";
        $xml .= '      <longTitle>' . "\n";
        $xml .= '        <p>' . htmlspecialchars($this->gerarEpigrafe($proposicao, $parametros)) . '</p>' . "\n";
        $xml .= '      </longTitle>' . "\n";
        $xml .= '      <formula name="Ementa">' . "\n";
        $xml .= '        <p>' . htmlspecialchars($proposicao->ementa ?? '') . '</p>' . "\n";
        $xml .= '      </formula>' . "\n";
        $xml .= '    </preface>' . "\n";
        
        // Body (Corpo da proposição)
        $xml .= '    <body>' . "\n";
        $xml .= '      <section>' . "\n";
        if (!empty($proposicao->conteudo)) {
            $artigos = $this->extrairArtigosParaXML($proposicao->conteudo);
            foreach ($artigos as $artigo) {
                $xml .= "        <article eId=\"art_{$artigo['numero']}\">" . "\n";
                $xml .= "          <num>{$artigo['numero']}</num>" . "\n";
                $xml .= "          <content><p>" . htmlspecialchars($artigo['texto']) . "</p></content>" . "\n";
                $xml .= "        </article>" . "\n";
            }
        } else {
            $xml .= '        <article eId="art_1">' . "\n";
            $xml .= '          <num>1º</num>' . "\n";
            $xml .= '          <content><p>[Conteúdo do artigo]</p></content>' . "\n";
            $xml .= '        </article>' . "\n";
        }
        $xml .= '      </section>' . "\n";
        $xml .= '    </body>' . "\n";
        
        $xml .= '  </bill>' . "\n";
        $xml .= '</akomaNtoso>' . "\n";
        
        return $xml;
    }

    // Métodos auxiliares privados

    private function gerarTitulo(Proposicao $proposicao): string
    {
        $tipo = $proposicao->tipoProposicao->nome ?? 'Proposição';
        $numero = str_pad($proposicao->numero ?? 1, 3, '0', STR_PAD_LEFT);
        $ano = $proposicao->ano ?? date('Y');
        
        return "{$tipo} nº {$numero}/{$ano}";
    }

    private function gerarCriador(Proposicao $proposicao): string
    {
        return $proposicao->user->name ?? 'Vereador';
    }

    private function gerarAssunto(Proposicao $proposicao): string
    {
        $assuntos = [];
        
        if ($proposicao->tipoProposicao) {
            $assuntos[] = $proposicao->tipoProposicao->nome;
        }
        
        // Extrair palavras-chave da ementa
        if (!empty($proposicao->ementa)) {
            $palavrasChave = $this->extrairPalavrasChave($proposicao->ementa);
            $assuntos = array_merge($assuntos, $palavrasChave);
        }
        
        return implode('; ', array_unique($assuntos));
    }

    private function gerarPublicador(array $parametros): string
    {
        return $parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'Câmara Municipal';
    }

    private function gerarContribuidor(Proposicao $proposicao): string
    {
        $contribuidores = [];
        
        if ($proposicao->user) {
            $contribuidores[] = $proposicao->user->name;
        }
        
        // Adicionar outros contribuidores se houver
        // (coautores, relatores, etc.)
        
        return implode('; ', $contribuidores);
    }

    private function gerarData(Proposicao $proposicao): string
    {
        return $proposicao->data_protocolo?->format('Y-m-d') ?? $proposicao->created_at?->format('Y-m-d') ?? date('Y-m-d');
    }

    private function gerarIdentificador(Proposicao $proposicao, array $parametros): string
    {
        $identificadores = [];
        
        // URN LexML
        $urn = $this->gerarLexMLURN($proposicao, $parametros);
        if (!empty($urn)) {
            $identificadores[] = $urn;
        }
        
        // ID interno
        $identificadores[] = "id:{$proposicao->id}";
        
        // Número/Ano
        $numero = str_pad($proposicao->numero ?? 1, 3, '0', STR_PAD_LEFT);
        $ano = $proposicao->ano ?? date('Y');
        $tipo = strtolower($proposicao->tipoProposicao->codigo ?? 'prop');
        $identificadores[] = "{$tipo}-{$numero}-{$ano}";
        
        return implode('; ', $identificadores);
    }

    private function gerarFonte(array $parametros): string
    {
        $baseURL = $parametros['Cabeçalho.cabecalho_website'] ?? '';
        if (empty($baseURL)) {
            $baseURL = config('app.url', 'http://localhost');
        }
        
        return $baseURL;
    }

    private function gerarRelacao(Proposicao $proposicao): string
    {
        $relacoes = [];
        
        // Relações com outras proposições (se implementado)
        // $relacoes[] = "relaciona-se-com: PL 123/2024";
        
        return implode('; ', $relacoes);
    }

    private function gerarCobertura(array $parametros): string
    {
        $municipio = $this->extrairMunicipio($parametros['Cabeçalho.cabecalho_nome_camara'] ?? '');
        return !empty($municipio) ? "Município de {$municipio}" : 'Âmbito Municipal';
    }

    private function gerarValidez(Proposicao $proposicao): string
    {
        // Data de início de validade (quando aprovada)
        if ($proposicao->status === 'aprovada' && $proposicao->data_publicacao) {
            return $proposicao->data_publicacao->format('Y-m-d');
        }
        
        return '';
    }

    private function calcularExtensao(Proposicao $proposicao): string
    {
        $caracteres = strlen($proposicao->conteudo ?? '');
        $palavras = str_word_count($proposicao->conteudo ?? '');
        
        return "{$palavras} palavras, {$caracteres} caracteres";
    }

    private function gerarProveniencia(array $parametros): string
    {
        return "Sistema Legislativo - " . ($parametros['Cabeçalho.cabecalho_nome_camara'] ?? 'Câmara Municipal');
    }

    private function determinarSubtipo(Proposicao $proposicao): string
    {
        // Determinar subtipo baseado no conteúdo ou configurações
        $tipo = strtolower($proposicao->tipoProposicao->codigo ?? '');
        
        return match($tipo) {
            'pl' => 'lei.ordinaria',
            'plc' => 'lei.complementar', 
            'pec' => 'emenda.constitucional',
            'ind' => 'indicacao',
            'req' => 'requerimento',
            'moc' => 'mocao',
            default => 'proposicao.geral'
        };
    }

    private function mapearStatusLexML(string $status): string
    {
        return match($status) {
            'rascunho' => 'draft',
            'protocolado' => 'submitted',
            'em_tramitacao' => 'under_consideration',
            'aprovado' => 'approved',
            'rejeitado' => 'rejected',
            'retirado' => 'withdrawn',
            'publicado' => 'published',
            default => 'draft'
        };
    }

    private function extrairPalavrasChave(string $texto): array
    {
        // Lista de palavras-chave comuns em legislação
        $palavrasRelevantes = [
            'saúde', 'educação', 'transporte', 'meio ambiente', 'segurança',
            'cultura', 'esporte', 'desenvolvimento', 'urbano', 'social',
            'tributário', 'fiscal', 'orçamento', 'planejamento', 'habitação'
        ];
        
        $palavrasEncontradas = [];
        $textoLower = strtolower($texto);
        
        foreach ($palavrasRelevantes as $palavra) {
            if (strpos($textoLower, $palavra) !== false) {
                $palavrasEncontradas[] = ucfirst($palavra);
            }
        }
        
        return array_slice($palavrasEncontradas, 0, 5); // Máximo 5 palavras-chave
    }

    private function gerarEpigrafe(Proposicao $proposicao, array $parametros): string
    {
        $formato = $parametros['Estrutura Legal.estrutura_formato_epigrafe'] ?? 'tipo_espaco_numero_barra_ano';
        $tipo = $proposicao->tipoProposicao->codigo ?? 'PROP';
        $numero = str_pad($proposicao->numero ?? 1, 3, '0', STR_PAD_LEFT);
        $ano = $proposicao->ano ?? date('Y');

        return match($formato) {
            'tipo_espaco_numero_barra_ano' => "{$tipo} Nº {$numero}/{$ano}",
            'tipo_numero_barra_ano' => "{$tipo} {$numero}/{$ano}",
            'tipo_espaco_numero_ano' => "{$tipo} Nº {$numero} DE {$ano}",
            default => "{$tipo} Nº {$numero}/{$ano}"
        };
    }

    private function gerarAutorId(Proposicao $proposicao): string
    {
        return 'autor_' . ($proposicao->user_id ?? '1');
    }

    private function extrairMunicipio(string $nomeCamara): string
    {
        $patterns = [
            '/CÂMARA MUNICIPAL DE (.+)/i',
            '/CÂMARA DE (.+)/i',
            '/CÂMARA (.+)/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $nomeCamara, $matches)) {
                return trim($matches[1]);
            }
        }
        
        return '';
    }

    private function extrairArtigosParaXML(string $conteudo): array
    {
        // Simplificado - extrair artigos básicos
        $artigos = [];
        $linhas = explode("\n", $conteudo);
        
        foreach ($linhas as $linha) {
            if (preg_match('/^Art\.?\s*(\d+º?)\s*[-.]?\s*(.+)$/i', trim($linha), $matches)) {
                $artigos[] = [
                    'numero' => $matches[1],
                    'texto' => trim($matches[2])
                ];
            }
        }
        
        if (empty($artigos)) {
            $artigos[] = [
                'numero' => '1º',
                'texto' => trim($conteudo)
            ];
        }
        
        return $artigos;
    }
}