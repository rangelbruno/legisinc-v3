<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureLegislativoPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legislativo:configure-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurar permissões padrão para perfil LEGISLATIVO';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Configurando permissões para perfil LEGISLATIVO...');
        
        $roleName = 'LEGISLATIVO';
        
        // Limpar permissões existentes para evitar conflitos
        $this->info('Removendo permissões antigas...');
        ScreenPermission::where('role_name', $roleName)->delete();
        
        // Permissões para o módulo Dashboard
        $this->info('Configurando permissões para Dashboard...');
        ScreenPermission::create([
            'role_name' => $roleName,
            'screen_route' => 'dashboard',
            'screen_name' => 'Dashboard',
            'screen_module' => 'dashboard',
            'can_access' => true,
        ]);

        // Permissões para o módulo Proposições - LEGISLATIVO NÃO CRIA PROPOSIÇÕES
        $this->info('Configurando permissões para Proposições (apenas análise e revisão)...');
        $proposicoesPermissions = [
            // LEGISLATIVO NÃO PODE criar proposições
            // 'proposicoes.criar' => 'Criar Proposição', // REMOVIDO
            // 'proposicoes.minhas-proposicoes' => 'Minhas Proposições', // REMOVIDO
            
            // Apenas visualização e análise
            'proposicoes.show' => 'Visualizar Proposição',
            'proposicoes.legislativo.index' => 'Proposições Recebidas',
            'proposicoes.legislativo.editar' => 'Editar via Legislativo',
            'proposicoes.legislativo.salvar-edicao' => 'Salvar Edição Legislativo',
            'proposicoes.legislativo.enviar-parlamentar' => 'Enviar para Parlamentar',
            'proposicoes.revisar' => 'Revisar Proposições',
            'proposicoes.revisar.show' => 'Revisar Proposição',
            'proposicoes.salvar-analise' => 'Salvar Análise',
            'proposicoes.aprovar' => 'Aprovar Proposição',
            'proposicoes.devolver' => 'Devolver Proposição',
            'proposicoes.relatorio-legislativo' => 'Relatório Legislativo',
            'proposicoes.aguardando-protocolo' => 'Aguardando Protocolo',
        ];

        foreach ($proposicoesPermissions as $route => $name) {
            ScreenPermission::create([
                'role_name' => $roleName,
                'screen_route' => $route,
                'screen_name' => $name,
                'screen_module' => 'proposicoes',
                'can_access' => true,
            ]);
        }

        // Permissões para visualizar parlamentares (necessário para trabalhar com proposições)
        $this->info('Configurando permissões para Parlamentares...');
        $parlamentaresPermissions = [
            'parlamentares.index' => 'Lista de Parlamentares',
            'parlamentares.show' => 'Visualizar Parlamentar',
        ];

        foreach ($parlamentaresPermissions as $route => $name) {
            ScreenPermission::create([
                'role_name' => $roleName,
                'screen_route' => $route,
                'screen_name' => $name,
                'screen_module' => 'parlamentares',
                'can_access' => true,
            ]);
        }

        // Permissões básicas do sistema
        $this->info('Configurando permissões básicas do sistema...');
        $systemPermissions = [
            'user.profile' => 'Meu Perfil',
            'user.update-last-access' => 'Atualizar Último Acesso',
        ];

        foreach ($systemPermissions as $route => $name) {
            ScreenPermission::create([
                'role_name' => $roleName,
                'screen_route' => $route,
                'screen_name' => $name,
                'screen_module' => 'sistema',
                'can_access' => true,
            ]);
        }

        $total = ScreenPermission::where('role_name', $roleName)->count();
        $this->info("✅ Permissões configuradas com sucesso! Total: {$total} permissões");
        
        $this->table(
            ['Módulo', 'Permissões'],
            [
                ['Dashboard', '1'],
                ['Proposições', count($proposicoesPermissions)],
                ['Parlamentares', count($parlamentaresPermissions)],
                ['Sistema', count($systemPermissions)],
            ]
        );
        
        $this->warn('🔄 Recomendação: Limpe o cache da aplicação executando: php artisan cache:clear');
        
        return Command::SUCCESS;
    }
}
