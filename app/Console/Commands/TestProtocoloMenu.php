<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class TestProtocoloMenu extends Command
{
    protected $signature = 'test:protocolo-menu';
    protected $description = 'Testa especificamente o menu do perfil PROTOCOLO';

    public function handle()
    {
        $this->info('📋 Testando menu específico do PROTOCOLO');
        $this->newLine();
        
        // Simular o aside específico do PROTOCOLO
        $this->info('📋 MENU PROTOCOLO - O que deve aparecer:');
        $this->newLine();
        
        // Dashboard
        if ($this->canAccess('PROTOCOLO', 'dashboard')) {
            $this->line('✅ Dashboard');
        } else {
            $this->line('❌ Dashboard');
        }
        
        // Parlamentares (deve estar oculto)
        if ($this->canAccessModule('PROTOCOLO', 'parlamentares')) {
            $this->line('❌ Parlamentares (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Parlamentares (corretamente oculto)');
        }
        
        // Proposições
        if ($this->canAccessModule('PROTOCOLO', 'proposicoes')) {
            $this->line('✅ Proposições');
            
            // Verificar submenus que NÃO devem aparecer
            if ($this->canAccess('PROTOCOLO', 'proposicoes.criar')) {
                $this->line('   └─ ❌ Criar Proposição (ERRO: não deveria aparecer!)');
            } else {
                $this->line('   └─ ✅ Criar Proposição (corretamente oculto)');
            }
            
            if ($this->canAccess('PROTOCOLO', 'proposicoes.minhas-proposicoes')) {
                $this->line('   └─ ❌ Minhas Proposições (ERRO: não deveria aparecer!)');
            } else {
                $this->line('   └─ ✅ Minhas Proposições (corretamente oculto)');
            }
            
            // Submenu Protocolo que DEVE aparecer
            $this->line('   └─ ✅ Protocolo (submenu)');
            
            if ($this->canAccess('PROTOCOLO', 'proposicoes.aguardando-protocolo')) {
                $this->line('       └─ ✅ Aguardando Protocolo');
            } else {
                $this->line('       └─ ❌ Aguardando Protocolo (ERRO!)');
            }
            
            if ($this->canAccess('PROTOCOLO', 'proposicoes.protocolar')) {
                $this->line('       └─ ✅ Protocolar');
            } else {
                $this->line('       └─ ❌ Protocolar (ERRO!)');
            }
            
            if ($this->canAccess('PROTOCOLO', 'proposicoes.protocolos-hoje')) {
                $this->line('       └─ ✅ Protocolos Hoje');
            } else {
                $this->line('       └─ ❌ Protocolos Hoje (ERRO!)');
            }
            
            if ($this->canAccess('PROTOCOLO', 'proposicoes.estatisticas-protocolo')) {
                $this->line('       └─ ✅ Estatísticas');
            } else {
                $this->line('       └─ ❌ Estatísticas (ERRO!)');
            }
            
        } else {
            $this->line('❌ Proposições');
        }
        
        // Sessões (deve estar oculto)
        if ($this->canAccessModule('PROTOCOLO', 'sessoes')) {
            $this->line('❌ Sessões (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Sessões (corretamente oculto)');
        }
        
        // Votações (deve estar oculto)
        if ($this->canAccessModule('PROTOCOLO', 'votacoes')) {
            $this->line('❌ Votações (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Votações (corretamente oculto)');
        }
        
        // Comissões (deve estar oculto)
        if ($this->canAccessModule('PROTOCOLO', 'comissoes')) {
            $this->line('❌ Comissões (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Comissões (corretamente oculto)');
        }
        
        // Meu Perfil
        if ($this->canAccess('PROTOCOLO', 'profile.edit')) {
            $this->line('✅ Meu Perfil');
        } else {
            $this->line('❌ Meu Perfil');
        }
        
        $this->newLine();
        $this->info('🎯 RESULTADO ESPERADO para PROTOCOLO:');
        $this->line('• Dashboard');
        $this->line('• Proposições');
        $this->line('  └─ Protocolo');
        $this->line('     ├─ Aguardando Protocolo');
        $this->line('     ├─ Protocolar');
        $this->line('     ├─ Protocolos Hoje');
        $this->line('     └─ Estatísticas');
        $this->line('• Meu Perfil');
        
        $this->newLine();
        $this->warn('❌ NÃO deve aparecer:');
        $this->line('• Parlamentares (não gerencia)');
        $this->line('• Partidos');
        $this->line('• Criar Proposição');
        $this->line('• Minhas Proposições');
        $this->line('• Assinatura');
        $this->line('• Sessões (não gerencia)');
        $this->line('• Votações (não gerencia)');
        $this->line('• Comissões (não gerencia)');
        $this->line('• Administração');
        
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