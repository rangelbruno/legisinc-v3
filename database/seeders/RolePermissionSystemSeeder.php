<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🔐 Configurando sistema de permissões por role...');

        // Garantir que todas as permissões necessárias estão na tabela screen_permissions
        $this->criarPermissoesAssinatura();
        $this->criarPermissoesOnlyOffice();
        $this->criarPermissoesAPI();
        $this->validarPermissoesPorRole();

        $this->command->info('✅ Sistema de permissões configurado com sucesso!');
    }

    /**
     * Criar permissões para assinatura digital
     */
    private function criarPermissoesAssinatura(): void
    {
        $this->command->info('📝 Configurando permissões de assinatura...');

        $permissoesAssinatura = [
            [
                'screen_route' => 'proposicoes.assinar',
                'screen_name' => 'Assinar Proposição',
                'screen_module' => 'proposicoes',
                'roles' => ['ADMIN', 'PARLAMENTAR']
            ],
            [
                'screen_route' => 'proposicoes.corrigir',
                'screen_name' => 'Corrigir Proposição',
                'screen_module' => 'proposicoes',
                'roles' => ['ADMIN', 'PARLAMENTAR']
            ],
            [
                'screen_route' => 'proposicoes.processar-assinatura',
                'screen_name' => 'Processar Assinatura Digital',
                'screen_module' => 'proposicoes',
                'roles' => ['ADMIN', 'PARLAMENTAR']
            ],
            [
                'screen_route' => 'proposicoes.confirmar-leitura',
                'screen_name' => 'Confirmar Leitura',
                'screen_module' => 'proposicoes',
                'roles' => ['ADMIN', 'PARLAMENTAR']
            ],
            [
                'screen_route' => 'proposicoes.historico-assinaturas',
                'screen_name' => 'Histórico de Assinaturas',
                'screen_module' => 'proposicoes',
                'roles' => ['ADMIN', 'PARLAMENTAR']
            ],
        ];

        foreach ($permissoesAssinatura as $permissao) {
            foreach ($permissao['roles'] as $role) {
                DB::table('screen_permissions')->updateOrInsert(
                    [
                        'role_name' => $role,
                        'screen_route' => $permissao['screen_route']
                    ],
                    [
                        'screen_name' => $permissao['screen_name'],
                        'screen_module' => $permissao['screen_module'],
                        'can_access' => true,
                        'can_create' => in_array($permissao['screen_route'], ['proposicoes.processar-assinatura']),
                        'can_edit' => in_array($permissao['screen_route'], ['proposicoes.corrigir']),
                        'can_delete' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Criar permissões para OnlyOffice
     */
    private function criarPermissoesOnlyOffice(): void
    {
        $this->command->info('📄 Configurando permissões do OnlyOffice...');

        $permissoesOnlyOffice = [
            [
                'screen_route' => 'onlyoffice.editor-parlamentar',
                'screen_name' => 'Editor OnlyOffice Parlamentar',
                'screen_module' => 'onlyoffice',
                'roles' => ['ADMIN', 'PARLAMENTAR']
            ],
            [
                'screen_route' => 'onlyoffice.editor',
                'screen_name' => 'Editor OnlyOffice Legislativo',
                'screen_module' => 'onlyoffice',
                'roles' => ['ADMIN', 'LEGISLATIVO']
            ],
            [
                'screen_route' => 'onlyoffice.callback',
                'screen_name' => 'Callback OnlyOffice',
                'screen_module' => 'onlyoffice',
                'roles' => ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO']
            ],
            [
                'screen_route' => 'proposicoes.onlyoffice.download',
                'screen_name' => 'Download OnlyOffice',
                'screen_module' => 'onlyoffice',
                'roles' => ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO']
            ],
            [
                'screen_route' => 'proposicoes.onlyoffice.status',
                'screen_name' => 'Status OnlyOffice',
                'screen_module' => 'onlyoffice',
                'roles' => ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO']
            ],
        ];

        foreach ($permissoesOnlyOffice as $permissao) {
            foreach ($permissao['roles'] as $role) {
                DB::table('screen_permissions')->updateOrInsert(
                    [
                        'role_name' => $role,
                        'screen_route' => $permissao['screen_route']
                    ],
                    [
                        'screen_name' => $permissao['screen_name'],
                        'screen_module' => $permissao['screen_module'],
                        'can_access' => true,
                        'can_create' => false,
                        'can_edit' => in_array($role, ['PARLAMENTAR', 'LEGISLATIVO']),
                        'can_delete' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Criar permissões para API
     */
    private function criarPermissoesAPI(): void
    {
        $this->command->info('🔌 Configurando permissões da API...');

        $permissoesAPI = [
            [
                'screen_route' => 'api.proposicoes.show',
                'screen_name' => 'API - Visualizar Proposição',
                'screen_module' => 'api',
                'roles' => ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO', 'PROTOCOLO']
            ],
            [
                'screen_route' => 'api.proposicoes.update',
                'screen_name' => 'API - Atualizar Proposição',
                'screen_module' => 'api',
                'roles' => ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO']
            ],
            [
                'screen_route' => 'api.proposicoes.status',
                'screen_name' => 'API - Atualizar Status',
                'screen_module' => 'api',
                'roles' => ['ADMIN', 'LEGISLATIVO', 'PROTOCOLO']
            ],
            [
                'screen_route' => 'api.proposicoes.updates',
                'screen_name' => 'API - Verificar Atualizações',
                'screen_module' => 'api',
                'roles' => ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO', 'PROTOCOLO']
            ],
        ];

        foreach ($permissoesAPI as $permissao) {
            foreach ($permissao['roles'] as $role) {
                DB::table('screen_permissions')->updateOrInsert(
                    [
                        'role_name' => $role,
                        'screen_route' => $permissao['screen_route']
                    ],
                    [
                        'screen_name' => $permissao['screen_name'],
                        'screen_module' => $permissao['screen_module'],
                        'can_access' => true,
                        'can_create' => false,
                        'can_edit' => in_array($permissao['screen_route'], ['api.proposicoes.update', 'api.proposicoes.status']),
                        'can_delete' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Validar e corrigir permissões por role
     */
    private function validarPermissoesPorRole(): void
    {
        $this->command->info('🔍 Validando permissões por role...');

        // Definir permissões essenciais por role
        $permissoesEssenciais = [
            'PARLAMENTAR' => [
                'proposicoes.create',
                'proposicoes.show',
                'proposicoes.edit',
                'proposicoes.assinar',
                'proposicoes.corrigir',
                'onlyoffice.editor-parlamentar',
                'dashboard'
            ],
            'LEGISLATIVO' => [
                'proposicoes.show',
                'proposicoes.revisar',
                'proposicoes.aprovar',
                'proposicoes.devolver',
                'onlyoffice.editor',
                'dashboard'
            ],
            'PROTOCOLO' => [
                'proposicoes.show',
                'proposicoes.protocolar',
                'proposicoes.numerar',
                'dashboard'
            ],
            'ADMIN' => [
                '*' // Admin tem acesso total
            ]
        ];

        foreach ($permissoesEssenciais as $role => $permissoes) {
            if ($role === 'ADMIN') {
                continue; // Admin já tem acesso total
            }

            foreach ($permissoes as $permissao) {
                // Verificar se permissão existe
                $exists = DB::table('screen_permissions')
                    ->where('role_name', $role)
                    ->where('screen_route', $permissao)
                    ->exists();

                if (!$exists) {
                    // Criar permissão se não existir
                    DB::table('screen_permissions')->insert([
                        'role_name' => $role,
                        'screen_route' => $permissao,
                        'screen_name' => ucfirst(str_replace(['.', '_'], [' ', ' '], $permissao)),
                        'screen_module' => explode('.', $permissao)[0] ?? 'sistema',
                        'can_access' => true,
                        'can_create' => in_array($permissao, ['proposicoes.create']),
                        'can_edit' => in_array($permissao, ['proposicoes.edit', 'proposicoes.corrigir']),
                        'can_delete' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $this->command->warn("  ➕ Adicionada permissão '$permissao' para role '$role'");
                }
            }
        }
    }

    /**
     * Remover controller hardcoded de validação de status
     */
    public function removerValidacaoHardcoded(): void
    {
        $this->command->info('🧹 Removendo validações hardcoded desnecessárias...');
        
        // Esta correção já foi aplicada diretamente no controller
        // ProposicaoAssinaturaController.php linha 33
        
        $this->command->info('✅ Validações hardcoded removidas - middleware assumiu controle');
    }
}