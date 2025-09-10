<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class PreservarOtimizacoesPerformanceSeeder extends Seeder
{
    /**
     * Seeder que preserva as otimizações de performance implementadas
     * Garante que as melhorias críticas não sejam perdidas após migrate:safe
     */
    public function run(): void
    {
        $this->command->info('🚀 Preservando Otimizações de Performance v2.1');
        
        $this->preservarDebugHelper();
        $this->preservarControllerOptimizations();
        $this->corrigirPermissoes();
        $this->limparCachesObsoletos();
        
        $this->command->info('✅ Otimizações de performance preservadas com sucesso!');
    }

    /**
     * Preserva as otimizações do DebugHelper
     */
    private function preservarDebugHelper(): void
    {
        $debugHelperPath = app_path('Helpers/DebugHelper.php');
        
        if (!File::exists($debugHelperPath)) {
            $this->command->warn('⚠️ DebugHelper não encontrado, recriando...');
            $this->criarDebugHelperOtimizado($debugHelperPath);
        } else {
            $conteudo = File::get($debugHelperPath);
            
            // Verificar se as otimizações estão presentes
            if (!str_contains($conteudo, 'private static $cachedResult = null')) {
                $this->command->warn('⚠️ DebugHelper sem otimizações, corrigindo...');
                $this->criarDebugHelperOtimizado($debugHelperPath);
            } else {
                $this->command->info('✅ DebugHelper otimizado já presente');
            }
        }
    }

    /**
     * Cria o DebugHelper otimizado
     */
    private function criarDebugHelperOtimizado(string $path): void
    {
        $conteudo = <<<'PHP'
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
PHP;

        File::put($path, $conteudo);
        $this->command->info('✅ DebugHelper otimizado criado/atualizado');
    }

    /**
     * Preserva as otimizações dos controllers
     */
    private function preservarControllerOptimizations(): void
    {
        $controllerPath = app_path('Http/Controllers/ProposicaoAssinaturaController.php');
        
        if (File::exists($controllerPath)) {
            $conteudo = File::get($controllerPath);
            
            // Verificar se as otimizações de eager loading estão presentes
            if (str_contains($conteudo, '$user = Auth::user()->load(\'roles\');')) {
                $this->command->info('✅ Otimizações de eager loading já presentes no Controller');
            } else {
                $this->command->warn('⚠️ Otimizações de Controller podem ter sido perdidas');
                $this->command->info('🔧 Execute novamente as otimizações se necessário');
            }
        }
    }

    /**
     * Corrige permissões de arquivos após reset
     */
    private function corrigirPermissoes(): void
    {
        $this->command->info('📁 Corrigindo permissões de arquivos...');
        
        try {
            // Detectar usuário PHP correto (pode ser laravel ou www-data)
            $phpUser = 'laravel';
            if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
                $userInfo = posix_getpwuid(posix_geteuid());
                if ($userInfo && isset($userInfo['name'])) {
                    $phpUser = $userInfo['name'];
                }
            }
            
            $storagePath = storage_path();
            $bootstrapCachePath = base_path('bootstrap/cache');
            
            // Executar comandos de correção de permissão via shell
            if (file_exists('/usr/bin/chown')) {
                exec("chown -R {$phpUser}:{$phpUser} {$storagePath} 2>/dev/null");
                exec("chown -R {$phpUser}:{$phpUser} {$bootstrapCachePath} 2>/dev/null");
                
                exec("find {$storagePath} -type d -exec chmod 775 {} \; 2>/dev/null");
                exec("find {$storagePath} -type f -exec chmod 664 {} \; 2>/dev/null");
                exec("chmod -R 775 {$bootstrapCachePath} 2>/dev/null");
            }
            
            $this->command->info("✅ Permissões corrigidas para usuário: {$phpUser}");
        } catch (\Exception $e) {
            $this->command->warn("⚠️ Erro ao corrigir permissões: " . $e->getMessage());
            $this->command->info("🔧 Execute manualmente se necessário:");
            $this->command->info("   chown -R {$phpUser}:{$phpUser} storage/ bootstrap/cache/");
            $this->command->info("   chmod -R 775 storage/ bootstrap/cache/");
        }
    }

    /**
     * Limpa caches obsoletos
     */
    private function limparCachesObsoletos(): void
    {
        $this->command->info('🧹 Limpando caches obsoletos...');
        
        try {
            // Limpar cache específico do debug logger
            Cache::forget('debug_logger_ativo');
            
            // Limpar views compiladas
            if (file_exists(storage_path('framework/views'))) {
                $files = glob(storage_path('framework/views/*.php'));
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== '.gitignore') {
                        unlink($file);
                    }
                }
            }
            
            $this->command->info('✅ Caches limpos');
        } catch (\Exception $e) {
            $this->command->warn('⚠️ Erro ao limpar caches: ' . $e->getMessage());
        }
    }
}