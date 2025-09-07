<?php

namespace App\Helpers;

use App\Models\Parametro\ParametroCampo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DebugHelper
{
    /**
     * Verifica se o debug logger está ativo
     */
    public static function isDebugLoggerActive(): bool
    {
        return Cache::remember('debug_logger_ativo', 300, function () {
            try {
                // Busca o campo debug_logger_ativo no sistema modular
                $campo = ParametroCampo::where('nome', 'debug_logger_ativo')
                    ->where('ativo', true)
                    ->first();
                
                if (!$campo) {
                    return false;
                }
                
                // Busca o valor atual do campo
                $valor = DB::table('parametros_valores')
                    ->where('campo_id', $campo->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if (!$valor) {
                    return false;
                }
                
                // Converte string para boolean
                if (is_string($valor->valor)) {
                    return in_array(strtolower($valor->valor), ['true', '1', 'yes', 'on']);
                }
                
                return (bool) $valor->valor;
            } catch (\Exception $e) {
                // Em caso de erro, retorna false por segurança
                return false;
            }
        });
    }
    
    /**
     * Limpa o cache do debug logger
     */
    public static function clearCache(): void
    {
        Cache::forget('debug_logger_ativo');
    }
}