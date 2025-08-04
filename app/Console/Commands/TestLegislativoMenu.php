<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class TestLegislativoMenu extends Command
{
    protected $signature = 'test:legislativo-menu';
    protected $description = 'Testa especificamente o menu do perfil LEGISLATIVO';

    public function handle()
    {
        $this->info('🏛️ Testando menu específico do LEGISLATIVO');
        $this->newLine();
        
        // Simular o aside específico do LEGISLATIVO
        $this->info('📋 MENU LEGISLATIVO - O que deve aparecer:');
        $this->newLine();
        
        // Dashboard
        if ($this->canAccess('LEGISLATIVO', 'dashboard')) {
            $this->line('✅ Dashboard');
        } else {
            $this->line('❌ Dashboard');
        }
        
        // Parlamentares
        if ($this->canAccess('LEGISLATIVO', 'parlamentares.index')) {
            $this->line('✅ Parlamentares');
            $this->line('   └─ ✅ Lista de Parlamentares');
        } else {
            $this->line('❌ Parlamentares');
        }
        
        // Proposições
        if ($this->canAccessModule('LEGISLATIVO', 'proposicoes')) {
            $this->line('✅ Proposições');
            
            // Verificar submenus que NÃO devem aparecer
            if ($this->canAccess('LEGISLATIVO', 'proposicoes.criar')) {
                $this->line('   └─ ❌ Criar Proposição (ERRO: não deveria aparecer!)');
            } else {
                $this->line('   └─ ✅ Criar Proposição (corretamente oculto)');
            }
            
            if ($this->canAccess('LEGISLATIVO', 'proposicoes.minhas-proposicoes')) {
                $this->line('   └─ ❌ Minhas Proposições (ERRO: não deveria aparecer!)');
            } else {
                $this->line('   └─ ✅ Minhas Proposições (corretamente oculto)');
            }
            
            // Submenu Legislativo que DEVE aparecer
            $this->line('   └─ ✅ Legislativo (submenu)');
            
            if ($this->canAccess('LEGISLATIVO', 'proposicoes.legislativo.index')) {
                $this->line('       └─ ✅ Proposições Recebidas');
            } else {
                $this->line('       └─ ❌ Proposições Recebidas (ERRO!)');
            }
            
            if ($this->canAccess('LEGISLATIVO', 'proposicoes.relatorio-legislativo')) {
                $this->line('       └─ ✅ Relatório');
            } else {
                $this->line('       └─ ❌ Relatório (ERRO!)');
            }
            
            if ($this->canAccess('LEGISLATIVO', 'proposicoes.aguardando-protocolo')) {
                $this->line('       └─ ✅ Aguardando Protocolo');
            } else {
                $this->line('       └─ ❌ Aguardando Protocolo (ERRO!)');
            }
            
        } else {
            $this->line('❌ Proposições');
        }
        
        // Meu Perfil
        if ($this->canAccess('LEGISLATIVO', 'profile.edit')) {
            $this->line('✅ Meu Perfil');
        } else {
            $this->line('❌ Meu Perfil');
        }
        
        $this->newLine();
        $this->info('🎯 RESULTADO ESPERADO para LEGISLATIVO:');
        $this->line('• Dashboard');
        $this->line('• Parlamentares (lista apenas)');  
        $this->line('• Proposições');
        $this->line('  └─ Legislativo');
        $this->line('     ├─ Proposições Recebidas');
        $this->line('     ├─ Relatório');
        $this->line('     └─ Aguardando Protocolo');
        $this->line('• Meu Perfil');
        
        $this->newLine();
        $this->warn('❌ NÃO deve aparecer:');
        $this->line('• Criar Proposição');
        $this->line('• Minhas Proposições');
        $this->line('• Assinatura');
        $this->line('• Partidos, Sessões, Votações, Administração');
        
        return 0;
    }
    
    private function canAccess($role, $route)
    {
        return ScreenPermission::where('role_name', $role)
            ->where('screen_route', $route)
            ->where('can_access', true)
            ->exists();
    }
    
    private function canAccessModule($role, $module)
    {
        return ScreenPermission::where('role_name', $role)
            ->where('screen_module', $module)
            ->where('can_access', true)
            ->exists();
    }
}