<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;
use App\Models\User;

class TestMenuPermissions extends Command
{
    protected $signature = 'permissions:test-menu {role?}';
    protected $description = 'Testa as permissões de menu para um perfil específico';

    public function handle()
    {
        $role = $this->argument('role') ?? $this->choice('Selecione o perfil para testar:', ['PARLAMENTAR', 'LEGISLATIVO', 'PROTOCOLO']);
        
        $this->info("🧪 Testando permissões de menu para o perfil: {$role}");
        
        // Simular módulos principais do menu
        $menuModules = [
            'dashboard' => 'Dashboard',
            'parlamentares' => 'Parlamentares',
            'partidos' => 'Partidos', 
            'proposicoes' => 'Proposições',
            'comissoes' => 'Comissões',
            'sessoes' => 'Sessões',
            'usuarios' => 'Usuários (Admin)',
            'documentos' => 'Documentos',
            'admin' => 'Administração',
            'parametros' => 'Parâmetros'
        ];
        
        $this->info('📋 Módulos que o usuário PODE acessar:');
        $allowedModules = [];
        
        foreach ($menuModules as $module => $displayName) {
            $hasAccess = ScreenPermission::where('role_name', $role)
                ->where('screen_module', $module)
                ->where('can_access', true)
                ->exists();
                
            if ($hasAccess) {
                $allowedModules[] = "✅ {$displayName}";
            }
        }
        
        if (empty($allowedModules)) {
            $this->warn('❌ Nenhum módulo permitido encontrado!');
        } else {
            foreach ($allowedModules as $module) {
                $this->line($module);
            }
        }
        
        $this->newLine();
        $this->info('🚫 Módulos que o usuário NÃO PODE acessar:');
        $deniedModules = [];
        
        foreach ($menuModules as $module => $displayName) {
            $hasAccess = ScreenPermission::where('role_name', $role)
                ->where('screen_module', $module)
                ->where('can_access', true)
                ->exists();
                
            if (!$hasAccess) {
                $deniedModules[] = "❌ {$displayName}";
            }
        }
        
        if (empty($deniedModules)) {
            $this->info('✅ Usuário tem acesso a todos os módulos!');
        } else {
            foreach ($deniedModules as $module) {
                $this->line($module);
            }
        }
        
        // Mostrar estatísticas
        $this->newLine();
        $totalPermissions = ScreenPermission::where('role_name', $role)->count();
        $allowedPermissions = ScreenPermission::where('role_name', $role)->where('can_access', true)->count();
        $deniedPermissions = ScreenPermission::where('role_name', $role)->where('can_access', false)->count();
        
        $this->info("📊 Estatísticas de permissões para {$role}:");
        $this->table(
            ['Tipo', 'Quantidade', 'Percentual'],
            [
                ['Total de rotas', $totalPermissions, '100%'],
                ['Rotas permitidas', $allowedPermissions, round(($allowedPermissions / $totalPermissions) * 100, 1) . '%'],
                ['Rotas negadas', $deniedPermissions, round(($deniedPermissions / $totalPermissions) * 100, 1) . '%'],
            ]
        );
        
        return 0;
    }
}