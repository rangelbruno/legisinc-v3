<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ConfigureMenuPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:configure-menu 
                            {--role= : Configurar apenas um perfil específico}
                            {--reset : Resetar todas as permissões antes de configurar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configurar permissões de menu para todos os perfis do sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Configurando permissões de menu...');
        
        if ($this->option('reset')) {
            if ($this->confirm('⚠️  Tem certeza que deseja resetar TODAS as permissões?')) {
                $this->call('db:table', ['table' => 'screen_permissions']);
                $this->info('✅ Tabela de permissões resetada!');
            } else {
                $this->info('❌ Operação cancelada.');
                return;
            }
        }
        
        // Executar o seeder
        Artisan::call('db:seed', [
            '--class' => 'MenuPermissionsSeeder',
            '--force' => true
        ]);
        
        $this->info(Artisan::output());
        
        // Limpar cache
        $this->call('cache:clear');
        $this->info('✅ Cache limpo!');
        
        // Mostrar resumo
        $this->showPermissionsSummary();
        
        $this->info('✅ Permissões de menu configuradas com sucesso!');
        $this->info('');
        $this->info('🔍 Para testar o menu de um perfil específico:');
        $this->info('   php artisan permissions:test-menu [ROLE]');
        $this->info('');
        $this->info('📋 Perfis disponíveis:');
        $this->info('   - ADMIN');
        $this->info('   - PARLAMENTAR');
        $this->info('   - LEGISLATIVO');
        $this->info('   - PROTOCOLO');
        $this->info('   - EXPEDIENTE');
        $this->info('   - ASSESSOR_JURIDICO');
    }
    
    private function showPermissionsSummary()
    {
        $this->info('');
        $this->table(
            ['Perfil', 'Total Permissões', 'Com Acesso', 'Sem Acesso', '% Acesso'],
            $this->getPermissionStats()
        );
    }
    
    private function getPermissionStats()
    {
        $roles = [
            'ADMIN',
            'PARLAMENTAR', 
            'LEGISLATIVO',
            'PROTOCOLO',
            'EXPEDIENTE',
            'ASSESSOR_JURIDICO'
        ];
        
        $stats = [];
        
        foreach ($roles as $role) {
            $total = \App\Models\ScreenPermission::where('role_name', $role)->count();
            $allowed = \App\Models\ScreenPermission::where('role_name', $role)
                ->where('can_access', true)
                ->count();
            $denied = $total - $allowed;
            $percentage = $total > 0 ? round(($allowed / $total) * 100, 1) : 0;
            
            $stats[] = [
                $role,
                $total,
                $allowed,
                $denied,
                $percentage . '%'
            ];
        }
        
        return $stats;
    }
}