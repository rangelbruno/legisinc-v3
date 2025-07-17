<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;
use App\Enums\UserRole;

class ParametroPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permissões para o sistema de parâmetros
        $permissions = [
            [
                'screen_name' => 'Parâmetros - Listagem',
                'screen_route' => 'admin.parametros.index',
                'screen_module' => 'parametros',
                'description' => 'Visualizar lista de parâmetros do sistema',
                'permissions' => [
                    'ADMIN' => true,
                    'LEGISLATIVO' => false,
                    'PARLAMENTAR' => false,
                    'RELATOR' => false,
                    'PROTOCOLO' => false,
                    'ASSESSOR' => false,
                    'CIDADAO_VERIFICADO' => false,
                    'PUBLICO' => false,
                ]
            ],
            [
                'screen_name' => 'Parâmetros - Criar',
                'screen_route' => 'admin.parametros.create',
                'screen_module' => 'parametros',
                'description' => 'Criar novos parâmetros do sistema',
                'permissions' => [
                    'ADMIN' => true,
                    'LEGISLATIVO' => false,
                    'PARLAMENTAR' => false,
                    'RELATOR' => false,
                    'PROTOCOLO' => false,
                    'ASSESSOR' => false,
                    'CIDADAO_VERIFICADO' => false,
                    'PUBLICO' => false,
                ]
            ],
            [
                'screen_name' => 'Parâmetros - Visualizar',
                'screen_route' => 'admin.parametros.show',
                'screen_module' => 'parametros',
                'description' => 'Visualizar detalhes de um parâmetro',
                'permissions' => [
                    'ADMIN' => true,
                    'LEGISLATIVO' => false,
                    'PARLAMENTAR' => false,
                    'RELATOR' => false,
                    'PROTOCOLO' => false,
                    'ASSESSOR' => false,
                    'CIDADAO_VERIFICADO' => false,
                    'PUBLICO' => false,
                ]
            ],
            [
                'screen_name' => 'Parâmetros - Editar',
                'screen_route' => 'admin.parametros.edit',
                'screen_module' => 'parametros',
                'description' => 'Editar parâmetros do sistema',
                'permissions' => [
                    'ADMIN' => true,
                    'LEGISLATIVO' => false,
                    'PARLAMENTAR' => false,
                    'RELATOR' => false,
                    'PROTOCOLO' => false,
                    'ASSESSOR' => false,
                    'CIDADAO_VERIFICADO' => false,
                    'PUBLICO' => false,
                ]
            ]
        ];

        // Criar permissões Spatie para parâmetros
        $spatiePermissions = [
            'parametros.view' => 'Visualizar parâmetros',
            'parametros.create' => 'Criar parâmetros',
            'parametros.edit' => 'Editar parâmetros',
            'parametros.delete' => 'Excluir parâmetros',
            'parametros.export' => 'Exportar parâmetros',
            'parametros.import' => 'Importar parâmetros',
            'parametros.cache' => 'Gerenciar cache de parâmetros',
        ];

        // Criar permissões do Spatie se não existirem
        foreach ($spatiePermissions as $permission => $description) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Atribuir permissões ao role ADMIN
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate([
            'name' => 'ADMIN',
            'guard_name' => 'web'
        ]);

        $adminRole->givePermissionTo(array_keys($spatiePermissions));

        // Criar permissões de tela
        foreach ($permissions as $permissionData) {
            foreach ($permissionData['permissions'] as $role => $canAccess) {
                ScreenPermission::updateOrCreate([
                    'role_name' => $role,
                    'screen_route' => $permissionData['screen_route'],
                ], [
                    'screen_name' => $permissionData['screen_name'],
                    'screen_module' => $permissionData['screen_module'],
                    'can_access' => $canAccess,
                    'can_create' => $canAccess && str_contains($permissionData['screen_route'], 'create'),
                    'can_edit' => $canAccess && str_contains($permissionData['screen_route'], 'edit'),
                    'can_delete' => $canAccess && $role === 'ADMIN',
                ]);
            }
        }

        $this->command->info('Permissões de parâmetros criadas com sucesso!');
    }
}