<?php

namespace App\Services\Parametro;

use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;
use App\Models\Parametro\ParametroValor;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditoriaParametroService
{
    /**
     * Registra uma ação de auditoria
     */
    public function registrarAcao(
        string $entidade,
        int $entidadeId,
        string $acao,
        array $dadosAntigos = [],
        array $dadosNovos = [],
        int $userId = null,
        string $ip = null,
        string $userAgent = null
    ): void {
        try {
            $registro = [
                'entidade' => $entidade,
                'entidade_id' => $entidadeId,
                'acao' => $acao,
                'dados_antigos' => json_encode($dadosAntigos),
                'dados_novos' => json_encode($dadosNovos),
                'user_id' => $userId ?: auth()->id(),
                'ip_address' => $ip ?: request()->ip(),
                'user_agent' => $userAgent ?: request()->userAgent(),
                'created_at' => now(),
                'metadata' => json_encode([
                    'session_id' => session()->getId(),
                    'timestamp' => microtime(true),
                    'request_id' => request()->header('X-Request-ID', uniqid()),
                ])
            ];

            DB::table('auditoria_parametros')->insert($registro);

            // Log estruturado para monitoramento
            // // Log::info('Auditoria de parâmetro registrada', [
            //     'entidade' => $entidade,
            //     'entidade_id' => $entidadeId,
            //     'acao' => $acao,
            //     'user_id' => $registro['user_id'],
            //     'ip' => $registro['ip_address']
            // ]);

        } catch (\Exception $e) {
            // Em caso de erro na auditoria, registrar em log mas não falhar a operação principal
            // // Log::error('Erro ao registrar auditoria de parâmetro', [
            //     'error' => $e->getMessage(),
            //     'entidade' => $entidade,
            //     'entidade_id' => $entidadeId,
            //     'acao' => $acao
            // ]);
        }
    }

    /**
     * Registra criação de módulo
     */
    public function registrarCriacaoModulo(ParametroModulo $modulo): void
    {
        $this->registrarAcao(
            'modulo',
            $modulo->id,
            'created',
            [],
            $modulo->toArray()
        );
    }

    /**
     * Registra atualização de módulo
     */
    public function registrarAtualizacaoModulo(ParametroModulo $modulo, array $dadosAntigos): void
    {
        $this->registrarAcao(
            'modulo',
            $modulo->id,
            'updated',
            $dadosAntigos,
            $modulo->toArray()
        );
    }

    /**
     * Registra exclusão de módulo
     */
    public function registrarExclusaoModulo(ParametroModulo $modulo): void
    {
        $this->registrarAcao(
            'modulo',
            $modulo->id,
            'deleted',
            $modulo->toArray(),
            []
        );
    }

    /**
     * Registra criação de submódulo
     */
    public function registrarCriacaoSubmodulo(ParametroSubmodulo $submodulo): void
    {
        $this->registrarAcao(
            'submodulo',
            $submodulo->id,
            'created',
            [],
            $submodulo->toArray()
        );
    }

    /**
     * Registra atualização de submódulo
     */
    public function registrarAtualizacaoSubmodulo(ParametroSubmodulo $submodulo, array $dadosAntigos): void
    {
        $this->registrarAcao(
            'submodulo',
            $submodulo->id,
            'updated',
            $dadosAntigos,
            $submodulo->toArray()
        );
    }

    /**
     * Registra exclusão de submódulo
     */
    public function registrarExclusaoSubmodulo(ParametroSubmodulo $submodulo): void
    {
        $this->registrarAcao(
            'submodulo',
            $submodulo->id,
            'deleted',
            $submodulo->toArray(),
            []
        );
    }

    /**
     * Registra criação de campo
     */
    public function registrarCriacaoCampo(ParametroCampo $campo): void
    {
        $this->registrarAcao(
            'campo',
            $campo->id,
            'created',
            [],
            $campo->toArray()
        );
    }

    /**
     * Registra atualização de campo
     */
    public function registrarAtualizacaoCampo(ParametroCampo $campo, array $dadosAntigos): void
    {
        $this->registrarAcao(
            'campo',
            $campo->id,
            'updated',
            $dadosAntigos,
            $campo->toArray()
        );
    }

    /**
     * Registra exclusão de campo
     */
    public function registrarExclusaoCampo(ParametroCampo $campo): void
    {
        $this->registrarAcao(
            'campo',
            $campo->id,
            'deleted',
            $campo->toArray(),
            []
        );
    }

    /**
     * Registra criação de valor
     */
    public function registrarCriacaoValor(ParametroValor $valor): void
    {
        $dados = $valor->toArray();
        $dados['campo_info'] = [
            'nome' => $valor->campo->nome,
            'submodulo' => $valor->campo->submodulo->nome,
            'modulo' => $valor->campo->submodulo->modulo->nome
        ];

        $this->registrarAcao(
            'valor',
            $valor->id,
            'created',
            [],
            $dados
        );
    }

    /**
     * Registra atualização de valor
     */
    public function registrarAtualizacaoValor(ParametroValor $valor, array $dadosAntigos): void
    {
        $dados = $valor->toArray();
        $dados['campo_info'] = [
            'nome' => $valor->campo->nome,
            'submodulo' => $valor->campo->submodulo->nome,
            'modulo' => $valor->campo->submodulo->modulo->nome
        ];

        $this->registrarAcao(
            'valor',
            $valor->id,
            'updated',
            $dadosAntigos,
            $dados
        );
    }

    /**
     * Registra expiração de valor
     */
    public function registrarExpiracaoValor(ParametroValor $valor): void
    {
        $dados = $valor->toArray();
        $dados['campo_info'] = [
            'nome' => $valor->campo->nome,
            'submodulo' => $valor->campo->submodulo->nome,
            'modulo' => $valor->campo->submodulo->modulo->nome
        ];

        $this->registrarAcao(
            'valor',
            $valor->id,
            'expired',
            $dados,
            ['valido_ate' => now()]
        );
    }

    /**
     * Obtém histórico de uma entidade
     */
    public function obterHistorico(string $entidade, int $entidadeId, int $limite = 50): array
    {
        $registros = DB::table('auditoria_parametros')
            ->where('entidade', $entidade)
            ->where('entidade_id', $entidadeId)
            ->orderBy('created_at', 'desc')
            ->limit($limite)
            ->get();

        return $registros->map(function ($registro) {
            return [
                'id' => $registro->id,
                'acao' => $registro->acao,
                'dados_antigos' => json_decode($registro->dados_antigos, true),
                'dados_novos' => json_decode($registro->dados_novos, true),
                'user_id' => $registro->user_id,
                'ip_address' => $registro->ip_address,
                'user_agent' => $registro->user_agent,
                'created_at' => Carbon::parse($registro->created_at),
                'metadata' => json_decode($registro->metadata, true)
            ];
        })->toArray();
    }

    /**
     * Obtém relatório de atividades por período
     */
    public function obterRelatorioAtividades(Carbon $dataInicio, Carbon $dataFim): array
    {
        $atividades = DB::table('auditoria_parametros')
            ->select('acao', 'entidade', DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->groupBy('acao', 'entidade')
            ->orderBy('total', 'desc')
            ->get();

        $usuariosAtivos = DB::table('auditoria_parametros')
            ->select('user_id', DB::raw('count(*) as total_acoes'))
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderBy('total_acoes', 'desc')
            ->limit(10)
            ->get();

        $atividadesPorDia = DB::table('auditoria_parametros')
            ->select(DB::raw('DATE(created_at) as data'), DB::raw('count(*) as total'))
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('data')
            ->get();

        return [
            'atividades_por_tipo' => $atividades,
            'usuarios_mais_ativos' => $usuariosAtivos,
            'atividades_por_dia' => $atividadesPorDia,
            'periodo' => [
                'inicio' => $dataInicio->format('Y-m-d'),
                'fim' => $dataFim->format('Y-m-d')
            ],
            'total_registros' => $atividades->sum('total')
        ];
    }

    /**
     * Obtém estatísticas de uso
     */
    public function obterEstatisticasUso(): array
    {
        $totalRegistros = DB::table('auditoria_parametros')->count();
        
        $ultimasSemanas = DB::table('auditoria_parametros')
            ->select(DB::raw('WEEK(created_at) as semana'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subWeeks(4))
            ->groupBy(DB::raw('WEEK(created_at)'))
            ->orderBy('semana')
            ->get();

        $entidadesMaisModificadas = DB::table('auditoria_parametros')
            ->select('entidade', 'entidade_id', DB::raw('count(*) as total_modificacoes'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('entidade', 'entidade_id')
            ->orderBy('total_modificacoes', 'desc')
            ->limit(10)
            ->get();

        $acoesMaisComuns = DB::table('auditoria_parametros')
            ->select('acao', DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('acao')
            ->orderBy('total', 'desc')
            ->get();

        return [
            'total_registros' => $totalRegistros,
            'atividade_semanal' => $ultimasSemanas,
            'entidades_mais_modificadas' => $entidadesMaisModificadas,
            'acoes_mais_comuns' => $acoesMaisComuns,
            'media_diaria' => $this->calcularMediaDiaria(),
            'pico_atividade' => $this->obterPicoAtividade()
        ];
    }

    /**
     * Calcula média diária de atividades
     */
    private function calcularMediaDiaria(): float
    {
        $totalUltimos30Dias = DB::table('auditoria_parametros')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return round($totalUltimos30Dias / 30, 2);
    }

    /**
     * Obtém horário de pico de atividade
     */
    private function obterPicoAtividade(): array
    {
        $atividadePorHora = DB::table('auditoria_parametros')
            ->select(DB::raw('HOUR(created_at) as hora'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('total', 'desc')
            ->first();

        return [
            'hora' => $atividadePorHora->hora ?? 0,
            'total_atividades' => $atividadePorHora->total ?? 0
        ];
    }

    /**
     * Limpa registros antigos de auditoria
     */
    public function limparRegistrosAntigos(int $diasParaManter = 365): int
    {
        $dataLimite = now()->subDays($diasParaManter);
        
        $totalRemovidos = DB::table('auditoria_parametros')
            ->where('created_at', '<', $dataLimite)
            ->delete();

        // // Log::info('Limpeza de auditoria de parâmetros executada', [
        //     'registros_removidos' => $totalRemovidos,
        //     'data_limite' => $dataLimite->format('Y-m-d H:i:s'),
        //     'dias_mantidos' => $diasParaManter
        // ]);

        return $totalRemovidos;
    }

    /**
     * Exporta dados de auditoria para análise
     */
    public function exportarDados(Carbon $dataInicio, Carbon $dataFim, string $formato = 'json'): string
    {
        $dados = DB::table('auditoria_parametros')
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->orderBy('created_at', 'desc')
            ->get();

        switch ($formato) {
            case 'csv':
                return $this->exportarCSV($dados);
            case 'json':
            default:
                return $dados->toJson(JSON_PRETTY_PRINT);
        }
    }

    /**
     * Exporta dados em formato CSV
     */
    private function exportarCSV($dados): string
    {
        $csv = "ID,Entidade,Entidade ID,Acao,User ID,IP,Data/Hora\n";
        
        foreach ($dados as $registro) {
            $csv .= sprintf(
                "%d,%s,%d,%s,%s,%s,%s\n",
                $registro->id,
                $registro->entidade,
                $registro->entidade_id,
                $registro->acao,
                $registro->user_id ?: 'N/A',
                $registro->ip_address,
                $registro->created_at
            );
        }

        return $csv;
    }

    /**
     * Verifica integridade dos dados de auditoria
     */
    public function verificarIntegridade(): array
    {
        $problemas = [];

        // Verificar registros órfãos
        $registrosOrfaos = DB::table('auditoria_parametros as a')
            ->leftJoin('parametros_modulos as m', function($join) {
                $join->on('a.entidade_id', '=', 'm.id')
                     ->where('a.entidade', '=', 'modulo');
            })
            ->leftJoin('parametros_submodulos as s', function($join) {
                $join->on('a.entidade_id', '=', 's.id')
                     ->where('a.entidade', '=', 'submodulo');
            })
            ->leftJoin('parametros_campos as c', function($join) {
                $join->on('a.entidade_id', '=', 'c.id')
                     ->where('a.entidade', '=', 'campo');
            })
            ->leftJoin('parametros_valores as v', function($join) {
                $join->on('a.entidade_id', '=', 'v.id')
                     ->where('a.entidade', '=', 'valor');
            })
            ->whereNull('m.id')
            ->whereNull('s.id')
            ->whereNull('c.id')
            ->whereNull('v.id')
            ->where('a.acao', '!=', 'deleted')
            ->count();

        if ($registrosOrfaos > 0) {
            $problemas[] = "Encontrados {$registrosOrfaos} registros de auditoria órfãos";
        }

        // Verificar dados JSON inválidos
        $dadosInvalidos = DB::table('auditoria_parametros')
            ->whereRaw('JSON_VALID(dados_antigos) = 0 OR JSON_VALID(dados_novos) = 0')
            ->count();

        if ($dadosInvalidos > 0) {
            $problemas[] = "Encontrados {$dadosInvalidos} registros com JSON inválido";
        }

        return [
            'status' => empty($problemas) ? 'ok' : 'problemas_encontrados',
            'problemas' => $problemas,
            'verificacao_executada_em' => now(),
            'total_registros_verificados' => DB::table('auditoria_parametros')->count()
        ];
    }
}