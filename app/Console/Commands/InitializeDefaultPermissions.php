<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DynamicPermissionService;
use App\Services\RouteDiscoveryService;
use App\Models\User;

class InitializeDefaultPermissions extends Command
{
    protected $signature = 'permissions:initialize 
                            {--force : Forçar recriação das permissões existentes}
                            {--role= : Aplicar apenas para um tipo de usuário específico}';
    
    protected $description = 'Inicializar permissões padrão para todos os tipos de usuário';

    private DynamicPermissionService $permissionService;
    private RouteDiscoveryService $routeService;

    public function __construct(
        DynamicPermissionService $permissionService,
        RouteDiscoveryService $routeService
    ) {
        parent::__construct();
        $this->permissionService = $permissionService;
        $this->routeService = $routeService;
    }

    public function handle()
    {
        $this->info('🚀 Inicializando Permissões Padrão do Sistema');
        $this->newLine();

        $force = $this->option('force');
        $specificRole = $this->option('role');

        // Obter configurações padrão
        $defaults = $this->routeService->getDefaultPermissionsByRole();

        if ($specificRole) {
            if (!isset($defaults[$specificRole])) {
                $this->error("❌ Tipo de usuário '{$specificRole}' não encontrado!");
                return 1;
            }
            $defaults = [$specificRole => $defaults[$specificRole]];
            $this->info("🎯 Aplicando permissões apenas para: {$specificRole}");
        }

        $this->info("📋 Tipos de usuário a serem configurados:");
        foreach ($defaults as $roleName => $config) {
            $this->line("  • {$roleName} - {$config['description']}");
        }
        $this->newLine();

        if (!$force && !$this->confirm('Deseja continuar com a inicialização?', true)) {
            $this->warn('⚠️ Operação cancelada pelo usuário');
            return 0;
        }

        $results = [];
        $successCount = 0;
        $errorCount = 0;

        foreach ($defaults as $roleName => $config) {
            $this->info("🔧 Configurando: {$roleName}");
            
            try {
                // Verificar se já existe configuração
                $existingPermissions = $this->permissionService->getRolePermissions($roleName);
                
                if ($existingPermissions->count() > 0 && !$force) {
                    $this->warn("  ⚡ Já possui configuração ({$existingPermissions->count()} permissões) - Use --force para sobrescrever");
                    continue;
                }

                if ($force && $existingPermissions->count() > 0) {
                    $this->warn("  🔄 Sobrescrevendo configuração existente ({$existingPermissions->count()} permissões)");
                }

                // Aplicar permissões padrão
                $success = $this->permissionService->applyDefaultPermissions($roleName);
                
                if ($success) {
                    $newPermissions = $this->permissionService->getRolePermissions($roleName);
                    $permissionCount = $newPermissions->where('can_access', true)->count();
                    
                    $this->info("  ✅ Sucesso! {$permissionCount} permissões aplicadas");
                    $results[$roleName] = [
                        'success' => true,
                        'permissions_count' => $permissionCount,
                        'description' => $config['description']
                    ];
                    $successCount++;
                } else {
                    $this->error("  ❌ Falha ao aplicar permissões padrão");
                    $results[$roleName] = [
                        'success' => false,
                        'error' => 'Falha na aplicação das permissões'
                    ];
                    $errorCount++;
                }

            } catch (\Exception $e) {
                $this->error("  ❌ Erro: " . $e->getMessage());
                $results[$roleName] = [
                    'success' => false,
                    'error' => $e->getMessage()
                ];
                $errorCount++;
            }
        }

        $this->newLine();
        $this->info('📊 RESUMO DA INICIALIZAÇÃO');
        $this->line('═══════════════════════════════');

        foreach ($results as $roleName => $result) {
            if ($result['success']) {
                $this->info("✅ {$roleName}: {$result['permissions_count']} permissões");
                $this->line("   {$result['description']}");
            } else {
                $this->error("❌ {$roleName}: {$result['error']}");
            }
        }

        $this->newLine();
        $this->line("🎯 Resultados: {$successCount} sucessos, {$errorCount} erros");

        if ($errorCount === 0) {
            $this->info('🎉 Todas as permissões padrão foram aplicadas com sucesso!');
            $this->newLine();
            $this->info('💡 Próximos passos:');
            $this->line('   • Acesse /admin/screen-permissions para personalizar');
            $this->line('   • Teste as permissões com usuários reais');
            $this->line('   • Ajuste conforme necessário para sua organização');
            return 0;
        } else {
            $this->warn('⚠️ Algumas permissões não foram aplicadas corretamente.');
            $this->line('   Verifique os logs para mais detalhes.');
            return 1;
        }
    }
}