<?php

namespace App\Console\Commands;

use App\Models\ScreenPermission;
use App\Enums\UserRole;
use App\Enums\SystemModule;
use Illuminate\Console\Command;

class InitializePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:initialize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializar permissões básicas: Dashboard para todos os perfis e estrutura completa';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Inicializando sistema de permissões...');
        
        $createdCount = 0;
        $updatedCount = 0;
        
        // Para cada role, criar permissões para todas as rotas disponíveis
        foreach (UserRole::cases() as $role) {
            $this->info("Processando role: {$role->value}");
            
            foreach (SystemModule::cases() as $module) {
                foreach ($module->getRoutes() as $route => $name) {
                    $permission = ScreenPermission::firstOrCreate(
                        [
                            'role_name' => $role->value,
                            'screen_route' => $route,
                        ],
                        [
                            'screen_name' => $name,
                            'screen_module' => $module->value,
                            'can_access' => $this->getDefaultAccess($role->value, $route),
                        ]
                    );
                    
                    if ($permission->wasRecentlyCreated) {
                        $createdCount++;
                    } else {
                        // Atualizar Dashboard se não estiver habilitado
                        if ($route === 'dashboard' && !$permission->can_access) {
                            $permission->update(['can_access' => true]);
                            $updatedCount++;
                        }
                    }
                }
            }
        }
        
        $this->info("✅ Permissões inicializadas:");
        $this->info("   - {$createdCount} permissões criadas");
        $this->info("   - {$updatedCount} permissões atualizadas");
        $this->info("   - Dashboard habilitado para todos os perfis");
        
        return 0;
    }
    
    /**
     * Definir acesso padrão baseado no role e rota
     */
    private function getDefaultAccess(string $roleName, string $route): bool
    {
        // Dashboard sempre habilitado para todos
        if ($route === 'dashboard.index') {
            return true;
        }
        
        // Admin sempre tem acesso total
        if ($roleName === 'ADMIN') {
            return true;
        }
        
        // Outras permissões começam desabilitadas (admin configura conforme necessário)
        return false;
    }
}
