<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\ScreenPermission;

class ValidarSistemaExpediente extends Command
{
    protected $signature = 'expediente:validar-sistema';
    protected $description = 'Validação completa do sistema EXPEDIENTE';

    public function handle()
    {
        $this->info('🎯 VALIDAÇÃO COMPLETA DO SISTEMA EXPEDIENTE');
        $this->newLine();

        $allPassed = true;

        // 1. Verificar usuário no banco
        $this->info('1️⃣ Verificando usuário no banco de dados:');
        $user = User::where('email', 'expediente@sistema.gov.br')->first();
        if ($user && $user->name === 'Carlos Expediente') {
            $this->line('   ✅ Usuário encontrado no banco');
            $this->line("   ✅ ID: {$user->id}, Nome: {$user->name}");
        } else {
            $this->error('   ❌ Usuário não encontrado no banco');
            $allPassed = false;
        }

        // 2. Verificar roles
        $this->info('2️⃣ Verificando roles:');
        if ($user) {
            $roles = $user->getRoleNames();
            if ($roles->contains('EXPEDIENTE')) {
                $this->line('   ✅ Role EXPEDIENTE atribuída');
            } else {
                $this->error('   ❌ Role EXPEDIENTE não encontrada: ' . $roles->implode(', '));
                $allPassed = false;
            }
        }

        // 3. Verificar permissões de tela
        $this->info('3️⃣ Verificando permissões de tela:');
        $expedientePermissions = ScreenPermission::where('role_name', 'EXPEDIENTE')
            ->where('can_access', true)
            ->count();
        
        if ($expedientePermissions >= 30) {
            $this->line("   ✅ {$expedientePermissions} permissões configuradas");
        } else {
            $this->error("   ❌ Apenas {$expedientePermissions} permissões encontradas (mínimo: 30)");
            $allPassed = false;
        }

        // 4. Verificar permissões específicas do módulo expediente
        $this->info('4️⃣ Verificando módulo expediente:');
        $modulePermissions = ScreenPermission::where('role_name', 'EXPEDIENTE')
            ->where('screen_module', 'expediente')
            ->where('can_access', true)
            ->count();
            
        if ($modulePermissions >= 7) {
            $this->line("   ✅ {$modulePermissions} permissões do módulo expediente");
        } else {
            $this->error("   ❌ Apenas {$modulePermissions} permissões do módulo expediente");
            $allPassed = false;
        }

        // 5. Verificar controller e rotas
        $this->info('5️⃣ Verificando controller ExpedienteController:');
        if (class_exists('App\Http\Controllers\ExpedienteController')) {
            $this->line('   ✅ ExpedienteController existe');
        } else {
            $this->error('   ❌ ExpedienteController não encontrado');
            $allPassed = false;
        }

        // 6. Verificar DashboardController
        $this->info('6️⃣ Verificando DashboardController:');
        if (method_exists('App\Http\Controllers\DashboardController', 'dashboardExpediente')) {
            $this->line('   ✅ Método dashboardExpediente() existe');
        } else {
            $this->error('   ❌ Método dashboardExpediente() não encontrado');
            $allPassed = false;
        }

        // 7. Verificar sistema mock
        $this->info('7️⃣ Verificando sistema mock (AuthController):');
        try {
            $user_mock = new User();
            $user_mock->email = 'expediente@sistema.gov.br';
            $user_mock->name = 'Carlos Expediente';
            $user_mock->cargo_atual = 'Responsável pelo Expediente';
            $user_mock->roles = collect([(object)['name' => 'EXPEDIENTE']]);
            
            $roles = $user_mock->getRoleNames();
            if ($roles->contains('EXPEDIENTE')) {
                $this->line('   ✅ Sistema mock funcional');
            } else {
                $this->error('   ❌ Sistema mock não funcional');
                $allPassed = false;
            }
        } catch (\Exception $e) {
            $this->error('   ❌ Erro no sistema mock: ' . $e->getMessage());
            $allPassed = false;
        }

        // 8. Resultado final
        $this->newLine();
        $this->info('🏁 RESULTADO FINAL:');
        
        if ($allPassed) {
            $this->line('✅ SISTEMA EXPEDIENTE FUNCIONANDO CORRETAMENTE!');
            $this->newLine();
            $this->warn('💡 Para testar:');
            $this->line('1. Acesse a tela de login');
            $this->line('2. Use: expediente@sistema.gov.br / 123456'); 
            $this->line('3. O usuário deve ser direcionado ao dashboard do Expediente');
            $this->line('4. O menu "Expediente" deve aparecer no aside');
            $this->line('5. Deve conseguir acessar todas as telas de proposições');
        } else {
            $this->error('❌ Há problemas no sistema que precisam ser corrigidos');
        }

        return $allPassed ? 0 : 1;
    }
}