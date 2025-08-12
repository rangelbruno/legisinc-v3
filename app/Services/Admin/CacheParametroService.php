<?php

namespace App\Services\Admin;

use App\Models\Parametro;
use App\Models\GrupoParametro;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheParametroService
{
    private const CACHE_TTL = 3600; // 1 hora
    private const CACHE_PREFIX = 'parametro:';
    private const CACHE_GRUPO_PREFIX = 'grupo_parametros:';
    private const CACHE_KEYS_LIST = 'cache_keys_parametros';
    private const CACHE_KEYS_GRUPOS = 'cache_keys_grupos';

    /**
     * Obter parâmetro do cache ou executar callback
     */
    public function obterParametroCache(string $codigo, callable $callback): mixed
    {
        $cacheKey = self::CACHE_PREFIX . $codigo;

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($callback, $cacheKey) {
                $resultado = $callback();
                $this->adicionarChaveAoRegistro($cacheKey, self::CACHE_KEYS_LIST);
                return $resultado;
            });
        } catch (\Exception $e) {
            // Log::error('Erro ao obter parâmetro do cache', [
                //     'codigo' => $codigo,
                //     'cache_key' => $cacheKey,
                //     'error' => $e->getMessage()
            // ]);

            // Fallback para execução direta
            return $callback();
        }
    }

    /**
     * Obter parâmetros de um grupo do cache
     */
    public function obterGrupoCache(string $codigoGrupo, callable $callback): mixed
    {
        $cacheKey = self::CACHE_GRUPO_PREFIX . $codigoGrupo;

        try {
            return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($callback, $cacheKey) {
                $resultado = $callback();
                $this->adicionarChaveAoRegistro($cacheKey, self::CACHE_KEYS_GRUPOS);
                return $resultado;
            });
        } catch (\Exception $e) {
            // Log::error('Erro ao obter grupo do cache', [
                //     'codigo_grupo' => $codigoGrupo,
                //     'cache_key' => $cacheKey,
                //     'error' => $e->getMessage()
            // ]);

            // Fallback para execução direta
            return $callback();
        }
    }

    /**
     * Limpar cache de um parâmetro específico
     */
    public function limparCacheParametro(string $codigo): void
    {
        $cacheKey = self::CACHE_PREFIX . $codigo;

        try {
            Cache::forget($cacheKey);
            $this->removerChaveDoRegistro($cacheKey, self::CACHE_KEYS_LIST);

            // Log::info('Cache do parâmetro limpo', [
                //     'codigo' => $codigo,
                //     'cache_key' => $cacheKey
            // ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao limpar cache do parâmetro', [
                //     'codigo' => $codigo,
                //     'cache_key' => $cacheKey,
                //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Limpar cache de um grupo específico
     */
    public function limparCacheGrupo(string $codigoGrupo): void
    {
        $cacheKey = self::CACHE_GRUPO_PREFIX . $codigoGrupo;

        try {
            Cache::forget($cacheKey);
            $this->removerChaveDoRegistro($cacheKey, self::CACHE_KEYS_GRUPOS);

            // Limpar também cache dos parâmetros deste grupo
            $this->limparCacheParametrosDoGrupo($codigoGrupo);

            // Log::info('Cache do grupo limpo', [
                //     'codigo_grupo' => $codigoGrupo,
                //     'cache_key' => $cacheKey
            // ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao limpar cache do grupo', [
                //     'codigo_grupo' => $codigoGrupo,
                //     'cache_key' => $cacheKey,
                //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Limpar cache de todos os parâmetros de um grupo
     */
    public function limparCacheParametrosDoGrupo(string $codigoGrupo): void
    {
        try {
            $grupo = GrupoParametro::where('codigo', $codigoGrupo)->first();

            if (!$grupo) {
                return;
            }

            $parametros = Parametro::where('grupo_parametro_id', $grupo->id)
                ->pluck('codigo');

            foreach ($parametros as $codigoParametro) {
                $this->limparCacheParametro($codigoParametro);
            }

            // Log::info('Cache dos parâmetros do grupo limpo', [
                //     'codigo_grupo' => $codigoGrupo,
                //     'total_parametros' => $parametros->count()
            // ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao limpar cache dos parâmetros do grupo', [
                //     'codigo_grupo' => $codigoGrupo,
                //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Limpar todo o cache de parâmetros
     */
    public function limparTodoCache(): void
    {
        try {
            // Limpar cache de parâmetros individuais
            $chavesParametros = Cache::get(self::CACHE_KEYS_LIST, []);
            foreach ($chavesParametros as $chave) {
                Cache::forget($chave);
            }

            // Limpar cache de grupos
            $chavesGrupos = Cache::get(self::CACHE_KEYS_GRUPOS, []);
            foreach ($chavesGrupos as $chave) {
                Cache::forget($chave);
            }

            // Limpar registros de chaves
            Cache::forget(self::CACHE_KEYS_LIST);
            Cache::forget(self::CACHE_KEYS_GRUPOS);

            // Limpar cache de estatísticas
            Cache::forget('parametros_estatisticas');

            // Log::info('Todo cache de parâmetros limpo', [
                //     'parametros_limpos' => count($chavesParametros),
                //     'grupos_limpos' => count($chavesGrupos)
            // ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao limpar todo cache', [
                //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Pré-aquecer cache com parâmetros mais utilizados
     */
    public function preaquecerCache(): void
    {
        try {
            // Obter parâmetros ativos mais comuns
            $parametrosAtivos = Parametro::where('ativo', true)
                ->where('visivel', true)
                ->with(['grupoParametro', 'tipoParametro'])
                ->get();

            $aquecidos = 0;

            foreach ($parametrosAtivos as $parametro) {
                $cacheKey = self::CACHE_PREFIX . $parametro->codigo;
                
                if (!Cache::has($cacheKey)) {
                    Cache::put($cacheKey, $parametro->valor_formatado, self::CACHE_TTL);
                    $this->adicionarChaveAoRegistro($cacheKey, self::CACHE_KEYS_LIST);
                    $aquecidos++;
                }
            }

            // Pré-aquecer cache de grupos
            $gruposAtivos = GrupoParametro::where('ativo', true)
                ->with(['parametrosAtivos'])
                ->get();

            foreach ($gruposAtivos as $grupo) {
                $cacheKey = self::CACHE_GRUPO_PREFIX . $grupo->codigo;
                
                if (!Cache::has($cacheKey)) {
                    $parametrosGrupo = [];
                    foreach ($grupo->parametrosAtivos as $parametro) {
                        $parametrosGrupo[$parametro->codigo] = $parametro->valor_formatado;
                    }
                    
                    Cache::put($cacheKey, $parametrosGrupo, self::CACHE_TTL);
                    $this->adicionarChaveAoRegistro($cacheKey, self::CACHE_KEYS_GRUPOS);
                }
            }

            // Marcar cache como pré-aquecido
            Cache::put('parametros_preaquecido', true, self::CACHE_TTL);
            Cache::put('parametros_last_update', now()->toISOString(), self::CACHE_TTL);

            // Log::info('Cache pré-aquecido com sucesso', [
                //     'parametros_aquecidos' => $aquecidos,
                //     'grupos_aquecidos' => $gruposAtivos->count()
            // ]);
        } catch (\Exception $e) {
            // Log::error('Erro ao pré-aquecer cache', [
                //     'error' => $e->getMessage()
            // ]);
        }
    }

    /**
     * Obter estatísticas do cache
     */
    public function obterEstatisticasCache(): array
    {
        try {
            $chavesParametros = Cache::get(self::CACHE_KEYS_LIST, []);
            $chavesGrupos = Cache::get(self::CACHE_KEYS_GRUPOS, []);

            $hits = 0;
            $misses = 0;

            // Verificar quais chaves estão no cache
            foreach ($chavesParametros as $chave) {
                if (Cache::has($chave)) {
                    $hits++;
                } else {
                    $misses++;
                }
            }

            foreach ($chavesGrupos as $chave) {
                if (Cache::has($chave)) {
                    $hits++;
                } else {
                    $misses++;
                }
            }

            $total = $hits + $misses;
            $hitRatio = $total > 0 ? ($hits / $total) * 100 : 0;

            return [
                'hits' => $hits,
                'misses' => $misses,
                'total' => $total,
                'hit_ratio' => round($hitRatio, 2),
                'parametros_cached' => count($chavesParametros),
                'grupos_cached' => count($chavesGrupos),
                'preaquecido' => Cache::has('parametros_preaquecido'),
                'last_update' => Cache::get('parametros_last_update'),
                'ttl' => self::CACHE_TTL
            ];
        } catch (\Exception $e) {
            // Log::error('Erro ao obter estatísticas do cache', [
                //     'error' => $e->getMessage()
            // ]);

            return [
                'hits' => 0,
                'misses' => 0,
                'total' => 0,
                'hit_ratio' => 0,
                'parametros_cached' => 0,
                'grupos_cached' => 0,
                'preaquecido' => false,
                'last_update' => null,
                'ttl' => self::CACHE_TTL
            ];
        }
    }

    /**
     * Validar integridade do cache
     */
    public function validarIntegridade(): array
    {
        $problemas = [];

        try {
            // Verificar chaves órfãs
            $chavesParametros = Cache::get(self::CACHE_KEYS_LIST, []);
            $chavesGrupos = Cache::get(self::CACHE_KEYS_GRUPOS, []);

            foreach ($chavesParametros as $chave) {
                if (!Cache::has($chave)) {
                    $problemas[] = "Chave de parâmetro órfã: {$chave}";
                }
            }

            foreach ($chavesGrupos as $chave) {
                if (!Cache::has($chave)) {
                    $problemas[] = "Chave de grupo órfã: {$chave}";
                }
            }

            // Verificar se existem parâmetros não cacheados
            $parametrosAtivos = Parametro::where('ativo', true)->pluck('codigo');
            foreach ($parametrosAtivos as $codigo) {
                $cacheKey = self::CACHE_PREFIX . $codigo;
                if (!in_array($cacheKey, $chavesParametros) && !Cache::has($cacheKey)) {
                    $problemas[] = "Parâmetro não cacheado: {$codigo}";
                }
            }

            // Log::info('Validação de integridade do cache concluída', [
                //     'problemas_encontrados' => count($problemas)
            // ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao validar integridade do cache', [
                //     'error' => $e->getMessage()
            // ]);

            $problemas[] = "Erro na validação: {$e->getMessage()}";
        }

        return $problemas;
    }

    /**
     * Reparar problemas de integridade do cache
     */
    public function repararIntegridade(): array
    {
        $reparos = [];

        try {
            // Limpar chaves órfãs
            $chavesParametros = Cache::get(self::CACHE_KEYS_LIST, []);
            $chavesGrupos = Cache::get(self::CACHE_KEYS_GRUPOS, []);

            $chavesParametrosLimpas = [];
            foreach ($chavesParametros as $chave) {
                if (Cache::has($chave)) {
                    $chavesParametrosLimpas[] = $chave;
                } else {
                    $reparos[] = "Chave órfã removida: {$chave}";
                }
            }

            $chavesGruposLimpas = [];
            foreach ($chavesGrupos as $chave) {
                if (Cache::has($chave)) {
                    $chavesGruposLimpas[] = $chave;
                } else {
                    $reparos[] = "Chave de grupo órfã removida: {$chave}";
                }
            }

            // Atualizar registros de chaves
            Cache::put(self::CACHE_KEYS_LIST, $chavesParametrosLimpas, self::CACHE_TTL);
            Cache::put(self::CACHE_KEYS_GRUPOS, $chavesGruposLimpas, self::CACHE_TTL);

            // Log::info('Reparação de integridade do cache concluída', [
                //     'reparos_realizados' => count($reparos)
            // ]);

        } catch (\Exception $e) {
            // Log::error('Erro ao reparar integridade do cache', [
                //     'error' => $e->getMessage()
            // ]);

            $reparos[] = "Erro na reparação: {$e->getMessage()}";
        }

        return $reparos;
    }

    /**
     * Adicionar chave ao registro
     */
    private function adicionarChaveAoRegistro(string $chave, string $registro): void
    {
        $chaves = Cache::get($registro, []);
        
        if (!in_array($chave, $chaves)) {
            $chaves[] = $chave;
            Cache::put($registro, $chaves, self::CACHE_TTL);
        }
    }

    /**
     * Remover chave do registro
     */
    private function removerChaveDoRegistro(string $chave, string $registro): void
    {
        $chaves = Cache::get($registro, []);
        
        $chaves = array_filter($chaves, fn($c) => $c !== $chave);
        
        Cache::put($registro, array_values($chaves), self::CACHE_TTL);
    }
}