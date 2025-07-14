<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ScreenPermission extends Model
{
    protected $fillable = [
        'role_name',
        'screen_route',
        'screen_name',
        'screen_module',
        'can_access',
    ];

    protected $casts = [
        'can_access' => 'boolean',
    ];

    /**
     * Verificar se um perfil tem acesso a uma tela específica
     */
    public static function canAccessScreen(string $roleName, string $screenRoute): bool
    {
        $permission = self::where('role_name', $roleName)
            ->where('screen_route', $screenRoute)
            ->first();

        return $permission ? $permission->can_access : false;
    }

    /**
     * Definir acesso de um perfil a uma tela
     */
    public static function setScreenAccess(string $roleName, string $screenRoute, string $screenName, string $screenModule, bool $canAccess): void
    {
        self::updateOrCreate(
            [
                'role_name' => $roleName,
                'screen_route' => $screenRoute,
            ],
            [
                'screen_name' => $screenName,
                'screen_module' => $screenModule,
                'can_access' => $canAccess,
            ]
        );
    }

    /**
     * Obter todas as telas disponíveis no sistema
     */
    public static function getAvailableScreens(): array
    {
        return [
            'dashboard' => [
                'name' => 'Dashboard',
                'module' => 'dashboard',
                'route' => 'dashboard'
            ],
            'parlamentares' => [
                'name' => 'Parlamentares',
                'module' => 'parlamentares',
                'children' => [
                    'parlamentares.index' => ['name' => 'Lista de Parlamentares', 'route' => 'parlamentares.index'],
                    'parlamentares.create' => ['name' => 'Novo Parlamentar', 'route' => 'parlamentares.create'],
                    'parlamentares.edit' => ['name' => 'Editar Parlamentar', 'route' => 'parlamentares.edit'],
                    'parlamentares.mesa-diretora' => ['name' => 'Mesa Diretora', 'route' => 'parlamentares.mesa-diretora'],
                ]
            ],
            'comissoes' => [
                'name' => 'Comissões',
                'module' => 'comissoes',
                'children' => [
                    'comissoes.index' => ['name' => 'Lista de Comissões', 'route' => 'comissoes.index'],
                    'comissoes.create' => ['name' => 'Nova Comissão', 'route' => 'comissoes.create'],
                    'comissoes.edit' => ['name' => 'Editar Comissão', 'route' => 'comissoes.edit'],
                ]
            ],
            'projetos' => [
                'name' => 'Projetos',
                'module' => 'projetos',
                'children' => [
                    'projetos.index' => ['name' => 'Lista de Projetos', 'route' => 'projetos.index'],
                    'projetos.create' => ['name' => 'Novo Projeto', 'route' => 'projetos.create'],
                    'projetos.edit' => ['name' => 'Editar Projeto', 'route' => 'projetos.edit'],
                ]
            ],
            'sessoes' => [
                'name' => 'Sessões',
                'module' => 'sessoes',
                'children' => [
                    'admin.sessions.index' => ['name' => 'Lista de Sessões', 'route' => 'admin.sessions.index'],
                    'admin.sessions.create' => ['name' => 'Nova Sessão', 'route' => 'admin.sessions.create'],
                    'admin.sessions.edit' => ['name' => 'Editar Sessão', 'route' => 'admin.sessions.edit'],
                ]
            ],
            'usuarios' => [
                'name' => 'Usuários',
                'module' => 'usuarios',
                'children' => [
                    'usuarios.index' => ['name' => 'Gestão de Usuários', 'route' => 'usuarios.index'],
                    'usuarios.create' => ['name' => 'Novo Usuário', 'route' => 'usuarios.create'],
                    'usuarios.edit' => ['name' => 'Editar Usuário', 'route' => 'usuarios.edit'],
                ]
            ],
            'modelos' => [
                'name' => 'Modelos de Projeto',
                'module' => 'modelos',
                'children' => [
                    'modelos.index' => ['name' => 'Lista de Modelos', 'route' => 'modelos.index'],
                    'modelos.create' => ['name' => 'Novo Modelo', 'route' => 'modelos.create'],
                    'modelos.edit' => ['name' => 'Editar Modelo', 'route' => 'modelos.edit'],
                ]
            ],
        ];
    }

    /**
     * Obter permissões por perfil
     */
    public static function getPermissionsByRole(string $roleName): array
    {
        $permissions = self::where('role_name', $roleName)->get();
        $result = [];

        foreach ($permissions as $permission) {
            $result[$permission->screen_route] = [
                'can_access' => $permission->can_access,
                'screen_name' => $permission->screen_name,
                'screen_module' => $permission->screen_module,
            ];
        }

        return $result;
    }

    /**
     * Verificar se o usuário atual pode acessar uma rota específica
     */
    public static function userCanAccessRoute(string $route): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Admin sempre tem acesso total
        if ($user->isAdmin()) {
            return true;
        }

        $roleName = $user->getRoleNames()->first() ?? 'PUBLICO';
        
        // Verificar na tabela de permissões
        $permission = self::where('role_name', $roleName)
            ->where('screen_route', $route)
            ->first();

        if ($permission) {
            return $permission->can_access;
        }

        // Fallback: aplicar regras padrão baseadas no perfil
        return self::getDefaultAccessByRole($roleName, $route);
    }

    /**
     * Obter acesso padrão por perfil (fallback quando não existe permissão específica)
     */
    private static function getDefaultAccessByRole(string $roleName, string $route): bool
    {
        // Regras padrão baseadas no middleware CheckScreenPermission
        switch ($roleName) {
            case 'ADMIN':
                return true;
            
            case 'LEGISLATIVO':
                return true; // Acesso total
            
            case 'PARLAMENTAR':
                $parlamentarRoutes = [
                    'dashboard',
                    'parlamentares.index',
                    'parlamentares.mesa-diretora',
                    'parlamentares.create',
                    'projetos.index',
                    'projetos.create',
                    'admin.sessions.index',
                    'admin.sessions.create',
                    'comissoes.index',
                ];
                return in_array($route, $parlamentarRoutes);
            
            case 'RELATOR':
                $relatorRoutes = [
                    'dashboard',
                    'parlamentares.index',
                    'parlamentares.mesa-diretora',
                    'projetos.index',
                    'admin.sessions.index',
                    'comissoes.index',
                ];
                return in_array($route, $relatorRoutes);
            
            case 'PROTOCOLO':
                $protocoloRoutes = [
                    'dashboard',
                    'projetos.index',
                    'projetos.create',
                    'admin.sessions.index',
                ];
                return in_array($route, $protocoloRoutes);
            
            case 'ASSESSOR':
                $assessorRoutes = [
                    'dashboard',
                    'parlamentares.index',
                    'projetos.index',
                    'admin.sessions.index',
                ];
                return in_array($route, $assessorRoutes);
            
            case 'CIDADAO_VERIFICADO':
            case 'PUBLICO':
                $publicRoutes = [
                    'dashboard',
                    'parlamentares.index',
                    'admin.sessions.index',
                ];
                return in_array($route, $publicRoutes);
            
            default:
                return false;
        }
    }

    /**
     * Verificar se o usuário pode acessar qualquer rota de um módulo
     */
    public static function userCanAccessModule(string $module): bool
    {
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Admin sempre tem acesso total
        if ($user->isAdmin()) {
            return true;
        }

        $roleName = $user->getRoleNames()->first() ?? 'PUBLICO';
        
        // Verificar se tem acesso a qualquer rota do módulo
        $hasAccess = self::where('role_name', $roleName)
            ->where('screen_module', $module)
            ->where('can_access', true)
            ->exists();

        if ($hasAccess) {
            return true;
        }

        // Fallback: verificar rotas principais do módulo
        $moduleRoutes = self::getModuleMainRoutes($module);
        foreach ($moduleRoutes as $route) {
            if (self::userCanAccessRoute($route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obter rotas principais de cada módulo
     */
    private static function getModuleMainRoutes(string $module): array
    {
        $routes = [
            'dashboard' => ['dashboard'],
            'parlamentares' => ['parlamentares.index', 'parlamentares.mesa-diretora'],
            'comissoes' => ['comissoes.index'],
            'projetos' => ['projetos.index'],
            'sessoes' => ['admin.sessions.index'],
            'usuarios' => ['usuarios.index', 'admin.usuarios.index'],
            'modelos' => ['modelos.index'],
        ];

        return $routes[$module] ?? [];
    }

}
