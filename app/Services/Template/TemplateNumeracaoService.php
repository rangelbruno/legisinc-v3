<?php

namespace App\Services\Template;

use App\Models\Proposicao;
use App\Models\TipoProposicao;
use App\Services\Template\TemplateParametrosService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TemplateNumeracaoService
{
    protected $parametrosService;

    public function __construct(TemplateParametrosService $parametrosService)
    {
        $this->parametrosService = $parametrosService;
    }

    /**
     * Gerar próximo número para proposição conforme sistema unificado
     */
    public function obterProximoNumero(TipoProposicao $tipo, ?int $ano = null): int
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $ano = $ano ?? $this->obterAnoLegislativo();
        
        $sistema = $parametros['Numeração Unificada.numeracao_sistema'] ?? 'unificada_anual';
        $reiniciarAno = ($parametros['Numeração Unificada.numeracao_reiniciar_ano'] ?? '1') === '1';

        return match($sistema) {
            'unificada_anual' => $this->proximoNumeroUnificadoAnual($tipo, $ano, $reiniciarAno),
            'sequencial_tipo' => $this->proximoNumeroSequencial($tipo),
            'geral_anual' => $this->proximoNumeroGeralAnual($ano, $reiniciarAno),
            default => $this->proximoNumeroUnificadoAnual($tipo, $ano, $reiniciarAno)
        };
    }

    /**
     * Validar se número está disponível
     */
    public function validarNumeroDisponivel(TipoProposicao $tipo, int $numero, int $ano): bool
    {
        $existente = Proposicao::where('tipo', $tipo->codigo)
            ->where('numero', (string)$numero)
            ->where('ano', $ano)
            ->exists();

        return !$existente;
    }

    /**
     * Reservar número para proposição
     */
    public function reservarNumero(TipoProposicao $tipo, ?int $numeroEspecifico = null, ?int $ano = null): array
    {
        $ano = $ano ?? $this->obterAnoLegislativo();
        
        // Se número específico foi solicitado, validar disponibilidade
        if ($numeroEspecifico !== null) {
            if (!$this->validarNumeroDisponivel($tipo, $numeroEspecifico, $ano)) {
                throw new \InvalidArgumentException("Número {$numeroEspecifico}/{$ano} já está em uso para {$tipo->nome}");
            }
            
            return [
                'numero' => $numeroEspecifico,
                'ano' => $ano,
                'disponivel' => true,
                'tipo_numeracao' => 'especifico'
            ];
        }

        // Gerar próximo número disponível
        $proximoNumero = $this->obterProximoNumero($tipo, $ano);
        
        // Verificar se está realmente disponível (race condition)
        $tentativas = 0;
        while (!$this->validarNumeroDisponivel($tipo, $proximoNumero, $ano) && $tentativas < 10) {
            $proximoNumero++;
            $tentativas++;
        }

        if ($tentativas >= 10) {
            throw new \RuntimeException("Não foi possível obter número disponível após 10 tentativas");
        }

        return [
            'numero' => $proximoNumero,
            'ano' => $ano,
            'disponivel' => true,
            'tipo_numeracao' => 'automatico'
        ];
    }

    /**
     * Formatar número com zeros à esquerda
     */
    public function formatarNumero(int $numero): string
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $digitosMinimos = (int)($parametros['Numeração Unificada.numeracao_digitos_minimos'] ?? 3);
        
        return str_pad($numero, $digitosMinimos, '0', STR_PAD_LEFT);
    }

    /**
     * Gerar epígrafe completa formatada
     */
    public function gerarEpigrafeFormatada(TipoProposicao $tipo, int $numero, int $ano): string
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $formato = $parametros['Estrutura Legal.estrutura_formato_epigrafe'] ?? 'tipo_espaco_numero_barra_ano';
        
        $numeroFormatado = $this->formatarNumero($numero);
        $codigoTipo = strtoupper($tipo->codigo);

        return match($formato) {
            'tipo_espaco_numero_barra_ano' => "{$codigoTipo} Nº {$numeroFormatado}/{$ano}",
            'tipo_numero_barra_ano' => "{$codigoTipo} {$numeroFormatado}/{$ano}",
            'tipo_espaco_numero_ano' => "{$codigoTipo} Nº {$numeroFormatado} DE {$ano}",
            default => "{$codigoTipo} Nº {$numeroFormatado}/{$ano}"
        };
    }

    /**
     * Obter estatísticas de numeração
     */
    public function obterEstatisticasNumeracao(?int $ano = null): array
    {
        $ano = $ano ?? $this->obterAnoLegislativo();
        
        $stats = [];
        
        // Estatísticas por tipo
        $tiposComProposicoes = DB::table('proposicoes')
            ->join('tipo_proposicoes', 'proposicoes.tipo', '=', 'tipo_proposicoes.codigo')
            ->where('proposicoes.ano', $ano)
            ->select(
                'tipo_proposicoes.id',
                'tipo_proposicoes.codigo',
                'tipo_proposicoes.nome',
                DB::raw('COUNT(*) as total'),
                DB::raw('MAX(CAST(proposicoes.numero as INTEGER)) as maior_numero'),
                DB::raw('MIN(CAST(proposicoes.numero as INTEGER)) as menor_numero')
            )
            ->whereRaw('proposicoes.numero ~ \'^[0-9]+$\'') // Apenas números
            ->groupBy('tipo_proposicoes.id', 'tipo_proposicoes.codigo', 'tipo_proposicoes.nome')
            ->orderBy('total', 'desc')
            ->get();

        foreach ($tiposComProposicoes as $tipo) {
            $stats['por_tipo'][$tipo->codigo] = [
                'nome' => $tipo->nome,
                'total' => $tipo->total,
                'maior_numero' => $tipo->maior_numero,
                'menor_numero' => $tipo->menor_numero,
                'proximo_numero' => $tipo->maior_numero + 1
            ];
        }

        // Estatísticas gerais
        $totalAno = Proposicao::where('ano', $ano)->count();
        $stats['geral'] = [
            'ano' => $ano,
            'total_proposicoes' => $totalAno,
            'tipos_utilizados' => count($stats['por_tipo'] ?? []),
            'maior_numero_geral' => $tiposComProposicoes->max('maior_numero') ?? 0
        ];

        // Lacunas na numeração (números pulados)
        foreach ($stats['por_tipo'] ?? [] as $codigo => $dados) {
            $numerosUsados = Proposicao::where('tipo', $codigo)
                ->where('ano', $ano)
                ->whereRaw('numero ~ \'^[0-9]+$\'')
                ->pluck('numero')
                ->map(fn($n) => (int)$n)
                ->sort()
                ->values()
                ->toArray();

            $lacunas = [];
            if (!empty($numerosUsados)) {
                $maxNumero = max($numerosUsados);
                for ($i = 1; $i < $maxNumero; $i++) {
                    if (!in_array($i, $numerosUsados)) {
                        $lacunas[] = $i;
                    }
                }
            }
            
            $stats['por_tipo'][$codigo]['lacunas'] = $lacunas;
            $stats['por_tipo'][$codigo]['tem_lacunas'] = !empty($lacunas);
        }

        return $stats;
    }

    /**
     * Validar numeração conforme padrões
     */
    public function validarNumeracao(Proposicao $proposicao): array
    {
        $erros = [];
        $avisos = [];
        $aprovado = [];

        // Validar ano
        if (empty($proposicao->ano)) {
            $erros[] = 'Ano não especificado';
        } elseif ($proposicao->ano < 1988 || $proposicao->ano > (date('Y') + 1)) {
            $avisos[] = 'Ano fora do intervalo esperado';
        } else {
            $aprovado[] = 'Ano válido';
        }

        // Validar número
        if (empty($proposicao->numero)) {
            $erros[] = 'Número não especificado';
        } elseif (!is_numeric($proposicao->numero) || $proposicao->numero <= 0) {
            $erros[] = 'Número deve ser positivo';
        } else {
            $aprovado[] = 'Número válido';
        }

        // Verificar duplicação
        if (!empty($proposicao->numero) && !empty($proposicao->ano) && $proposicao->tipoProposicao) {
            $duplicata = Proposicao::where('tipo_proposicao_id', $proposicao->tipo_proposicao_id)
                ->where('numero', $proposicao->numero)
                ->where('ano', $proposicao->ano)
                ->where('id', '!=', $proposicao->id ?? 0)
                ->exists();

            if ($duplicata) {
                $erros[] = 'Número já utilizado para este tipo e ano';
            } else {
                $aprovado[] = 'Número único';
            }
        }

        // Verificar se segue padrão unificado (pós-2019)
        if (($proposicao->ano ?? date('Y')) >= 2019) {
            $aprovado[] = 'Segue padrão unificado pós-2019';
        }

        // Verificar sequência lógica
        if (!empty($proposicao->numero) && $proposicao->tipoProposicao) {
            $ultimoNumero = Proposicao::where('tipo_proposicao_id', $proposicao->tipo_proposicao_id)
                ->where('ano', $proposicao->ano)
                ->where('id', '!=', $proposicao->id ?? 0)
                ->max('numero') ?? 0;

            if ($proposicao->numero > ($ultimoNumero + 10)) {
                $avisos[] = 'Grande salto na numeração - verificar se está correto';
            }
        }

        return [
            'erros' => $erros,
            'avisos' => $avisos,
            'aprovado' => $aprovado,
            'valida' => empty($erros)
        ];
    }

    /**
     * Corrigir numeração de proposições
     */
    public function corrigirNumeracao(TipoProposicao $tipo, int $ano, bool $previewOnly = true): array
    {
        $proposicoes = Proposicao::where('tipo_proposicao_id', $tipo->id)
            ->where('ano', $ano)
            ->orderBy('created_at')
            ->get();

        $alteracoes = [];
        $numeroAtual = 1;

        foreach ($proposicoes as $proposicao) {
            if ($proposicao->numero != $numeroAtual) {
                $alteracoes[] = [
                    'id' => $proposicao->id,
                    'numero_atual' => $proposicao->numero,
                    'numero_novo' => $numeroAtual,
                    'ementa' => substr($proposicao->ementa ?? '', 0, 50) . '...'
                ];

                if (!$previewOnly) {
                    $proposicao->numero = $numeroAtual;
                    $proposicao->save();
                    
                    Log::info('Numeração corrigida', [
                        'proposicao_id' => $proposicao->id,
                        'numero_antigo' => $proposicao->numero,
                        'numero_novo' => $numeroAtual,
                        'tipo' => $tipo->codigo,
                        'ano' => $ano
                    ]);
                }
            }
            
            $numeroAtual++;
        }

        return [
            'total_proposicoes' => $proposicoes->count(),
            'alteracoes_necessarias' => count($alteracoes),
            'alteracoes' => $alteracoes,
            'executado' => !$previewOnly
        ];
    }

    // Métodos privados auxiliares

    private function proximoNumeroUnificadoAnual(TipoProposicao $tipo, int $ano, bool $reiniciar): int
    {
        $query = Proposicao::where('tipo', $tipo->codigo);
        
        if ($reiniciar) {
            $query->where('ano', $ano);
        }
        
        $ultimoNumero = $query->max('numero') ?? 0;
        
        // Se reinicia por ano e não há proposições neste ano, começar do 1
        if ($reiniciar && !Proposicao::where('tipo', $tipo->codigo)->where('ano', $ano)->exists()) {
            return 1;
        }
        
        return is_numeric($ultimoNumero) ? (int)$ultimoNumero + 1 : 1;
    }

    private function proximoNumeroSequencial(TipoProposicao $tipo): int
    {
        $ultimoNumero = Proposicao::where('tipo', $tipo->codigo)->max('numero') ?? 0;
        return is_numeric($ultimoNumero) ? (int)$ultimoNumero + 1 : 1;
    }

    private function proximoNumeroGeralAnual(int $ano, bool $reiniciar): int
    {
        $query = Proposicao::query();
        
        if ($reiniciar) {
            $query->where('ano', $ano);
        }
        
        $ultimoNumero = $query->max('numero') ?? 0;
        
        if ($reiniciar && !Proposicao::where('ano', $ano)->exists()) {
            return 1;
        }
        
        return is_numeric($ultimoNumero) ? (int)$ultimoNumero + 1 : 1;
    }

    private function obterAnoLegislativo(): int
    {
        $parametros = $this->parametrosService->obterParametrosTemplates();
        $inicioAno = $parametros['Numeração Unificada.numeracao_inicio_ano_fiscal'] ?? '01-01';
        
        // Se configurado para iniciar em data diferente de 1º de janeiro
        if ($inicioAno !== '01-01') {
            [$mes, $dia] = explode('-', $inicioAno);
            $inicioAnoAtual = \Carbon\Carbon::create(date('Y'), (int)$mes, (int)$dia);
            
            if (now()->isBefore($inicioAnoAtual)) {
                return date('Y') - 1; // Ainda está no ano legislativo anterior
            }
        }
        
        return (int)date('Y');
    }
}