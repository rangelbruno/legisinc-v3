<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScreenPermission;
use App\Models\User;

class ScreenPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar permissões existentes
        ScreenPermission::truncate();
        
        // Configurar permissões para cada perfil
        $this->configurarPermissoesParlamentar();
        $this->configurarPermissoesRelator();
        $this->configurarPermissoesProtocolo();
        $this->configurarPermissoesAssessor();
        $this->configurarPermissoesPublico();
        
        $this->command->info('Permissões de tela configuradas com sucesso!');
    }
    
    /**
     * Configurar permissões para perfil PARLAMENTAR
     */
    private function configurarPermissoesParlamentar(): void
    {
        $permissions = [
            // Dashboard sempre habilitado
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'dashboard.index',
                'screen_name' => 'Painel Principal',
                'screen_module' => 'dashboard',
                'can_access' => true,
            ],
            
            // Parlamentares - pode visualizar
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'parlamentares.index',
                'screen_name' => 'Listar Parlamentares',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'parlamentares.mesa-diretora',
                'screen_name' => 'Mesa Diretora',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            
            // Projetos - pode visualizar, criar e editar
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'projetos.index',
                'screen_name' => 'Listar Projetos',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'projetos.create',
                'screen_name' => 'Criar Projeto',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'projetos.edit',
                'screen_name' => 'Editar Projeto',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            
            // Comissões - pode visualizar
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'comissoes.index',
                'screen_name' => 'Listar Comissões',
                'screen_module' => 'comissoes',
                'can_access' => true,
            ],
            
            // Sessões - pode visualizar e criar
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'admin.sessions.index',
                'screen_name' => 'Listar Sessões',
                'screen_module' => 'sessoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PARLAMENTAR',
                'screen_route' => 'admin.sessions.create',
                'screen_name' => 'Criar Sessão',
                'screen_module' => 'sessoes',
                'can_access' => true,
            ],
        ];
        
        foreach ($permissions as $permission) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $permission['role_name'],
                    'screen_route' => $permission['screen_route'],
                ],
                $permission
            );
        }
    }
    
    /**
     * Configurar permissões para perfil RELATOR
     */
    private function configurarPermissoesRelator(): void
    {
        $permissions = [
            // Dashboard sempre habilitado
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'dashboard.index',
                'screen_name' => 'Painel Principal',
                'screen_module' => 'dashboard',
                'can_access' => true,
            ],
            
            // Parlamentares - pode visualizar
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'parlamentares.index',
                'screen_name' => 'Listar Parlamentares',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'parlamentares.mesa-diretora',
                'screen_name' => 'Mesa Diretora',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            
            // Projetos - pode visualizar, criar e editar (como relator)
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'projetos.index',
                'screen_name' => 'Listar Projetos',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'projetos.create',
                'screen_name' => 'Criar Projeto',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'projetos.edit',
                'screen_name' => 'Editar Projeto',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            
            // Comissões - pode visualizar
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'comissoes.index',
                'screen_name' => 'Listar Comissões',
                'screen_module' => 'comissoes',
                'can_access' => true,
            ],
            
            // Sessões - pode visualizar
            [
                'role_name' => 'RELATOR',
                'screen_route' => 'admin.sessions.index',
                'screen_name' => 'Listar Sessões',
                'screen_module' => 'sessoes',
                'can_access' => true,
            ],
        ];
        
        foreach ($permissions as $permission) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $permission['role_name'],
                    'screen_route' => $permission['screen_route'],
                ],
                $permission
            );
        }
    }
    
    /**
     * Configurar permissões para perfil PROTOCOLO
     */
    private function configurarPermissoesProtocolo(): void
    {
        $permissions = [
            // Dashboard sempre habilitado
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'dashboard',
                'screen_name' => 'Painel Principal',
                'screen_module' => 'dashboard',
                'can_access' => true,
            ],
            
            // Proposições - Telas específicas do protocolo
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.aguardando-protocolo',
                'screen_name' => 'Aguardando Protocolo',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.protocolar',
                'screen_name' => 'Protocolar',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.protocolar.show',
                'screen_name' => 'Protocolar Proposição',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.efetivar-protocolo',
                'screen_name' => 'Efetivar Protocolo',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.protocolos-hoje',
                'screen_name' => 'Protocolos Hoje',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.estatisticas-protocolo',
                'screen_name' => 'Estatísticas Protocolo',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'proposicoes.iniciar-tramitacao',
                'screen_name' => 'Iniciar Tramitação',
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ],
            
            // Parlamentares - pode visualizar (necessário para contexto)
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'parlamentares.index',
                'screen_name' => 'Listar Parlamentares',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            
            // Sessões - pode visualizar
            [
                'role_name' => 'PROTOCOLO',
                'screen_route' => 'admin.sessions.index',
                'screen_name' => 'Listar Sessões',
                'screen_module' => 'sessoes',
                'can_access' => true,
            ],
        ];
        
        foreach ($permissions as $permission) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $permission['role_name'],
                    'screen_route' => $permission['screen_route'],
                ],
                $permission
            );
        }
    }
    
    /**
     * Configurar permissões para perfil ASSESSOR
     */
    private function configurarPermissoesAssessor(): void
    {
        $permissions = [
            // Dashboard sempre habilitado
            [
                'role_name' => 'ASSESSOR',
                'screen_route' => 'dashboard.index',
                'screen_name' => 'Painel Principal',
                'screen_module' => 'dashboard',
                'can_access' => true,
            ],
            
            // Parlamentares - pode visualizar
            [
                'role_name' => 'ASSESSOR',
                'screen_route' => 'parlamentares.index',
                'screen_name' => 'Listar Parlamentares',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            
            // Projetos - pode apenas visualizar
            [
                'role_name' => 'ASSESSOR',
                'screen_route' => 'projetos.index',
                'screen_name' => 'Listar Projetos',
                'screen_module' => 'projetos',
                'can_access' => true,
            ],
            
            // Sessões - pode visualizar
            [
                'role_name' => 'ASSESSOR',
                'screen_route' => 'admin.sessions.index',
                'screen_name' => 'Listar Sessões',
                'screen_module' => 'sessoes',
                'can_access' => true,
            ],
        ];
        
        foreach ($permissions as $permission) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $permission['role_name'],
                    'screen_route' => $permission['screen_route'],
                ],
                $permission
            );
        }
    }
    
    /**
     * Configurar permissões para perfil PUBLICO
     */
    private function configurarPermissoesPublico(): void
    {
        $permissions = [
            // Dashboard sempre habilitado
            [
                'role_name' => 'PUBLICO',
                'screen_route' => 'dashboard.index',
                'screen_name' => 'Painel Principal',
                'screen_module' => 'dashboard',
                'can_access' => true,
            ],
            
            // Parlamentares - pode visualizar
            [
                'role_name' => 'PUBLICO',
                'screen_route' => 'parlamentares.index',
                'screen_name' => 'Listar Parlamentares',
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ],
            
            // Sessões - pode visualizar
            [
                'role_name' => 'PUBLICO',
                'screen_route' => 'admin.sessions.index',
                'screen_name' => 'Listar Sessões',
                'screen_module' => 'sessoes',
                'can_access' => true,
            ],
        ];
        
        foreach ($permissions as $permission) {
            ScreenPermission::updateOrCreate(
                [
                    'role_name' => $permission['role_name'],
                    'screen_route' => $permission['screen_route'],
                ],
                $permission
            );
        }
    }
}
