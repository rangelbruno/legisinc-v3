<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScreenPermission;

class TestExpedienteMenu extends Command
{
    protected $signature = 'test:expediente-menu';
    protected $description = 'Testa especificamente o menu do perfil EXPEDIENTE';

    public function handle()
    {
        $this->info('📋 Testando menu específico do EXPEDIENTE');
        $this->newLine();
        
        // Simular o aside específico do EXPEDIENTE
        $this->info('📋 MENU EXPEDIENTE - O que deve aparecer:');
        $this->newLine();
        
        // Dashboard
        if ($this->canAccess('EXPEDIENTE', 'dashboard')) {
            $this->line('✅ Dashboard');
        } else {
            $this->line('❌ Dashboard');
        }
        
        // Parlamentares (deve estar oculto)
        if ($this->canAccessModule('EXPEDIENTE', 'parlamentares')) {
            $this->line('❌ Parlamentares (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Parlamentares (corretamente oculto)');
        }
        
        // Proposições
        if ($this->canAccessModule('EXPEDIENTE', 'proposicoes')) {
            $this->line('✅ Proposições');
            
            // Verificar submenus que NÃO devem aparecer
            if ($this->canAccess('EXPEDIENTE', 'proposicoes.criar')) {
                $this->line('   └─ ❌ Criar Proposição (ERRO: não deveria aparecer!)');
            } else {
                $this->line('   └─ ✅ Criar Proposição (corretamente oculto)');
            }
            
            if ($this->canAccess('EXPEDIENTE', 'proposicoes.minhas-proposicoes')) {
                $this->line('   └─ ❌ Minhas Proposições (ERRO: não deveria aparecer!)');
            } else {
                $this->line('   └─ ✅ Minhas Proposições (corretamente oculto)');
            }
            
            // Submenu Expediente que DEVE aparecer
            $this->line('   └─ ✅ Expediente (submenu)');
            
            if ($this->canAccess('EXPEDIENTE', 'proposicoes.legislativo.index')) {
                $this->line('       └─ ✅ Proposições Protocoladas');
            } else {
                $this->line('       └─ ❌ Proposições Protocoladas (ERRO!)');
            }
            
            if ($this->canAccess('EXPEDIENTE', 'proposicoes.relatorio-legislativo')) {
                $this->line('       └─ ✅ Relatório');
            } else {
                $this->line('       └─ ❌ Relatório (ERRO!)');
            }
            
            if ($this->canAccess('EXPEDIENTE', 'proposicoes.aguardando-pauta')) {
                $this->line('       └─ ✅ Aguardando Pauta');
            } else {
                $this->line('       └─ ❌ Aguardando Pauta (ERRO!)');
            }
            
            if ($this->canAccess('EXPEDIENTE', 'proposicoes.gerenciar-pautas')) {
                $this->line('       └─ ✅ Gerenciar Pautas');
            } else {
                $this->line('       └─ ❌ Gerenciar Pautas (ERRO!)');
            }
            
        } else {
            $this->line('❌ Proposições');
        }
        
        // Sessões (deve aparecer - foco principal do EXPEDIENTE)
        if ($this->canAccessModule('EXPEDIENTE', 'sessoes')) {
            $this->line('✅ Sessões');
            
            if ($this->canAccess('EXPEDIENTE', 'admin.sessions.index')) {
                $this->line('   └─ ✅ Lista de Sessões');
            } else {
                $this->line('   └─ ❌ Lista de Sessões (ERRO!)');
            }
            
            if ($this->canAccess('EXPEDIENTE', 'sessoes.agenda')) {
                $this->line('   └─ ✅ Agenda');
            } else {
                $this->line('   └─ ❌ Agenda (ERRO!)');
            }
            
            if ($this->canAccess('EXPEDIENTE', 'sessoes.atas')) {
                $this->line('   └─ ✅ Atas');
            } else {
                $this->line('   └─ ❌ Atas (ERRO!)');
            }
            
        } else {
            $this->line('❌ Sessões (ERRO: deveria aparecer!)');
        }
        
        // Votações (deve estar oculto)
        if ($this->canAccessModule('EXPEDIENTE', 'votacoes')) {
            $this->line('❌ Votações (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Votações (corretamente oculto)');
        }
        
        // Comissões (deve estar oculto)
        if ($this->canAccessModule('EXPEDIENTE', 'comissoes')) {
            $this->line('❌ Comissões (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Comissões (corretamente oculto)');
        }
        
        // Administração (deve estar oculto)
        if ($this->canAccessModule('EXPEDIENTE', 'usuarios')) {
            $this->line('❌ Administração (ERRO: não deveria aparecer!)');
        } else {
            $this->line('✅ Administração (corretamente oculto)');
        }
        
        // Meu Perfil
        if ($this->canAccess('EXPEDIENTE', 'profile.edit')) {
            $this->line('✅ Meu Perfil');
        } else {
            $this->line('❌ Meu Perfil');
        }
        
        $this->newLine();
        $this->info('🎯 RESULTADO ESPERADO para EXPEDIENTE:');
        $this->line('• Dashboard');
        $this->line('• Proposições');
        $this->line('  └─ Expediente');
        $this->line('     ├─ Proposições Protocoladas');
        $this->line('     └─ Relatório');
        $this->line('• Sessões');
        $this->line('  ├─ Lista de Sessões');
        $this->line('  ├─ Agenda');
        $this->line('  └─ Atas');
        $this->line('• Meu Perfil');
        
        $this->newLine();
        $this->warn('❌ NÃO deve aparecer:');
        $this->line('• Parlamentares (não gerencia)');
        $this->line('• Partidos');
        $this->line('• Criar Proposição');
        $this->line('• Minhas Proposições');
        $this->line('• Assinatura');
        $this->line('• Protocolo (não protocola)');
        $this->line('• Votações (não gerencia)');
        $this->line('• Comissões (não gerencia)');
        $this->line('• Administração');
        
        $this->newLine();
        $this->info('🎯 FUNÇÃO DO EXPEDIENTE:');
        $this->line('Organizar pautas de sessões com proposições já protocoladas');
        
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