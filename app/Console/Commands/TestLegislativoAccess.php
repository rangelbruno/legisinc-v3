<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;
use App\Models\User;

class TestLegislativoAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legislativo:test-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testar acesso do perfil LEGISLATIVO sem banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testando permissões padrão para perfil LEGISLATIVO...');
        
        // Simular usuário LEGISLATIVO
        $this->info('Criando usuário simulado com perfil LEGISLATIVO...');
        
        // Criar usuário de teste
        $testUser = new User();
        $testUser->id = 999;
        $testUser->name = 'Servidor Legislativo';
        $testUser->email = 'legislativo@teste.gov.br';
        $testUser->cargo_atual = 'Servidor Legislativo';
        $testUser->exists = true;
        
        // Testar detecção de role
        $roles = $testUser->getRoleNames();
        $this->info("Roles detectados: " . $roles->join(', '));
        
        $isLegislativo = $testUser->isLegislativo();
        $hasLegislativoRole = $testUser->hasRole('LEGISLATIVO');
        
        $this->info("isLegislativo(): " . ($isLegislativo ? '✅ SIM' : '❌ NÃO'));
        $this->info("hasRole('LEGISLATIVO'): " . ($hasLegislativoRole ? '✅ SIM' : '❌ NÃO'));
        
        // Testar permissões padrão
        $defaultPermissions = ScreenPermission::getDefaultLegislativoPermissions();
        $defaultModules = ScreenPermission::getDefaultLegislativoModules();
        
        $this->info('✅ Permissões padrão carregadas com sucesso!');
        
        $this->table(
            ['Tipo', 'Quantidade', 'Exemplos'],
            [
                ['Rotas', count($defaultPermissions), 'dashboard, proposicoes.legislativo.index'],
                ['Módulos', count($defaultModules), 'dashboard, proposicoes, parlamentares'],
            ]
        );
        
        // Testar rotas específicas
        $testRoutes = [
            'dashboard',
            'proposicoes.legislativo.index',
            'proposicoes.legislativo.editar',
            'proposicoes.revisar',
            'parlamentares.index'
        ];
        
        $this->info('Testando rotas específicas:');
        foreach ($testRoutes as $route) {
            $hasAccess = $defaultPermissions[$route] ?? false;
            $status = $hasAccess ? '✅ PERMITIDO' : '❌ NEGADO';
            $this->line("  {$route}: {$status}");
        }
        
        // Testar módulos
        $testModules = ['dashboard', 'proposicoes', 'parlamentares', 'usuarios'];
        
        $this->info('Testando módulos:');
        foreach ($testModules as $module) {
            $hasAccess = $defaultModules[$module] ?? false;
            $status = $hasAccess ? '✅ PERMITIDO' : '❌ NEGADO';
            $this->line("  {$module}: {$status}");
        }
        
        $this->warn('💡 Nota: Estes são os acessos padrão quando não há configuração no banco de dados.');
        $this->info('Para configurar permanentemente, execute: php artisan legislativo:configure-permissions');
        
        return Command::SUCCESS;
    }
}
