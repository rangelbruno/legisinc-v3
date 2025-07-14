<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ScreenPermission;
use App\Models\User;
use Illuminate\Http\Request;

class ScreenPermissionController extends Controller
{
    public function __construct()
    {
        // Middleware removido - será aplicado nas rotas
    }

    /**
     * Exibir tela principal de atribuição de permissões
     */
    public function index()
    {
        // Verificar se é admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
        }

        $roles = $this->getAvailableRoles();
        $screens = ScreenPermission::getAvailableScreens();
        $selectedRole = request('role', User::PERFIL_PARLAMENTAR);
        $currentPermissions = ScreenPermission::getPermissionsByRole($selectedRole);
        
        return view('admin.screen-permissions.index', compact(
            'roles',
            'screens', 
            'selectedRole',
            'currentPermissions'
        ));
    }

    /**
     * Atualizar permissões de um perfil
     */
    public function update(Request $request)
    {
        // Verificar se é admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
        }

        $request->validate([
            'role_name' => 'required|string',
            'permissions' => 'array',
            'permissions.*' => 'boolean'
        ]);

        $roleName = $request->role_name;
        $permissions = $request->permissions ?? [];
        $screens = ScreenPermission::getAvailableScreens();

        // Processar todas as telas disponíveis
        foreach ($screens as $moduleKey => $module) {
            // Se for um módulo com tela principal
            if (isset($module['route'])) {
                $hasAccess = isset($permissions[$module['route']]) && $permissions[$module['route']];
                ScreenPermission::setScreenAccess(
                    $roleName,
                    $module['route'],
                    $module['name'],
                    $moduleKey,
                    $hasAccess
                );
            }

            // Processar telas filhas
            if (isset($module['children'])) {
                foreach ($module['children'] as $screenKey => $screen) {
                    $hasAccess = isset($permissions[$screen['route']]) && $permissions[$screen['route']];
                    ScreenPermission::setScreenAccess(
                        $roleName,
                        $screen['route'],
                        $screen['name'],
                        $moduleKey,
                        $hasAccess
                    );
                }
            }
        }

        return redirect()
            ->route('admin.screen-permissions.index', ['role' => $roleName])
            ->with('success', 'Permissões atualizadas com sucesso!');
    }

    /**
     * Obter permissões via AJAX
     */
    public function getPermissions(Request $request)
    {
        // Verificar se é admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Acesso negado'], 403);
        }

        $roleName = $request->get('role');
        $permissions = ScreenPermission::getPermissionsByRole($roleName);
        
        return response()->json([
            'success' => true,
            'permissions' => $permissions
        ]);
    }

    /**
     * Resetar permissões de um perfil para o padrão
     */
    public function reset(Request $request)
    {
        // Verificar se é admin
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            abort(403, 'Acesso negado. Apenas administradores podem acessar esta funcionalidade.');
        }

        $request->validate([
            'role_name' => 'required|string'
        ]);

        $roleName = $request->role_name;
        
        // Remover todas as permissões existentes do perfil
        ScreenPermission::where('role_name', $roleName)->delete();
        
        // Aplicar permissões padrão baseadas no perfil
        $this->applyDefaultPermissions($roleName);

        return redirect()
            ->route('admin.screen-permissions.index', ['role' => $roleName])
            ->with('success', 'Permissões resetadas para o padrão!');
    }

    /**
     * Obter perfis disponíveis no sistema
     */
    private function getAvailableRoles(): array
    {
        return [
            User::PERFIL_ADMIN => 'Administrador',
            User::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            User::PERFIL_PARLAMENTAR => 'Parlamentar',
            User::PERFIL_RELATOR => 'Relator',
            User::PERFIL_PROTOCOLO => 'Protocolo',
            User::PERFIL_ASSESSOR => 'Assessor',
            User::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            User::PERFIL_PUBLICO => 'Público',
        ];
    }

    /**
     * Aplicar permissões padrão baseadas no perfil
     */
    private function applyDefaultPermissions(string $roleName): void
    {
        $screens = ScreenPermission::getAvailableScreens();
        
        foreach ($screens as $moduleKey => $module) {
            // Determinar acesso padrão baseado no perfil
            $defaultAccess = $this->getDefaultAccess($roleName, $moduleKey);
            
            // Se for um módulo com tela principal
            if (isset($module['route'])) {
                ScreenPermission::setScreenAccess(
                    $roleName,
                    $module['route'],
                    $module['name'],
                    $moduleKey,
                    $defaultAccess
                );
            }

            // Processar telas filhas
            if (isset($module['children'])) {
                foreach ($module['children'] as $screenKey => $screen) {
                    $childAccess = $this->getDefaultAccess($roleName, $moduleKey, $screen['route']);
                    ScreenPermission::setScreenAccess(
                        $roleName,
                        $screen['route'],
                        $screen['name'],
                        $moduleKey,
                        $childAccess
                    );
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
        if ($roleName === User::PERFIL_ADMIN) {
            return true;
        }

        // Regras específicas por perfil
        switch ($roleName) {
            case User::PERFIL_LEGISLATIVO:
                // Servidor legislativo tem acesso quase total exceto administração
                return !in_array($module, ['usuarios']) || ($route && str_contains($route, '.edit'));
                
            case User::PERFIL_PARLAMENTAR:
            case User::PERFIL_RELATOR:
                // Parlamentares têm acesso a visualização e criação, mas edição limitada
                return !in_array($module, ['usuarios', 'modelos']) && 
                       (!$route || !str_contains($route, '.edit') || str_contains($route, 'projetos'));
                
            case User::PERFIL_PROTOCOLO:
                // Protocolo tem acesso principalmente a projetos e sessões
                return in_array($module, ['dashboard', 'projetos', 'sessoes', 'parlamentares']);
                
            case User::PERFIL_ASSESSOR:
                // Assessor tem acesso limitado principalmente a consultas
                return in_array($module, ['dashboard', 'parlamentares', 'projetos', 'comissoes']) &&
                       (!$route || str_contains($route, 'index') || $route === 'dashboard');
                
            case User::PERFIL_CIDADAO_VERIFICADO:
            case User::PERFIL_PUBLICO:
                // Acesso apenas a visualizações públicas
                return in_array($module, ['dashboard', 'parlamentares', 'comissoes']) &&
                       (!$route || str_contains($route, 'index') || $route === 'dashboard');
                
            default:
                return false;
        }
    }
}
