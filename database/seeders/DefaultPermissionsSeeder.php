<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\DynamicPermissionService;
use App\Services\RouteDiscoveryService;
use Illuminate\Support\Facades\Log;

class DefaultPermissionsSeeder extends Seeder
{
    private DynamicPermissionService $permissionService;
    private RouteDiscoveryService $routeService;

    public function __construct(
        DynamicPermissionService $permissionService,
        RouteDiscoveryService $routeService
    ) {
        $this->permissionService = $permissionService;
        $this->routeService = $routeService;
    }

    /**
     * Aplicar configurações padrão de permissão para todos os tipos de usuário.
     */
    public function run(): void
    {
        $this->command->info('🚀 Aplicando Permissões Padrão para Tipos de Usuário');
        
        $defaults = $this->routeService->getDefaultPermissionsByRole();
        $successCount = 0;
        $errorCount = 0;

        foreach ($defaults as $roleName => $config) {
            $this->command->info("🔧 Configurando: {$roleName}");
            
            try {
                // Verificar se já existe configuração
                $existingPermissions = $this->permissionService->getRolePermissions($roleName);
                
                if ($existingPermissions->count() > 0) {
                    $this->command->warn("  ⚡ Já possui configuração - Mantendo existente");
                    continue;
                }

                // Aplicar permissões padrão
                $success = $this->permissionService->applyDefaultPermissions($roleName);
                
                if ($success) {
                    $newPermissions = \App\Models\ScreenPermission::where('role_name', $roleName)
                        ->where('can_access', true)
                        ->count();
                    
                    $this->command->info("  ✅ {$newPermissions} permissões aplicadas");
                    $successCount++;
                    
                    // Log detalhado
                    Log::info("Default permissions applied for role: {$roleName}", [
                        'permissions_count' => $newPermissions,
                        'description' => $config['description']
                    ]);
                } else {
                    $this->command->error("  ❌ Falha ao aplicar permissões");
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->command->error("  ❌ Erro: " . $e->getMessage());
                Log::error("Error applying default permissions for role: {$roleName}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $errorCount++;
            }
        }

        $this->command->newLine();
        
        if ($errorCount === 0) {
            $this->command->info("🎉 Permissões padrão aplicadas com sucesso!");
            $this->command->info("📊 {$successCount} tipos de usuário configurados");
            
            // Mostrar resumo das configurações
            $this->showPermissionsSummary($defaults);
        } else {
            $this->command->warn("⚠️ Algumas configurações falharam: {$errorCount} erros");
        }
    }

    /**
     * Mostrar resumo das permissões por tipo de usuário
     */
    private function showPermissionsSummary(array $defaults): void
    {
        $this->command->newLine();
        $this->command->info('📋 RESUMO DAS PERMISSÕES PADRÃO');
        $this->command->line('═══════════════════════════════════');

        foreach ($defaults as $roleName => $config) {
            $level = $config['level'] ?? 0;
            $description = $config['description'] ?? '';
            $accessType = $config['default_access'];
            
            $this->command->line("🔹 {$roleName} (Nível {$level})");
            $this->command->line("   {$description}");
            
            if ($accessType === 'all') {
                $this->command->line("   🌟 Acesso total a todas as funcionalidades");
            } else {
                $permissionsCount = count($config['permissions'] ?? []);
                $this->command->line("   📱 {$permissionsCount} telas específicas habilitadas");
                
                // Mostrar algumas permissões principais
                if (isset($config['permissions'])) {
                    $mainPermissions = array_slice(array_keys($config['permissions']), 0, 3);
                    foreach ($mainPermissions as $permission) {
                        $this->command->line("     • {$permission}");
                    }
                    if (count($config['permissions']) > 3) {
                        $remaining = count($config['permissions']) - 3;
                        $this->command->line("     • ... e mais {$remaining} telas");
                    }
                }
            }
            $this->command->newLine();
        }

        $this->command->info('💡 Para personalizar as permissões:');
        $this->command->line('   php artisan permissions:initialize --help');
        $this->command->line('   Acesse: /admin/screen-permissions');
    }
}