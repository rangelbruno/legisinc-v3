<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Criar permissões por módulo
        $permissions = [
            // Módulo Parlamentares
            'parlamentares.view',
            'parlamentares.create',
            'parlamentares.edit',
            'parlamentares.delete',
            'parlamentares.manage_all',
            
            // Módulo Projetos (futuro)
            'projetos.view',
            'projetos.create',
            'projetos.edit',
            'projetos.delete',
            'projetos.tramitar',
            'projetos.relatar',
            
            // Módulo Sessões (futuro)
            'sessoes.view',
            'sessoes.create',
            'sessoes.edit',
            'sessoes.delete',
            'sessoes.controlar',
            'sessoes.presenca',
            
            // Módulo Votações (futuro)
            'votacoes.view',
            'votacoes.votar',
            'votacoes.gerenciar',
            
            // Módulo Usuários
            'usuarios.view',
            'usuarios.create',
            'usuarios.edit',
            'usuarios.delete',
            'usuarios.manage_permissions',
            
            // Sistema Geral
            'sistema.admin',
            'sistema.dashboard',
            'sistema.relatorios',
            'sistema.configuracoes',
            
            // Mesa Diretora
            'mesa.view',
            'mesa.gerenciar',
            
            // Comissões
            'comissoes.view',
            'comissoes.participar',
            'comissoes.presidir',
            'comissoes.relatar',
            
            // Transparência
            'transparencia.view_publico',
            'transparencia.view_completo',
        ];

        // Criar todas as permissões
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar os 7 perfis do sistema parlamentar
        $roles = [
            User::PERFIL_PUBLICO => [
                'name' => User::PERFIL_PUBLICO,
                'description' => 'Acesso público limitado',
                'permissions' => [
                    'transparencia.view_publico',
                    'parlamentares.view',
                    'projetos.view',
                    'sessoes.view',
                    'votacoes.view',
                ]
            ],
            
            User::PERFIL_CIDADAO_VERIFICADO => [
                'name' => User::PERFIL_CIDADAO_VERIFICADO,
                'description' => 'Cidadãos com identidade verificada',
                'permissions' => [
                    'transparencia.view_publico',
                    'transparencia.view_completo',
                    'parlamentares.view',
                    'projetos.view',
                    'sessoes.view',
                    'votacoes.view',
                    'sistema.dashboard',
                ]
            ],
            
            User::PERFIL_ASSESSOR => [
                'name' => User::PERFIL_ASSESSOR,
                'description' => 'Assessores parlamentares',
                'permissions' => [
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
                ]
            ],
            
            User::PERFIL_RELATOR => [
                'name' => User::PERFIL_RELATOR,
                'description' => 'Parlamentares com relatoria ativa',
                'permissions' => [
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
                ]
            ],
            
            User::PERFIL_PARLAMENTAR => [
                'name' => User::PERFIL_PARLAMENTAR,
                'description' => 'Vereadores e membros da mesa diretora',
                'permissions' => [
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
                ]
            ],
            
            User::PERFIL_LEGISLATIVO => [
                'name' => User::PERFIL_LEGISLATIVO,
                'description' => 'Servidores técnicos da casa legislativa',
                'permissions' => [
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
                ]
            ],
            
            User::PERFIL_ADMIN => [
                'name' => User::PERFIL_ADMIN,
                'description' => 'Acesso total ao sistema',
                'permissions' => 'all' // Todas as permissões
            ],
        ];

        // Criar roles e atribuir permissões
        foreach ($roles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'guard_name' => 'web'
            ]);

            if ($roleData['permissions'] === 'all') {
                // Admin tem todas as permissões
                $role->givePermissionTo(Permission::all());
            } else {
                // Outros roles têm permissões específicas
                $role->givePermissionTo($roleData['permissions']);
            }
        }

        // Criar usuário admin padrão
        $admin = User::firstOrCreate(
            ['email' => 'admin@sistema.gov.br'],
            [
                'name' => 'Administrador do Sistema',
                'password' => bcrypt('admin123'),
                'documento' => '000.000.000-00',
                'telefone' => '(11) 0000-0000',
                'profissao' => 'Administrador de Sistema',
                'cargo_atual' => 'Administrador',
                'ativo' => true,
            ]
        );
        
        $admin->assignRole(User::PERFIL_ADMIN);

        // Criar usuário parlamentar de exemplo
        $parlamentar = User::firstOrCreate(
            ['email' => 'parlamentar@camara.gov.br'],
            [
                'name' => 'João Silva Santos',
                'password' => bcrypt('parlamentar123'),
                'documento' => '111.111.111-11',
                'telefone' => '(11) 1111-1111',
                'data_nascimento' => '1975-03-15',
                'profissao' => 'Advogado',
                'cargo_atual' => 'Vereador',
                'partido' => 'PT',
                'ativo' => true,
            ]
        );
        
        $parlamentar->assignRole(User::PERFIL_PARLAMENTAR);

        // Criar usuário legislativo de exemplo
        $legislativo = User::firstOrCreate(
            ['email' => 'servidor@camara.gov.br'],
            [
                'name' => 'Maria Técnica Legal',
                'password' => bcrypt('servidor123'),
                'documento' => '222.222.222-22',
                'telefone' => '(11) 2222-2222',
                'data_nascimento' => '1985-08-20',
                'profissao' => 'Analista Legislativo',
                'cargo_atual' => 'Servidor Técnico',
                'ativo' => true,
            ]
        );
        
        $legislativo->assignRole(User::PERFIL_LEGISLATIVO);

        $this->command->info('Perfis e permissões criados com sucesso!');
        $this->command->info('Usuários criados:');
        $this->command->info('- Admin: admin@sistema.gov.br / admin123');
        $this->command->info('- Parlamentar: parlamentar@camara.gov.br / parlamentar123');
        $this->command->info('- Servidor: servidor@camara.gov.br / servidor123');
    }
}