<?php

namespace App\Helpers;

use App\Models\Parametro\ParametroCampo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DebugHelper
{
    /**
     * Cache estático para evitar múltiplas verificações na mesma requisição
     */
    private static $cachedResult = null;
    
    /**
     * Verifica se o debug logger está ativo
     * OTIMIZADO: Cache estático + Cache persistente + query única otimizada + fallback seguro
     */
    public static function isDebugLoggerActive(): bool
    {
        // OTIMIZAÇÃO: Cache estático na mesma requisição (evita múltiplas chamadas)
        if (self::$cachedResult !== null) {
            return self::$cachedResult;
        }
        
        // Cache mais longo (1 hora) para reduzir queries
        self::$cachedResult = Cache::remember('debug_logger_ativo', 3600, function () {
            try {
                // OTIMIZAÇÃO: Query única com JOIN ao invés de múltiplas consultas
                $resultado = DB::table('parametros_campos')
                    ->join('parametros_valores', 'parametros_campos.id', '=', 'parametros_valores.campo_id')
                    ->where('parametros_campos.nome', 'debug_logger_ativo')
                    ->where('parametros_campos.ativo', true)
                    ->orderBy('parametros_valores.created_at', 'desc')
                    ->select('parametros_valores.valor')
                    ->first();
                
                if (!$resultado) {
                    // Fallback: verificar se parâmetro existe sem valor (padrão false)
                    $existeCampo = DB::table('parametros_campos')
                        ->where('nome', 'debug_logger_ativo')
                        ->where('ativo', true)
                        ->exists();
                    
                    return $existeCampo ? false : false; // Padrão sempre false para segurança
                }
                
                // Converte string para boolean de forma otimizada
                $valor = $resultado->valor;
                if (is_string($valor)) {
                    return in_array(strtolower($valor), ['true', '1', 'yes', 'on']);
                }
                
                return (bool) $valor;
            } catch (\Exception $e) {
                // Em caso de erro, retorna false por segurança e não falha o sistema
                \Log::warning('DebugHelper: Erro ao verificar debug_logger_ativo', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return false;
            }
        });
        
        return self::$cachedResult;
    }
    
    /**
     * Limpa o cache do debug logger (estático e persistente)
     */
    public static function clearCache(): void
    {
        // Limpar cache estático
        self::$cachedResult = null;
        
        // Limpar cache persistente
        Cache::forget('debug_logger_ativo');
    }
}