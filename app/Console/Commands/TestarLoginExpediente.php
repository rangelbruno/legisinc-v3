<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class TestarLoginExpediente extends Command
{
    protected $signature = 'debug:testar-login-expediente';
    protected $description = 'Testa o processo de login do usuário EXPEDIENTE';

    public function handle()
    {
        $this->info('🔐 Testando login do usuário EXPEDIENTE');
        $this->newLine();

        // Buscar usuário
        $user = User::where('email', 'expediente@sistema.gov.br')->first();
        
        if (!$user) {
            $this->error('❌ Usuário não encontrado!');
            return 1;
        }

        $this->info("✅ Usuário encontrado:");
        $this->line("   ID: {$user->id}");
        $this->line("   Nome: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->newLine();

        // Testar senha
        $senhaCorreta = Hash::check('123456', $user->password);
        $this->info("🔑 Teste de senha '123456':");
        $this->line("   Resultado: " . ($senhaCorreta ? '✅ Correta' : '❌ Incorreta'));
        $this->newLine();

        // Simular login
        if ($senhaCorreta) {
            $this->info("🎯 Simulando login:");
            
            // Login manual para teste
            Auth::login($user);
            
            $authenticatedUser = Auth::user();
            if ($authenticatedUser) {
                $this->line("   ✅ Login realizado com sucesso");
                $this->line("   ID autenticado: {$authenticatedUser->id}");
                $this->line("   Nome autenticado: {$authenticatedUser->name}");
                $this->line("   Email autenticado: {$authenticatedUser->email}");
                
                // Verificar roles do usuário autenticado
                $roles = $authenticatedUser->getRoleNames();
                $this->line("   Roles: " . $roles->implode(', '));
                
                // Testar qual dashboard seria chamado
                $firstRole = $roles->first();
                $this->line("   Primeira role: {$firstRole}");
                
                switch ($firstRole) {
                    case 'EXPEDIENTE':
                        $dashboard = 'dashboardExpediente()';
                        break;
                    case 'PROTOCOLO':
                        $dashboard = 'dashboardProtocolo()';
                        break;
                    default:
                        $dashboard = 'dashboard padrão ou outro';
                }
                
                $this->line("   Dashboard que seria chamado: {$dashboard}");
                
                // Fazer logout
                Auth::logout();
                $this->line("   ✅ Logout realizado");
            } else {
                $this->error("   ❌ Falha no login");
            }
        }

        // Testar credenciais via Auth::attempt
        $this->newLine();
        $this->info("🔍 Testando Auth::attempt:");
        
        $credentials = [
            'email' => 'expediente@sistema.gov.br',
            'password' => '123456'
        ];
        
        try {
            if (Auth::attempt($credentials)) {
                $authUser = Auth::user();
                $this->line("   ✅ Auth::attempt funcionou");
                $this->line("   ID: {$authUser->id}");
                $this->line("   Nome: {$authUser->name}");
                $this->line("   Roles: " . $authUser->getRoleNames()->implode(', '));
                Auth::logout();
            } else {
                $this->error("   ❌ Auth::attempt falhou");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Erro no Auth::attempt: " . $e->getMessage());
        }

        return 0;
    }
}