<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
