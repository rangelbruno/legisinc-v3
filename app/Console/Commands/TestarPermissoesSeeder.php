<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;
use App\Models\User;

class TestarPermissoesSeeder extends Command
{
    protected $signature = 'seeder:testar-permissoes';
    protected $description = 'Testa se as permissões do seeder foram aplicadas corretamente';

    public function handle()
    {
        $this->info('🧪 Testando permissões aplicadas pelo seeder...');
        $this->newLine();

        // 1. Verificar total de permissões por role
        $this->info('📊 Total de permissões por role:');
        $stats = ScreenPermission::selectRaw('role_name, COUNT(*) as total')
            ->where('can_access', true)
            ->groupBy('role_name')
            ->orderBy('role_name')
            ->get();

        $tableData = [];
        foreach ($stats as $stat) {
            $tableData[] = [$stat->role_name, $stat->total];
        }
        $this->table(['Role', 'Total Permissões'], $tableData);

        // 2. Verificar permissões específicas do EXPEDIENTE
        $this->newLine();
        $this->info('📋 Permissões específicas do EXPEDIENTE:');
        
        $expedientePermissions = ScreenPermission::where('role_name', 'EXPEDIENTE')
            ->where('screen_module', 'expediente')
            ->where('can_access', true)
            ->get();

        if ($expedientePermissions->count() > 0) {
            $this->line('✅ Módulo expediente configurado com ' . $expedientePermissions->count() . ' permissões:');
            foreach ($expedientePermissions as $perm) {
                $this->line("  • {$perm->screen_route} - {$perm->screen_name}");
            }
        } else {
            $this->error('❌ Nenhuma permissão do módulo expediente encontrada!');
        }

        // 3. Testar usuário EXPEDIENTE
        $this->newLine();
        $this->info('👤 Testando usuário EXPEDIENTE:');
        
        $user = User::where('name', 'Carlos Expediente')->first();
        if ($user) {
            $this->line("✅ Usuário encontrado: {$user->name} ({$user->email})");
            
            // Simular verificação de permissões
            $canAccessExpediente = ScreenPermission::where('role_name', 'EXPEDIENTE')
                ->where('screen_route', 'expediente.index')
                ->where('can_access', true)
                ->exists();
                
            if ($canAccessExpediente) {
                $this->line('✅ Tem acesso ao painel do expediente');
            } else {
                $this->error('❌ NÃO tem acesso ao painel do expediente');
            }
            
            $canAccessModule = ScreenPermission::where('role_name', 'EXPEDIENTE')
                ->where('screen_module', 'expediente')
                ->where('can_access', true)
                ->exists();
                
            if ($canAccessModule) {
                $this->line('✅ Tem acesso ao módulo expediente');
            } else {
                $this->error('❌ NÃO tem acesso ao módulo expediente');
            }
        } else {
            $this->error('❌ Usuário EXPEDIENTE não encontrado!');
        }

        // 4. Verificar outras permissões essenciais do EXPEDIENTE
        $this->newLine();
        $this->info('🔍 Verificando outras permissões essenciais:');
        
        $essentialRoutes = [
            'dashboard',
            'proposicoes.show',
            'proposicoes.legislativo.index',
            'admin.sessions.index',
            'sessoes.agenda',
            'profile.edit'
        ];

        foreach ($essentialRoutes as $route) {
            $hasPermission = ScreenPermission::where('role_name', 'EXPEDIENTE')
                ->where('screen_route', $route)
                ->where('can_access', true)
                ->exists();
                
            $status = $hasPermission ? '✅' : '❌';
            $this->line("  {$status} {$route}");
        }

        // 5. Resultado final
        $this->newLine();
        $this->info('🎯 RESULTADO:');
        
        $totalExpediente = ScreenPermission::where('role_name', 'EXPEDIENTE')
            ->where('can_access', true)
            ->count();
            
        if ($totalExpediente >= 30) {
            $this->line('✅ Seeder aplicado corretamente!');
            $this->line("✅ EXPEDIENTE tem {$totalExpediente} permissões configuradas");
            $this->line('✅ Módulo expediente disponível');
            $this->line('✅ Menu deve aparecer quando logado como EXPEDIENTE');
            
            $this->newLine();
            $this->warn('💡 Para testar:');
            $this->line('1. Faça logout do usuário atual');
            $this->line('2. Faça login com: expediente@sistema.gov.br / 123456');
            $this->line('3. O menu "Expediente" deve aparecer em Proposições');
        } else {
            $this->error('❌ Seeder não aplicado corretamente ou incompleto');
            $this->line("❌ EXPEDIENTE tem apenas {$totalExpediente} permissões");
        }

        return 0;
    }
}