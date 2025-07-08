<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SimpleRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        DB::table('roles')->truncate();
        DB::table('permissions')->truncate();

        // Create permissions
        $permissions = [
            // Módulo Parlamentares
            ['name' => 'parlamentares.view', 'guard_name' => 'web'],
            ['name' => 'parlamentares.create', 'guard_name' => 'web'],
            ['name' => 'parlamentares.edit', 'guard_name' => 'web'],
            ['name' => 'parlamentares.delete', 'guard_name' => 'web'],
            ['name' => 'parlamentares.manage_all', 'guard_name' => 'web'],
            
            // Módulo Projetos
            ['name' => 'projetos.view', 'guard_name' => 'web'],
            ['name' => 'projetos.create', 'guard_name' => 'web'],
            ['name' => 'projetos.edit', 'guard_name' => 'web'],
            ['name' => 'projetos.delete', 'guard_name' => 'web'],
            ['name' => 'projetos.tramitar', 'guard_name' => 'web'],
            ['name' => 'projetos.relatar', 'guard_name' => 'web'],
            
            // Módulo Sessões
            ['name' => 'sessoes.view', 'guard_name' => 'web'],
            ['name' => 'sessoes.create', 'guard_name' => 'web'],
            ['name' => 'sessoes.edit', 'guard_name' => 'web'],
            ['name' => 'sessoes.delete', 'guard_name' => 'web'],
            ['name' => 'sessoes.controlar', 'guard_name' => 'web'],
            ['name' => 'sessoes.presenca', 'guard_name' => 'web'],
            
            // Módulo Votações
            ['name' => 'votacoes.view', 'guard_name' => 'web'],
            ['name' => 'votacoes.votar', 'guard_name' => 'web'],
            ['name' => 'votacoes.gerenciar', 'guard_name' => 'web'],
            
            // Módulo Usuários
            ['name' => 'usuarios.view', 'guard_name' => 'web'],
            ['name' => 'usuarios.create', 'guard_name' => 'web'],
            ['name' => 'usuarios.edit', 'guard_name' => 'web'],
            ['name' => 'usuarios.delete', 'guard_name' => 'web'],
            ['name' => 'usuarios.manage_permissions', 'guard_name' => 'web'],
            
            // Sistema Geral
            ['name' => 'sistema.admin', 'guard_name' => 'web'],
            ['name' => 'sistema.dashboard', 'guard_name' => 'web'],
            ['name' => 'sistema.relatorios', 'guard_name' => 'web'],
            ['name' => 'sistema.configuracoes', 'guard_name' => 'web'],
            
            // Mesa Diretora
            ['name' => 'mesa.view', 'guard_name' => 'web'],
            ['name' => 'mesa.gerenciar', 'guard_name' => 'web'],
            
            // Comissões
            ['name' => 'comissoes.view', 'guard_name' => 'web'],
            ['name' => 'comissoes.participar', 'guard_name' => 'web'],
            ['name' => 'comissoes.presidir', 'guard_name' => 'web'],
            ['name' => 'comissoes.relatar', 'guard_name' => 'web'],
            
            // Transparência
            ['name' => 'transparencia.view_publico', 'guard_name' => 'web'],
            ['name' => 'transparencia.view_completo', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Create roles
        $roles = [
            ['name' => User::PERFIL_PUBLICO, 'guard_name' => 'web'],
            ['name' => User::PERFIL_CIDADAO_VERIFICADO, 'guard_name' => 'web'],
            ['name' => User::PERFIL_ASSESSOR, 'guard_name' => 'web'],
            ['name' => User::PERFIL_RELATOR, 'guard_name' => 'web'],
            ['name' => User::PERFIL_PARLAMENTAR, 'guard_name' => 'web'],
            ['name' => User::PERFIL_LEGISLATIVO, 'guard_name' => 'web'],
            ['name' => User::PERFIL_ADMIN, 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        // Get role and permission IDs
        $roleIds = DB::table('roles')->pluck('id', 'name');
        $permissionIds = DB::table('permissions')->pluck('id', 'name');

        // Define role permissions
        $rolePermissions = [
            User::PERFIL_PUBLICO => [
                'transparencia.view_publico',
                'parlamentares.view',
                'projetos.view',
                'sessoes.view',
                'votacoes.view',
            ],
            User::PERFIL_CIDADAO_VERIFICADO => [
                'transparencia.view_publico',
                'transparencia.view_completo',
                'parlamentares.view',
                'projetos.view',
                'sessoes.view',
                'votacoes.view',
                'sistema.dashboard',
            ],
            User::PERFIL_ASSESSOR => [
                'transparencia.view_publico',
                'transparencia.view_completo',
                'parlamentares.view',
                'projetos.view',
                'projetos.create',
                'projetos.edit',
                'sessoes.view',
                'sessoes.presenca',
                'votacoes.view',
                'sistema.dashboard',
                'comissoes.view',
                'comissoes.participar',
            ],
            User::PERFIL_RELATOR => [
                'transparencia.view_publico',
                'transparencia.view_completo',
                'parlamentares.view',
                'parlamentares.edit',
                'projetos.view',
                'projetos.create',
                'projetos.edit',
                'projetos.relatar',
                'projetos.tramitar',
                'sessoes.view',
                'sessoes.presenca',
                'votacoes.view',
                'votacoes.votar',
                'sistema.dashboard',
                'sistema.relatorios',
                'comissoes.view',
                'comissoes.participar',
                'comissoes.relatar',
                'mesa.view',
            ],
            User::PERFIL_PARLAMENTAR => [
                'transparencia.view_publico',
                'transparencia.view_completo',
                'parlamentares.view',
                'parlamentares.edit',
                'projetos.view',
                'projetos.create',
                'projetos.edit',
                'projetos.tramitar',
                'sessoes.view',
                'sessoes.presenca',
                'votacoes.view',
                'votacoes.votar',
                'sistema.dashboard',
                'sistema.relatorios',
                'comissoes.view',
                'comissoes.participar',
                'comissoes.presidir',
                'mesa.view',
            ],
            User::PERFIL_LEGISLATIVO => [
                'transparencia.view_publico',
                'transparencia.view_completo',
                'parlamentares.view',
                'parlamentares.create',
                'parlamentares.edit',
                'projetos.view',
                'projetos.create',
                'projetos.edit',
                'projetos.tramitar',
                'sessoes.view',
                'sessoes.create',
                'sessoes.edit',
                'sessoes.controlar',
                'sessoes.presenca',
                'votacoes.view',
                'votacoes.gerenciar',
                'sistema.dashboard',
                'sistema.relatorios',
                'sistema.configuracoes',
                'usuarios.view',
                'usuarios.create',
                'usuarios.edit',
                'comissoes.view',
                'comissoes.participar',
                'mesa.view',
                'mesa.gerenciar',
            ],
            User::PERFIL_ADMIN => array_keys($permissionIds->toArray()), // All permissions
        ];

        // Assign permissions to roles
        foreach ($rolePermissions as $roleName => $permissions) {
            $roleId = $roleIds[$roleName];
            foreach ($permissions as $permissionName) {
                if (isset($permissionIds[$permissionName])) {
                    DB::table('role_has_permissions')->insert([
                        'role_id' => $roleId,
                        'permission_id' => $permissionIds[$permissionName],
                    ]);
                }
            }
        }

        // Create default users
        $users = [
            [
                'name' => 'Administrador do Sistema',
                'email' => 'admin@sistema.gov.br',
                'password' => Hash::make('admin123'),
                'documento' => '000.000.000-00',
                'telefone' => '(11) 0000-0000',
                'profissao' => 'Administrador de Sistema',
                'cargo_atual' => 'Administrador',
                'ativo' => true,
                'role' => User::PERFIL_ADMIN
            ],
            [
                'name' => 'João Silva Santos',
                'email' => 'parlamentar@camara.gov.br',
                'password' => Hash::make('parlamentar123'),
                'documento' => '111.111.111-11',
                'telefone' => '(11) 1111-1111',
                'data_nascimento' => '1975-03-15',
                'profissao' => 'Advogado',
                'cargo_atual' => 'Vereador',
                'partido' => 'PT',
                'ativo' => true,
                'role' => User::PERFIL_PARLAMENTAR
            ],
            [
                'name' => 'Maria Técnica Legal',
                'email' => 'servidor@camara.gov.br',
                'password' => Hash::make('servidor123'),
                'documento' => '222.222.222-22',
                'telefone' => '(11) 2222-2222',
                'data_nascimento' => '1985-08-20',
                'profissao' => 'Analista Legislativo',
                'cargo_atual' => 'Servidor Técnico',
                'ativo' => true,
                'role' => User::PERFIL_LEGISLATIVO
            ]
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            $user = User::create(array_merge($userData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));

            // Assign role to user
            $roleId = $roleIds[$role];
            DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
            ]);
        }

        $this->command->info('Perfis e permissões criados com sucesso!');
        $this->command->info('Usuários criados:');
        $this->command->info('- Admin: admin@sistema.gov.br / admin123');
        $this->command->info('- Parlamentar: parlamentar@camara.gov.br / parlamentar123');
        $this->command->info('- Servidor: servidor@camara.gov.br / servidor123');
    }
}