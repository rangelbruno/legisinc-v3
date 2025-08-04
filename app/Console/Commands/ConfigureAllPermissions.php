<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class ConfigureAllPermissions extends Command
{
    protected $signature = 'permissions:configure-all';
    protected $description = 'Configura as permissões corretas para todos os perfis do sistema';

    public function handle()
    {
        $this->info('🚀 Configurando permissões para todos os perfis do sistema...');
        
        // Executar configuração de cada perfil
        $commands = [
            'permissions:configure-admin',
            'permissions:configure-parlamentar',
            'legislativo:configure-permissions',
            'permissions:configure-protocolo',
            'permissions:configure-expediente',
        ];
        
        foreach ($commands as $command) {
            $this->info("Executando: {$command}");
            $this->call($command);
            $this->newLine();
        }
        
        // Mostrar resumo geral
        $this->info('📊 Resumo geral das permissões:');
        
        $summary = ScreenPermission::selectRaw('
            role_name,
            COUNT(*) as total_permissions,
            SUM(CASE WHEN can_access = true THEN 1 ELSE 0 END) as allowed,
            SUM(CASE WHEN can_access = false THEN 1 ELSE 0 END) as denied
        ')
        ->whereIn('role_name', ['ADMIN', 'PARLAMENTAR', 'LEGISLATIVO', 'PROTOCOLO', 'EXPEDIENTE'])
        ->groupBy('role_name')
        ->orderBy('role_name')
        ->get();
        
        $tableData = [];
        foreach ($summary as $role) {
            $tableData[] = [
                $role->role_name,
                $role->total_permissions,
                $role->allowed,
                $role->denied,
            ];
        }
        
        $this->table(
            ['Perfil', 'Total', 'Permitidas', 'Negadas'],
            $tableData
        );
        
        $this->info('✅ Configuração de permissões concluída com sucesso!');
        $this->warn('🔄 Recomendação: Limpe o cache da aplicação com: php artisan cache:clear');
        
        return 0;
    }
}