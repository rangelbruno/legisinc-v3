<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ScreenPermission;
use Illuminate\Support\Facades\Auth;

class DebugExpedienteMenu extends Command
{
    protected $signature = 'debug:expediente-menu';
    protected $description = 'Debug do menu do usuário EXPEDIENTE';

    public function handle()
    {
        $this->info('🔍 DEBUG MENU EXPEDIENTE');
        
        // Encontrar usuário expediente
        $user = User::where('email', 'expediente@sistema.gov.br')->first();
        
        if (!$user) {
            $this->error('❌ Usuário expediente@sistema.gov.br não encontrado!');
            return 1;
        }
        
        $this->line("👤 Usuário: {$user->name} ({$user->email})");
        $this->line("🎭 Roles: " . $user->roles->pluck('name')->implode(', '));
        
        // Fazer login como o usuário
        Auth::login($user);
        $this->line("✅ Logado como: " . Auth::user()->name);
        
        $this->line('');
        $this->info('🔍 TESTANDO CONDIÇÕES DO MENU:');
        
        // Testar condição principal do módulo proposições
        $canAccessProposicoes = ScreenPermission::userCanAccessModule('proposicoes');
        $this->line("1. userCanAccessModule('proposicoes'): " . ($canAccessProposicoes ? '✅ SIM' : '❌ NÃO'));
        
        // Testar condição do submenu expediente
        $canAccessExpedienteModule = ScreenPermission::userCanAccessModule('expediente');
        $canAccessExpedienteRoute = ScreenPermission::userCanAccessRoute('expediente.index');
        
        $this->line("2. userCanAccessModule('expediente'): " . ($canAccessExpedienteModule ? '✅ SIM' : '❌ NÃO'));
        $this->line("3. userCanAccessRoute('expediente.index'): " . ($canAccessExpedienteRoute ? '✅ SIM' : '❌ NÃO'));
        
        // Condição final do submenu (OR logic)
        $showExpedienteSubmenu = $canAccessExpedienteModule || $canAccessExpedienteRoute;
        $this->line("4. Mostrar submenu Expediente: " . ($showExpedienteSubmenu ? '✅ SIM' : '❌ NÃO'));
        
        $this->line('');
        $this->info('🔍 TESTANDO ROTAS ESPECÍFICAS:');
        
        $routes = [
            'expediente.index' => 'Painel do Expediente',
            'proposicoes.legislativo.index' => 'Proposições Protocoladas',
            'expediente.aguardando-pauta' => 'Aguardando Pauta',
            'expediente.relatorio' => 'Relatório',
            'proposicoes.aguardando-pauta' => 'Aguardando Pauta (Antigo)'
        ];
        
        foreach ($routes as $route => $name) {
            $canAccess = ScreenPermission::userCanAccessRoute($route);
            $this->line("   {$route}: " . ($canAccess ? '✅ SIM' : '❌ NÃO') . " - {$name}");
        }
        
        $this->line('');
        
        // Verificar se há outras condições que podem estar bloqueando
        $this->info('🔍 VERIFICANDO CONDIÇÕES BLADE:');
        
        // Simular as condições exatas do aside.blade.php
        $showProposicoesMenu = ScreenPermission::userCanAccessModule('proposicoes');
        $this->line("@if(userCanAccessModule('proposicoes')): " . ($showProposicoesMenu ? '✅ TRUE' : '❌ FALSE'));
        
        $showExpedienteSubmenuBlade = ScreenPermission::userCanAccessModule('expediente') || ScreenPermission::userCanAccessRoute('expediente.index');
        $this->line("@if(userCanAccessModule('expediente') || userCanAccessRoute('expediente.index')): " . ($showExpedienteSubmenuBlade ? '✅ TRUE' : '❌ FALSE'));
        
        if (!$showProposicoesMenu) {
            $this->error('❌ PROBLEMA: Menu Proposições não aparece porque userCanAccessModule("proposicoes") retorna FALSE');
        }
        
        if (!$showExpedienteSubmenuBlade) {
            $this->error('❌ PROBLEMA: Submenu Expediente não aparece porque as condições Blade retornam FALSE');
        }
        
        if ($showProposicoesMenu && $showExpedienteSubmenuBlade) {
            $this->info('✅ CONDIÇÕES OK: O submenu deveria aparecer!');
            $this->warn('💡 Se não está aparecendo, pode ser cache do navegador ou sessão.');
        }
        
        return 0;
    }
}