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
            
            // Módulo Projetos - Sistema de Tramitação
            'projeto.create',
            'projeto.edit_own',
            'projeto.sign',
            'projeto.view_own',
            'projeto.view_all',
            'projeto.analyze',
            'projeto.approve',
            'projeto.reject',
            'projeto.view_approved',
            'projeto.assign_number',
            'projeto.include_session',
            'tramitacao.manage',
            
            // Módulo Sessões
            'sessions.view',
            'sessions.create',
            'sessions.edit',
            'sessions.delete',
            'sessions.export',
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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar os perfis do sistema parlamentar incluindo tramitação
        $roles = [
            
            User::PERFIL_PROTOCOLO => [
                'name' => User::PERFIL_PROTOCOLO,
                'description' => 'Atribui números de protocolo aos documentos assinados',
                'permissions' => [
                    'projeto.view_approved',
                    'projeto.assign_number',
                    'projeto.include_session',
                    'sessions.view',
                    'sessoes.view',
                    'sistema.dashboard',
                ]
            ],
            
            User::PERFIL_PUBLICO => [
                'name' => User::PERFIL_PUBLICO,
                'description' => 'Acesso público limitado',
                'permissions' => [
                    'transparencia.view_publico',
                    'parlamentares.view',
                    'projeto.view_all',
                    'sessions.view',
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
                    'projeto.view_all',
                    'sessions.view',
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
                    'projeto.view_all',
                    'projeto.create',
                    'projeto.edit_own',
                    'sessions.view',
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
                    'projeto.view_all',
                    'projeto.create',
                    'projeto.edit_own',
                    'projeto.analyze',
                    'tramitacao.manage',
                    'sessions.view',
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
                    'projeto.view_all',
                    'projeto.create',
                    'projeto.edit_own',
                    'projeto.sign',
                    'tramitacao.manage',
                    'sessions.view',
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
                    'projeto.view_all',
                    'projeto.analyze',
                    'projeto.approve',
                    'projeto.reject',
                    'tramitacao.manage',
                    'sessions.view',
                    'sessions.create',
                    'sessions.edit',
                    'sessions.export',
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
            
            User::PERFIL_EXPEDIENTE => [
                'name' => User::PERFIL_EXPEDIENTE,
                'description' => 'Responsável pelo expediente e tramitação de documentos',
                'permissions' => [
                    'projeto.view_all',
                    'projeto.assign_number',
                    'projeto.include_session',
                    'tramitacao.manage',
                    'parlamentares.view',
                    'sessions.view',
                    'sessoes.view',
                    'sistema.dashboard',
                    'sistema.relatorios',
                ]
            ],
            
            User::PERFIL_ASSESSOR_JURIDICO => [
                'name' => User::PERFIL_ASSESSOR_JURIDICO,
                'description' => 'Assessor jurídico da casa legislativa',
                'permissions' => [
                    'projeto.view_all',
                    'projeto.analyze',
                    'projeto.create',
                    'projeto.edit_own',
                    'tramitacao.manage',
                    'parlamentares.view',
                    'sessions.view',
                    'sessoes.view',
                    'sistema.dashboard',
                    'sistema.relatorios',
                    'sistema.configuracoes',
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
            $role = Role::firstOrCreate([
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

        // Criar usuário parlamentar Jessica Santos
        $parlamentar = User::firstOrCreate(
            ['email' => 'jessica@sistema.gov.br'],
            [
                'name' => 'Jessica Santos',
                'password' => bcrypt('123456'),
                'documento' => '111.111.111-11',
                'telefone' => '(11) 9111-1111',
                'data_nascimento' => '1985-03-15',
                'profissao' => 'Advogada',
                'cargo_atual' => 'Vereadora',
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

        // Criar usuário protocolo de exemplo
        $protocolo = User::firstOrCreate(
            ['email' => 'protocolo@camara.gov.br'],
            [
                'name' => 'Carlos Protocolo Silva',
                'password' => bcrypt('protocolo123'),
                'documento' => '333.333.333-33',
                'telefone' => '(11) 3333-3333',
                'data_nascimento' => '1980-12-10',
                'profissao' => 'Técnico em Protocolo',
                'cargo_atual' => 'Responsável pelo Protocolo',
                'ativo' => true,
            ]
        );
        
        $protocolo->assignRole(User::PERFIL_PROTOCOLO);

        $this->command->info('Perfis e permissões criados com sucesso!');
        $this->command->info('Usuários criados:');
        $this->command->info('- Admin: admin@sistema.gov.br / admin123');
        $this->command->info('- Parlamentar: jessica@sistema.gov.br / 123456');
        $this->command->info('- Legislativo: servidor@camara.gov.br / servidor123');
        $this->command->info('- Protocolo: protocolo@camara.gov.br / protocolo123');
    }
}