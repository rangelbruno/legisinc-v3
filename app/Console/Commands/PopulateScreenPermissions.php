<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class PopulateScreenPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:populate-screens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popula as permissões de tela para todos os perfis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Populando permissões de tela...');
        
        // Configurar permissões para PARLAMENTAR
        $this->configurarPermissoesParlamentar();
        $this->line('✓ Permissões do PARLAMENTAR configuradas');
        
        // Configurar permissões para RELATOR
        $this->configurarPermissoesRelator();
        $this->line('✓ Permissões do RELATOR configuradas');
        
        // Configurar permissões para PROTOCOLO
        $this->configurarPermissoesProtocolo();
        $this->line('✓ Permissões do PROTOCOLO configuradas');
        
        // Configurar permissões para ASSESSOR
        $this->configurarPermissoesAssessor();
        $this->line('✓ Permissões do ASSESSOR configuradas');
        
        // Configurar permissões para PUBLICO
        $this->configurarPermissoesPublico();
        $this->line('✓ Permissões do PUBLICO configuradas');
        
        $this->info('Permissões de tela populadas com sucesso!');
        
        return Command::SUCCESS;
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
                'screen_route' => 'dashboard.index',
                'screen_name' => 'Painel Principal',
                'screen_module' => 'dashboard',
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
