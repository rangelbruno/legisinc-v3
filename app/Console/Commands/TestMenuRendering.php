<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class TestMenuRendering extends Command
{
    protected $signature = 'permissions:test-menu-rendering {role?}';
    protected $description = 'Simula a renderização do menu lateral para um perfil específico';

    public function handle()
    {
        $role = $this->argument('role') ?? $this->choice('Selecione o perfil para simular o menu:', ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO', 'PROTOCOLO']);
        
        $this->info("🎭 Simulando menu lateral para o perfil: {$role}");
        $this->newLine();
        
        // Simular a lógica do menu lateral
        $menuItems = [
            [
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'module' => 'dashboard',
                'icon' => 'ki-home',
                'children' => []
            ],
            [
                'title' => 'Parlamentares',
                'route' => null,
                'module' => 'parlamentares',
                'icon' => 'ki-people',
                'children' => [
                    ['title' => 'Lista de Parlamentares', 'route' => 'parlamentares.index'],
                    ['title' => 'Mesa Diretora', 'route' => 'parlamentares.mesa-diretora'],
                ]
            ],
            [
                'title' => 'Partidos',
                'route' => null,
                'module' => 'partidos',
                'icon' => 'ki-flag',
                'children' => [
                    ['title' => 'Lista de Partidos', 'route' => 'partidos.index'],
                ]
            ],
            [
                'title' => 'Proposições',
                'route' => null,
                'module' => 'proposicoes',
                'icon' => 'ki-file-up',
                'children' => [
                    ['title' => 'Criar Proposição', 'route' => 'proposicoes.criar'],
                    ['title' => 'Minhas Proposições', 'route' => 'proposicoes.minhas-proposicoes'],
                    ['title' => 'Assinatura', 'route' => 'proposicoes.assinatura'],
                ]
            ],
            [
                'title' => 'Comissões',
                'route' => null,
                'module' => 'comissoes',
                'icon' => 'ki-category',
                'children' => [
                    ['title' => 'Lista de Comissões', 'route' => 'comissoes.index'],
                    ['title' => 'Minhas Comissões', 'route' => 'comissoes.minhas-comissoes'],
                ]
            ],
            [
                'title' => 'Sessões',
                'route' => null,
                'module' => 'sessoes',
                'icon' => 'ki-calendar',
                'children' => [
                    ['title' => 'Lista de Sessões', 'route' => 'admin.sessions.index'],
                    ['title' => 'Agenda', 'route' => 'sessoes.agenda'],
                ]
            ],
            [
                'title' => 'Votações',
                'route' => null,
                'module' => 'votacoes',
                'icon' => 'ki-poll',
                'children' => [
                    ['title' => 'Lista de Votações', 'route' => 'votacoes.index'],
                ]
            ],
            [
                'title' => 'Usuários',
                'route' => null,
                'module' => 'usuarios',
                'icon' => 'ki-user',
                'children' => [
                    ['title' => 'Gestão de Usuários', 'route' => 'usuarios.index'],
                ]
            ],
            [
                'title' => 'Meu Perfil',
                'route' => 'profile.edit',
                'module' => 'profile',
                'icon' => 'ki-profile-circle',
                'children' => []
            ]
        ];
        
        $this->info('📋 MENU LATERAL RENDERIZADO:');
        $this->newLine();
        
        $visibleMenus = 0;
        
        foreach ($menuItems as $item) {
            if ($this->canShowMenuItem($role, $item)) {
                $visibleMenus++;
                $this->line("✅ {$item['title']}");
                
                // Verificar submenus
                if (!empty($item['children'])) {
                    foreach ($item['children'] as $child) {
                        if ($this->canAccessRoute($role, $child['route'])) {
                            $this->line("   └─ ✅ {$child['title']}");
                        } else {
                            $this->line("   └─ ❌ {$child['title']} (sem permissão)");
                        }
                    }
                }
            } else {
                $this->line("❌ {$item['title']} (módulo não acessível)");
            }
        }
        
        $this->newLine();
        $this->info("📊 Total de menus visíveis: {$visibleMenus}/" . count($menuItems));
        
        // Mostrar resumo específico para PARLAMENTAR
        if ($role === 'PARLAMENTAR') {
            $this->newLine();
            $this->warn('🎯 PARLAMENTAR deve ver apenas:');
            $this->line('• Dashboard');
            $this->line('• Proposições (criar, minhas proposições, assinatura)');
            $this->line('• Comissões (lista, minhas comissões)');
            $this->line('• Meu Perfil');
            $this->newLine();
            $this->warn('❌ PARLAMENTAR NÃO deve ver:');
            $this->line('• Parlamentares');
            $this->line('• Partidos');
            $this->line('• Sessões');
            $this->line('• Votações');
            $this->line('• Usuários (Admin)');
        }
        
        return 0;
    }
    
    private function canShowMenuItem($role, $item)
    {
        // Se tem rota específica, verificar a rota
        if ($item['route']) {
            return $this->canAccessRoute($role, $item['route']);
        }
        
        // Se é um módulo, verificar se pode acessar o módulo
        return $this->canAccessModule($role, $item['module']);
    }
    
    private function canAccessRoute($role, $route)
    {
        if (!$route) return false;
        
        return ScreenPermission::where('role_name', $role)
            ->where('screen_route', $route)
            ->where('can_access', true)
            ->exists();
    }
    
    private function canAccessModule($role, $module)
    {
        if (!$module) return false;
        
        return ScreenPermission::where('role_name', $role)
            ->where('screen_module', $module)
            ->where('can_access', true)
            ->exists();
    }
}