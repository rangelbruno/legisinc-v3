<?php

use App\Models\Parametro;
use App\Models\GrupoParametro;
use App\Services\Admin\CacheParametroService;
use App\Services\Admin\ParametroService;

if (!function_exists('parametro')) {
    /**
     * Obter valor de um parâmetro
     * 
     * @param string $codigo Código do parâmetro
     * @param mixed $default Valor padrão se não encontrado
     * @return mixed
     */
    function parametro(string $codigo, mixed $default = null): mixed
    {
        static $cacheService = null;
        
        if (!$cacheService) {
            $cacheService = app(CacheParametroService::class);
        }
        
        return $cacheService->obterParametroCache($codigo, function () use ($codigo, $default) {
            try {
                $parametro = Parametro::where('codigo', $codigo)
                    ->where('ativo', true)
                    ->first();
                
                if (!$parametro) {
                    return $default;
                }
                
                return $parametro->valor_formatado ?? $default;
            } catch (\Exception $e) {
                \Log::error('Erro ao obter parâmetro', [
                    'codigo' => $codigo,
                    'error' => $e->getMessage()
                ]);
                
                return $default;
            }
        });
    }
}

if (!function_exists('parametros_grupo')) {
    /**
     * Obter todos os parâmetros de um grupo
     * 
     * @param string $codigoGrupo Código do grupo
     * @return array
     */
    function parametros_grupo(string $codigoGrupo): array
    {
        static $cacheService = null;
        
        if (!$cacheService) {
            $cacheService = app(CacheParametroService::class);
        }
        
        return $cacheService->obterGrupoCache($codigoGrupo, function () use ($codigoGrupo) {
            try {
                $grupo = GrupoParametro::where('codigo', $codigoGrupo)
                    ->where('ativo', true)
                    ->first();
                
                if (!$grupo) {
                    return [];
                }
                
                $parametros = Parametro::where('grupo_parametro_id', $grupo->id)
                    ->where('ativo', true)
                    ->where('visivel', true)
                    ->orderBy('ordem')
                    ->get();
                
                $resultado = [];
                foreach ($parametros as $parametro) {
                    $resultado[$parametro->codigo] = $parametro->valor_formatado;
                }
                
                return $resultado;
            } catch (\Exception $e) {
                \Log::error('Erro ao obter parâmetros do grupo', [
                    'codigo_grupo' => $codigoGrupo,
                    'error' => $e->getMessage()
                ]);
                
                return [];
            }
        });
    }
}

if (!function_exists('ParametroHelper')) {
    /**
     * Classe helper para parâmetros com métodos estáticos
     */
    class ParametroHelper
    {
        /**
         * Obter parâmetro como string
         */
        public static function string(string $codigo, string $default = ''): string
        {
            return (string) parametro($codigo, $default);
        }
        
        /**
         * Obter parâmetro como integer
         */
        public static function int(string $codigo, int $default = 0): int
        {
            return (int) parametro($codigo, $default);
        }
        
        /**
         * Obter parâmetro como float
         */
        public static function float(string $codigo, float $default = 0.0): float
        {
            return (float) parametro($codigo, $default);
        }
        
        /**
         * Obter parâmetro como boolean
         */
        public static function bool(string $codigo, bool $default = false): bool
        {
            $valor = parametro($codigo, $default);
            
            if (is_bool($valor)) {
                return $valor;
            }
            
            if (is_string($valor)) {
                return in_array(strtolower($valor), ['true', '1', 'yes', 'sim', 'on']);
            }
            
            return (bool) $valor;
        }
        
        /**
         * Obter parâmetro como array
         */
        public static function array(string $codigo, array $default = []): array
        {
            $valor = parametro($codigo, $default);
            
            if (is_array($valor)) {
                return $valor;
            }
            
            if (is_string($valor)) {
                // Tentar decodificar JSON primeiro
                $decoded = json_decode($valor, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
                
                // Se não for JSON, separar por vírgula
                return array_map('trim', explode(',', $valor));
            }
            
            return $default;
        }
        
        /**
         * Obter parâmetro como JSON decodificado
         */
        public static function json(string $codigo, array $default = []): array
        {
            $valor = parametro($codigo, $default);
            
            if (is_array($valor)) {
                return $valor;
            }
            
            if (is_string($valor)) {
                $decoded = json_decode($valor, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $default;
            }
            
            return $default;
        }
        
        /**
         * Verificar se parâmetro está ativo (para boolean)
         */
        public static function ativo(string $codigo, bool $default = false): bool
        {
            return self::bool($codigo, $default);
        }
        
        /**
         * Obter parâmetro como email
         */
        public static function email(string $codigo, string $default = ''): string
        {
            $valor = self::string($codigo, $default);
            
            if (filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                return $valor;
            }
            
            return $default;
        }
        
        /**
         * Obter parâmetro como URL
         */
        public static function url(string $codigo, string $default = ''): string
        {
            $valor = self::string($codigo, $default);
            
            if (filter_var($valor, FILTER_VALIDATE_URL)) {
                return $valor;
            }
            
            return $default;
        }
        
        /**
         * Obter parâmetro como cor (hex)
         */
        public static function cor(string $codigo, string $default = '#000000'): string
        {
            $valor = self::string($codigo, $default);
            
            if (preg_match('/^#[A-Fa-f0-9]{6}$/', $valor)) {
                return $valor;
            }
            
            return $default;
        }
        
        /**
         * Obter parâmetro como data
         */
        public static function data(string $codigo, ?\DateTime $default = null): ?\DateTime
        {
            $valor = parametro($codigo, $default);
            
            if ($valor instanceof \DateTime) {
                return $valor;
            }
            
            if (is_string($valor)) {
                try {
                    return new \DateTime($valor);
                } catch (\Exception $e) {
                    return $default;
                }
            }
            
            return $default;
        }
        
        /**
         * Verificar se parâmetro existe
         */
        public static function existe(string $codigo): bool
        {
            try {
                return Parametro::where('codigo', $codigo)
                    ->where('ativo', true)
                    ->exists();
            } catch (\Exception $e) {
                return false;
            }
        }
        
        /**
         * Obter parâmetro com informações completas
         */
        public static function completo(string $codigo): ?array
        {
            try {
                $parametro = Parametro::with(['grupoParametro', 'tipoParametro'])
                    ->where('codigo', $codigo)
                    ->where('ativo', true)
                    ->first();
                
                if (!$parametro) {
                    return null;
                }
                
                return [
                    'codigo' => $parametro->codigo,
                    'nome' => $parametro->nome,
                    'valor' => $parametro->valor_formatado,
                    'valor_display' => $parametro->valor_display,
                    'tipo' => $parametro->tipoParametro->codigo,
                    'grupo' => $parametro->grupoParametro->codigo,
                    'descricao' => $parametro->descricao,
                    'help_text' => $parametro->help_text,
                    'obrigatorio' => $parametro->obrigatorio,
                    'editavel' => $parametro->editavel,
                    'visivel' => $parametro->visivel
                ];
            } catch (\Exception $e) {
                \Log::error('Erro ao obter parâmetro completo', [
                    'codigo' => $codigo,
                    'error' => $e->getMessage()
                ]);
                
                return null;
            }
        }
        
        /**
         * Definir valor de parâmetro (apenas para uso interno)
         */
        public static function definir(string $codigo, mixed $valor): bool
        {
            try {
                $parametro = Parametro::where('codigo', $codigo)
                    ->where('ativo', true)
                    ->where('editavel', true)
                    ->first();
                
                if (!$parametro) {
                    return false;
                }
                
                $parametro->valor = $valor;
                $parametro->save();
                
                // Limpar cache
                $cacheService = app(CacheParametroService::class);
                $cacheService->limparCacheParametro($codigo);
                
                return true;
            } catch (\Exception $e) {
                \Log::error('Erro ao definir parâmetro', [
                    'codigo' => $codigo,
                    'valor' => $valor,
                    'error' => $e->getMessage()
                ]);
                
                return false;
            }
        }
        
        /**
         * Obter lista de parâmetros por prefixo
         */
        public static function porPrefixo(string $prefixo): array
        {
            try {
                $parametros = Parametro::where('codigo', 'LIKE', $prefixo . '%')
                    ->where('ativo', true)
                    ->where('visivel', true)
                    ->orderBy('codigo')
                    ->get();
                
                $resultado = [];
                foreach ($parametros as $parametro) {
                    $resultado[$parametro->codigo] = $parametro->valor_formatado;
                }
                
                return $resultado;
            } catch (\Exception $e) {
                \Log::error('Erro ao obter parâmetros por prefixo', [
                    'prefixo' => $prefixo,
                    'error' => $e->getMessage()
                ]);
                
                return [];
            }
        }
        
        /**
         * Limpar cache de parâmetros
         */
        public static function limparCache(?string $codigo = null): void
        {
            try {
                $cacheService = app(CacheParametroService::class);
                
                if ($codigo) {
                    $cacheService->limparCacheParametro($codigo);
                } else {
                    $cacheService->limparTodoCache();
                }
            } catch (\Exception $e) {
                \Log::error('Erro ao limpar cache de parâmetros', [
                    'codigo' => $codigo,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        /**
         * Recarregar cache de parâmetros
         */
        public static function recarregarCache(): void
        {
            try {
                $cacheService = app(CacheParametroService::class);
                $cacheService->limparTodoCache();
                $cacheService->preaquecerCache();
            } catch (\Exception $e) {
                \Log::error('Erro ao recarregar cache de parâmetros', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}

// Aliases para facilitar o uso
if (!function_exists('param')) {
    function param(string $codigo, mixed $default = null): mixed
    {
        return parametro($codigo, $default);
    }
}

if (!function_exists('param_grupo')) {
    function param_grupo(string $codigoGrupo): array
    {
        return parametros_grupo($codigoGrupo);
    }
}

if (!function_exists('config_sistema')) {
    function config_sistema(string $chave, mixed $default = null): mixed
    {
        return parametro('sistema.' . $chave, $default);
    }
}

if (!function_exists('config_legislativo')) {
    function config_legislativo(string $chave, mixed $default = null): mixed
    {
        return parametro('legislativo.' . $chave, $default);
    }
}

if (!function_exists('config_notificacoes')) {
    function config_notificacoes(string $chave, mixed $default = null): mixed
    {
        return parametro('notificacoes.' . $chave, $default);
    }
}

if (!function_exists('config_seguranca')) {
    function config_seguranca(string $chave, mixed $default = null): mixed
    {
        return parametro('seguranca.' . $chave, $default);
    }
}

if (!function_exists('config_interface')) {
    function config_interface(string $chave, mixed $default = null): mixed
    {
        return parametro('interface.' . $chave, $default);
    }
}

if (!function_exists('config_performance')) {
    function config_performance(string $chave, mixed $default = null): mixed
    {
        return parametro('performance.' . $chave, $default);
    }
}

if (!function_exists('config_backup')) {
    function config_backup(string $chave, mixed $default = null): mixed
    {
        return parametro('backup.' . $chave, $default);
    }
}

// Helpers específicos para valores comuns
if (!function_exists('nome_sistema')) {
    function nome_sistema(): string
    {
        return ParametroHelper::string('sistema.nome', 'LegisInc');
    }
}

if (!function_exists('versao_sistema')) {
    function versao_sistema(): string
    {
        return ParametroHelper::string('sistema.versao', '1.0.0');
    }
}

if (!function_exists('sistema_em_manutencao')) {
    function sistema_em_manutencao(): bool
    {
        return ParametroHelper::bool('sistema.manutencao', false);
    }
}

if (!function_exists('admin_email')) {
    function admin_email(): string
    {
        return ParametroHelper::email('sistema.admin_email', 'admin@sistema.com');
    }
}

if (!function_exists('cache_habilitado')) {
    function cache_habilitado(): bool
    {
        return ParametroHelper::bool('performance.cache_habilitado', true);
    }
}

if (!function_exists('cache_ttl')) {
    function cache_ttl(): int
    {
        return ParametroHelper::int('performance.cache_ttl', 60);
    }
}

if (!function_exists('itens_por_pagina')) {
    function itens_por_pagina(): int
    {
        return ParametroHelper::int('interface.itens_pagina', 20);
    }
}

if (!function_exists('tema_interface')) {
    function tema_interface(): string
    {
        return ParametroHelper::string('interface.tema', 'light');
    }
}

if (!function_exists('cor_primaria')) {
    function cor_primaria(): string
    {
        return ParametroHelper::cor('interface.cor_primaria', '#009EF7');
    }
}

// Validação de valores
if (!function_exists('validar_parametro')) {
    function validar_parametro(string $codigo, mixed $valor): array
    {
        try {
            $parametroService = app(ParametroService::class);
            
            $parametro = Parametro::where('codigo', $codigo)
                ->where('ativo', true)
                ->first();
            
            if (!$parametro) {
                return [
                    'valido' => false,
                    'erros' => ['Parâmetro não encontrado']
                ];
            }
            
            return $parametroService->validarValorParametro(
                $parametro->tipo_parametro_id,
                $valor,
                $parametro->regras_validacao ?? []
            );
        } catch (\Exception $e) {
            return [
                'valido' => false,
                'erros' => ['Erro na validação: ' . $e->getMessage()]
            ];
        }
    }
}