<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScreenPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $screens = \App\Models\ScreenPermission::getAvailableScreens();
        $roles = [
            \App\Models\User::PERFIL_ADMIN,
            \App\Models\User::PERFIL_LEGISLATIVO,
            \App\Models\User::PERFIL_PARLAMENTAR,
            \App\Models\User::PERFIL_RELATOR,
            \App\Models\User::PERFIL_PROTOCOLO,
            \App\Models\User::PERFIL_ASSESSOR,
            \App\Models\User::PERFIL_CIDADAO_VERIFICADO,
            \App\Models\User::PERFIL_PUBLICO,
        ];

        foreach ($roles as $role) {
            foreach ($screens as $moduleKey => $module) {
                // Tela principal do módulo
                if (isset($module['route'])) {
                    $canAccess = $this->getDefaultAccess($role, $moduleKey);
                    \App\Models\ScreenPermission::setScreenAccess(
                        $role,
                        $module['route'],
                        $module['name'],
                        $moduleKey,
                        $canAccess
                    );
                }

                // Telas filhas
                if (isset($module['children'])) {
                    foreach ($module['children'] as $screenKey => $screen) {
                        $canAccess = $this->getDefaultAccess($role, $moduleKey, $screen['route']);
                        \App\Models\ScreenPermission::setScreenAccess(
                            $role,
                            $screen['route'],
                            $screen['name'],
                            $moduleKey,
                            $canAccess
                        );
                    }
                }
            }
        }
    }

    /**
     * Determinar acesso padrão baseado no perfil
     */
    private function getDefaultAccess(string $roleName, string $module, string $route = null): bool
    {
        // Admin tem acesso a tudo
        if ($roleName === \App\Models\User::PERFIL_ADMIN) {
            return true;
        }

        // Regras específicas por perfil
        switch ($roleName) {
            case \App\Models\User::PERFIL_LEGISLATIVO:
                // Servidor legislativo tem acesso quase total exceto administração
                return !in_array($module, ['usuarios']) || ($route && str_contains($route, '.edit'));
                
            case \App\Models\User::PERFIL_PARLAMENTAR:
            case \App\Models\User::PERFIL_RELATOR:
                // Parlamentares têm acesso a visualização e criação, mas edição limitada
                return !in_array($module, ['usuarios', 'modelos']) && 
                       (!$route || !str_contains($route, '.edit') || str_contains($route, 'projetos'));
                
            case \App\Models\User::PERFIL_PROTOCOLO:
                // Protocolo tem acesso principalmente a projetos e sessões
                return in_array($module, ['dashboard', 'projetos', 'sessoes', 'parlamentares']);
                
            case \App\Models\User::PERFIL_ASSESSOR:
                // Assessor tem acesso limitado principalmente a consultas
                return in_array($module, ['dashboard', 'parlamentares', 'projetos', 'comissoes']) &&
                       (!$route || str_contains($route, 'index') || $route === 'dashboard');
                
            case \App\Models\User::PERFIL_CIDADAO_VERIFICADO:
            case \App\Models\User::PERFIL_PUBLICO:
                // Acesso apenas a visualizações públicas
                return in_array($module, ['dashboard', 'parlamentares', 'comissoes']) &&
                       (!$route || str_contains($route, 'index') || $route === 'dashboard');
                
            default:
                return false;
        }
    }
}
