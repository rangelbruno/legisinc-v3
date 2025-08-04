<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TestarLoginMockExpediente extends Command
{
    protected $signature = 'debug:testar-mock-expediente';
    protected $description = 'Testa o sistema mock de login do EXPEDIENTE';

    public function handle()
    {
        $this->info('🎭 Testando sistema mock para EXPEDIENTE');
        $this->newLine();

        // Simular o que acontece no AuthController mock
        $this->info('🔧 Simulando AuthController->attemptMockLogin()');
        
        $email = 'expediente@sistema.gov.br';
        $password = '123456';
        
        // Dados mock do AuthController
        $mockUsers = [
            'expediente@sistema.gov.br' => [
                'password' => '123456',
                'user' => [
                    'id' => 10,
                    'name' => 'Carlos Expediente',
                    'email' => 'expediente@sistema.gov.br',
                    'documento' => '444.444.444-44',
                    'telefone' => '(11) 9444-4444',
                    'profissao' => 'Servidor Público',
                    'cargo_atual' => 'Responsável pelo Expediente',
                    'ativo' => true,
                ]
            ],
        ];
        
        if (isset($mockUsers[$email]) && $password === $mockUsers[$email]['password']) {
            $this->line('✅ Credenciais mock encontradas');
            
            // Create a mock User instance (simulando o AuthController)
            $userData = $mockUsers[$email]['user'];
            $user = new User();
            foreach ($userData as $key => $value) {
                $user->{$key} = $value;
            }
            $user->exists = true;

            // Assign role (nova correção)
            $roleMapping = [
                'expediente@sistema.gov.br' => 'EXPEDIENTE',
            ];
            
            if (isset($roleMapping[$email])) {
                $user->roles = collect([(object)['name' => $roleMapping[$email]]]);
                $this->line("✅ Role atribuída: {$roleMapping[$email]}");
            }

            $this->newLine();
            $this->info('🧪 Testando métodos do usuário mock:');
            
            // Testar getRoleNames
            $roles = $user->getRoleNames();
            $this->line("   getRoleNames(): " . $roles->implode(', '));
            
            // Testar métodos específicos
            $this->line("   isExpediente(): " . ($user->isExpediente() ? '✅ true' : '❌ false'));
            $this->line("   isProtocolo(): " . ($user->isProtocolo() ? '✅ true' : '❌ false'));
            $this->line("   isAdmin(): " . ($user->isAdmin() ? '✅ true' : '❌ false'));
            
            // Testar qual dashboard seria chamado
            $firstRole = $roles->first();
            $this->line("   Primeira role: " . ($firstRole ?? 'null'));
            
            switch ($firstRole) {
                case 'EXPEDIENTE':
                    $dashboard = 'dashboardExpediente()';
                    break;
                case 'PROTOCOLO':
                    $dashboard = 'dashboardProtocolo()';
                    break;
                default:
                    $dashboard = 'dashboard padrão';
            }
            
            $this->line("   Dashboard: {$dashboard}");
            
            $this->newLine();
            $this->info('🎯 RESULTADO:');
            if ($firstRole === 'EXPEDIENTE') {
                $this->line('✅ Sistema mock corrigido com sucesso!');
                $this->line('✅ Usuário EXPEDIENTE será direcionado ao dashboard correto');
            } else {
                $this->error('❌ Sistema mock ainda não funcionando corretamente');
            }
        } else {
            $this->error('❌ Credenciais mock não encontradas');
        }

        return 0;
    }
}