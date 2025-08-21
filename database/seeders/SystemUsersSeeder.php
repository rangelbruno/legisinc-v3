<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SystemUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar role PROTOCOLO se não existir
        $protocoloRole = Role::firstOrCreate([
            'name' => User::PERFIL_PROTOCOLO,
            'guard_name' => 'web'
        ]);

        // Definir permissões para protocolo se não existirem
        $protocoloPermissions = [
            'protocolo.numerar',
            'protocolo.incluir_sessao',
            'projetos.view',
            'projetos.protocolar',
            'sessoes.view',
            'sessoes.incluir_projeto',
            'sistema.dashboard',
        ];

        // Criar/atualizar permissões para protocolo
        foreach ($protocoloPermissions as $permission) {
            $perm = \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
            
            // Atribuir permissão ao role protocolo
            if (!$protocoloRole->hasPermissionTo($perm)) {
                $protocoloRole->givePermissionTo($perm);
            }
        }

        // Definir usuários do sistema
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
                'name' => 'Jessica Santos',
                'email' => 'jessica@sistema.gov.br',
                'password' => Hash::make('123456'),
                'documento' => '111.111.111-11',
                'telefone' => '(11) 9111-1111',
                'data_nascimento' => '1985-03-15',
                'profissao' => 'Advogada',
                'cargo_atual' => 'Vereadora',
                'partido' => 'PT',
                'ativo' => true,
                'role' => User::PERFIL_PARLAMENTAR
            ],
            [
                'name' => 'Servidor Legislativo',
                'email' => 'servidor@camara.gov.br',
                'password' => Hash::make('servidor123'),
                'documento' => '222.222.222-22',
                'telefone' => '(11) 2222-2222',
                'data_nascimento' => '1985-08-20',
                'profissao' => 'Analista Legislativo',
                'cargo_atual' => 'Servidor Legislativo',
                'ativo' => true,
                'role' => User::PERFIL_LEGISLATIVO
            ],
            [
                'name' => 'Servidor do Protocolo',
                'email' => 'protocolo@camara.gov.br',
                'password' => Hash::make('protocolo123'),
                'documento' => '333.333.333-33',
                'telefone' => '(11) 3333-3333',
                'data_nascimento' => '1990-12-10',
                'profissao' => 'Técnico Administrativo',
                'cargo_atual' => 'Servidor do Protocolo',
                'ativo' => true,
                'role' => User::PERFIL_PROTOCOLO
            ]
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            
            // Verificar se usuário já existe
            $existingUser = User::where('email', $userData['email'])->first();
            
            if (!$existingUser) {
                $user = User::create(array_merge($userData, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]));

                // Atribuir role ao usuário
                $user->assignRole($role);
                
                $this->command->info("Usuário {$userData['name']} criado com sucesso!");
            } else {
                $this->command->warn("Usuário {$userData['email']} já existe!");
            }
        }

        $this->command->info('✅ Usuários do sistema criados com sucesso!');
        $this->command->info('');
        $this->command->info('👥 Usuários do Sistema de Tramitação:');
        $this->command->info('');
        $this->command->info('🔧 Administrador:');
        $this->command->info('   Email: admin@sistema.gov.br');
        $this->command->info('   Senha: admin123');
        $this->command->info('   Role: admin (acesso total)');
        $this->command->info('');
        $this->command->info('🏛️ Parlamentar:');
        $this->command->info('   Nome: Jessica Santos');
        $this->command->info('   Email: jessica@sistema.gov.br');
        $this->command->info('   Senha: 123456');
        $this->command->info('   Role: parlamentar');
        $this->command->info('   Partido: PT');
        $this->command->info('   Cargo: Vereadora');
        $this->command->info('   Permissões: Criar, editar e assinar projetos próprios');
        $this->command->info('');
        $this->command->info('⚖️ Legislativo:');
        $this->command->info('   Email: servidor@camara.gov.br');
        $this->command->info('   Senha: servidor123');
        $this->command->info('   Role: legislativo');
        $this->command->info('   Permissões: Analisar, aprovar/rejeitar projetos');
        $this->command->info('');
        $this->command->info('📋 Protocolo:');
        $this->command->info('   Email: protocolo@camara.gov.br');
        $this->command->info('   Senha: protocolo123');
        $this->command->info('   Role: protocolo');
        $this->command->info('   Permissões: Atribuir números de protocolo e incluir projetos em sessão');
    }
} 