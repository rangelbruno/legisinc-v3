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
            'proposicoes' => [
                'name' => 'Proposições',
                'module' => 'proposicoes',
                'children' => [
                    'proposicoes.criar' => ['name' => 'Criar Proposição', 'route' => 'proposicoes.criar'],
                    'proposicoes.minhas-proposicoes' => ['name' => 'Minhas Proposições', 'route' => 'proposicoes.minhas-proposicoes'],
                    'proposicoes.assinatura' => ['name' => 'Assinatura', 'route' => 'proposicoes.assinatura'],
                    'proposicoes.historico-assinaturas' => ['name' => 'Histórico de Assinaturas', 'route' => 'proposicoes.historico-assinaturas'],
                    'proposicoes.revisar' => ['name' => 'Revisar Proposições', 'route' => 'proposicoes.revisar'],
                    'proposicoes.relatorio-legislativo' => ['name' => 'Relatório Legislativo', 'route' => 'proposicoes.relatorio-legislativo'],
                    'proposicoes.aguardando-protocolo' => ['name' => 'Aguardando Protocolo', 'route' => 'proposicoes.aguardando-protocolo'],
                    'proposicoes.protocolar' => ['name' => 'Protocolar', 'route' => 'proposicoes.protocolar'],
                    'proposicoes.protocolos-hoje' => ['name' => 'Protocolos Hoje', 'route' => 'proposicoes.protocolos-hoje'],
                    'proposicoes.estatisticas-protocolo' => ['name' => 'Estatísticas Protocolo', 'route' => 'proposicoes.estatisticas-protocolo'],
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
        
        // Admin sempre tem acesso total a todas as telas
        if ($user->isAdmin()) {
            return true;
        }

        $roleName = $user->getRoleNames()->first() ?? 'PUBLICO';
        
        // Admin via role name também tem acesso total
        if ($roleName === 'ADMIN') {
            return true;
        }
        
        // Verificar se existe configuração de permissões para este perfil
        if (self::hasConfiguredPermissions($roleName)) {
            // Se há permissões configuradas, só permitir o que foi explicitamente liberado
            $permission = self::where('role_name', $roleName)
                ->where('screen_route', $route)
                ->first();

            return $permission ? $permission->can_access : false;
        }

        // Se não há permissões configuradas, permitir apenas dashboard para não deixar usuário sem acesso
        if ($route === 'dashboard.index') {
            return true;
        }
        
        // Outras rotas requerem configuração explícita pelo administrador
        return false;
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
                    'admin.sessions.index',
                    'comissoes.index',
                ];
                return in_array($route, $relatorRoutes);
            
            case 'PROTOCOLO':
                $protocoloRoutes = [
                    'dashboard',
                    'admin.sessions.index',
                ];
                return in_array($route, $protocoloRoutes);
            
            case 'ASSESSOR':
                $assessorRoutes = [
                    'dashboard',
                    'parlamentares.index',
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
        
        // Admin sempre tem acesso total a todos os módulos
        if ($user->isAdmin()) {
            return true;
        }

        $roleName = $user->getRoleNames()->first() ?? 'PUBLICO';
        
        // Admin via role name também tem acesso total
        if ($roleName === 'ADMIN') {
            return true;
        }
        
        // Verificar se existem permissões configuradas para este role
        if (self::hasConfiguredPermissions($roleName)) {
            // Se há permissões configuradas, só permitir módulos com pelo menos uma rota liberada
            $hasAccess = self::where('role_name', $roleName)
                ->where('screen_module', $module)
                ->where('can_access', true)
                ->exists();

            return $hasAccess;
        }

        // Se não há permissões configuradas, permitir apenas dashboard para não deixar usuário sem acesso
        if ($module === 'dashboard') {
            return true;
        }
        
        // Outros módulos requerem configuração explícita pelo administrador
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
            'sessoes' => ['admin.sessions.index'],
            'usuarios' => ['usuarios.index', 'admin.usuarios.index'],
        ];

        return $routes[$module] ?? [];
    }

    /**
     * Aplicar permissões configuradas para um perfil específico
     */
    public static function applyRolePermissions(string $roleName, array $permissions): void
    {
        // Primeiro, remover todas as permissões existentes do role
        self::where('role_name', $roleName)->delete();

        // Aplicar as novas permissões
        foreach ($permissions as $permission) {
            self::setScreenAccess(
                $roleName,
                $permission['screen_route'],
                $permission['screen_name'] ?? '',
                $permission['screen_module'] ?? '',
                $permission['can_access'] ?? false
            );
        }
    }

    /**
     * Obter todas as permissões configuradas para um perfil
     */
    public static function getRolePermissions(string $roleName): array
    {
        return self::where('role_name', $roleName)
            ->get()
            ->keyBy('screen_route')
            ->toArray();
    }

    /**
     * Verificar se existem permissões configuradas para um perfil
     */
    public static function hasConfiguredPermissions(string $roleName): bool
    {
        return self::where('role_name', $roleName)->exists();
    }

}
