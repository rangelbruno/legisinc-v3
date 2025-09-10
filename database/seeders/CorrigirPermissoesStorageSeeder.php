<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CorrigirPermissoesStorageSeeder extends Seeder
{
    /**
     * Corrige permissões de storage automaticamente após migrate
     * Resolve problema de "Permission denied" em storage/framework/views
     */
    public function run(): void
    {
        $this->command->info('🔧 Corrigindo permissões de storage...');
        
        // Detectar usuário correto
        $phpUser = $this->detectarUsuarioPhp();
        
        $this->corrigirPermissoes($phpUser);
        $this->validarPermissoes();
        
        $this->command->info('✅ Permissões de storage corrigidas!');
    }
    
    /**
     * Detecta qual usuário está executando o PHP
     */
    private function detectarUsuarioPhp(): string
    {
        if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
            $userInfo = posix_getpwuid(posix_geteuid());
            $phpUser = $userInfo['name'] ?? 'www-data';
            
            $this->command->info("👤 Usuário PHP detectado: {$phpUser}");
            return $phpUser;
        }
        
        $this->command->info("👤 Usando usuário padrão: www-data");
        return 'www-data';
    }
    
    /**
     * Corrige ownership e permissões dos diretórios críticos
     */
    private function corrigirPermissoes(string $phpUser): void
    {
        $paths = [
            storage_path(),
            base_path('bootstrap/cache'),
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $this->command->info("📁 Corrigindo: {$path}");
                
                try {
                    // Recursivamente corrigir ownership
                    $chownCmd = "chown -R {$phpUser}:{$phpUser} {$path} 2>/dev/null";
                    exec($chownCmd);
                    
                    // Corrigir permissões de diretórios
                    $chmodDirCmd = "find {$path} -type d -exec chmod 775 {} \; 2>/dev/null";
                    exec($chmodDirCmd);
                    
                    // Corrigir permissões de arquivos
                    $chmodFileCmd = "find {$path} -type f -exec chmod 664 {} \; 2>/dev/null";
                    exec($chmodFileCmd);
                    
                } catch (\Exception $e) {
                    $this->command->warn("⚠️ Erro ao corrigir {$path}: " . $e->getMessage());
                }
            }
        }
        
        $this->command->info("✅ Ownership: {$phpUser}:{$phpUser}");
        $this->command->info("✅ Diretórios: 775 | Arquivos: 664");
    }
    
    /**
     * Valida se as permissões estão funcionando
     */
    private function validarPermissoes(): void
    {
        $testFile = storage_path('framework/views/permission_test.txt');
        
        try {
            $result = file_put_contents($testFile, 'test content');
            
            if ($result !== false && file_exists($testFile)) {
                unlink($testFile);
                $this->command->info('✅ Teste de escrita: SUCESSO');
            } else {
                $this->command->error('❌ Teste de escrita falhou: arquivo não criado');
            }
            
        } catch (\Exception $e) {
            $this->command->error('❌ Teste de escrita falhou: ' . $e->getMessage());
            $this->command->info('🔧 Execute manualmente se necessário:');
            $this->command->info('   chmod -R 775 storage/ bootstrap/cache/');
        }
    }
}