<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestarTodosUsuarios extends Command
{
    protected $signature = 'debug:testar-todos-usuarios';
    protected $description = 'Testa login de todos os usuários com senha 123456';

    public function handle()
    {
        $this->info('🔐 Testando autenticação de todos os usuários com senha 123456');
        $this->newLine();

        $users = User::with('roles')->get();
        
        foreach ($users as $user) {
            $this->info("👤 {$user->name} ({$user->email}):");
            
            // Testar senha
            $senhaCorreta = Hash::check('123456', $user->password);
            $this->line("   Senha 123456: " . ($senhaCorreta ? '✅ Correta' : '❌ Incorreta'));
            
            // Mostrar roles
            $roles = $user->getRoleNames();
            $this->line("   Roles: " . $roles->implode(', '));
            
            // Testar dashboard
            $firstRole = $roles->first();
            switch ($firstRole) {
                case 'ADMIN':
                    $dashboard = 'dashboardAdmin()';
                    break;
                case 'PARLAMENTAR':
                    $dashboard = 'dashboardParlamentar()';
                    break;
                case 'LEGISLATIVO':
                    $dashboard = 'dashboardLegislativo()';
                    break;
                case 'PROTOCOLO':
                    $dashboard = 'dashboardProtocolo()';
                    break;
                case 'EXPEDIENTE':
                    $dashboard = 'dashboardExpediente()';
                    break;
                case 'ASSESSOR_JURIDICO':
                    $dashboard = 'dashboardAssessorJuridico()';
                    break;
                default:
                    $dashboard = 'dashboardPublico()';
            }
            
            $this->line("   Dashboard: {$dashboard}");
            $this->newLine();
        }
        
        $this->info('🎯 RESUMO:');
        $totalUsers = $users->count();
        $usersWithCorrectPassword = $users->filter(function($user) {
            return Hash::check('123456', $user->password);
        })->count();
        
        $this->line("Total de usuários: {$totalUsers}");
        $this->line("Com senha 123456: {$usersWithCorrectPassword}");
        
        if ($usersWithCorrectPassword === $totalUsers) {
            $this->line('✅ Todos os usuários têm senha 123456');
        } else {
            $this->error('❌ Alguns usuários não têm senha 123456');
        }

        return 0;
    }
}