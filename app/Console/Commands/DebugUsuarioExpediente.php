<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DebugUsuarioExpediente extends Command
{
    protected $signature = 'debug:usuario-expediente';
    protected $description = 'Debug completo do usuário EXPEDIENTE';

    public function handle()
    {
        $this->info('🔍 Debug do usuário EXPEDIENTE');
        $this->newLine();

        // Buscar usuário
        $user = User::where('email', 'expediente@sistema.gov.br')->first();
        
        if (!$user) {
            $this->error('❌ Usuário expediente@sistema.gov.br não encontrado!');
            return 1;
        }

        $this->info("✅ Usuário encontrado:");
        $this->line("   ID: {$user->id}");
        $this->line("   Nome: {$user->name}");
        $this->line("   Email: {$user->email}");
        $this->newLine();

        // Verificar roles usando Spatie
        $this->info("🏷️  Roles via Spatie:");
        $roles = $user->getRoleNames();
        if ($roles->count() > 0) {
            foreach ($roles as $role) {
                $this->line("   ✅ {$role}");
            }
        } else {
            $this->error("   ❌ Nenhuma role encontrada via Spatie");
        }
        $this->newLine();

        // Verificar roles via query direta
        $this->info("🔍 Roles via query direta:");
        $rolesQuery = \DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('model_has_roles.model_id', $user->id)
            ->pluck('roles.name');
            
        if ($rolesQuery->count() > 0) {
            foreach ($rolesQuery as $role) {
                $this->line("   ✅ {$role}");
            }
        } else {
            $this->error("   ❌ Nenhuma role encontrada via query");
        }
        $this->newLine();

        // Testar métodos específicos
        $this->info("🛠️  Testando métodos de verificação:");
        $this->line("   isExpediente(): " . ($user->isExpediente() ? '✅ true' : '❌ false'));
        $this->line("   isProtocolo(): " . ($user->isProtocolo() ? '✅ true' : '❌ false'));
        $this->line("   isLegislativo(): " . ($user->isLegislativo() ? '✅ true' : '❌ false'));
        $this->line("   isParlamentar(): " . ($user->isParlamentar() ? '✅ true' : '❌ false'));
        $this->line("   isAdmin(): " . ($user->isAdmin() ? '✅ true' : '❌ false'));
        $this->newLine();

        // Testar qual seria o dashboard
        $firstRole = $user->getRoleNames()->first();
        $this->info("🎯 Dashboard que seria carregado:");
        $this->line("   Primeira role: " . ($firstRole ?? 'null'));
        
        switch ($firstRole) {
            case User::PERFIL_ADMIN:
                $dashboard = 'dashboardAdmin()';
                break;
            case User::PERFIL_PARLAMENTAR:
                $dashboard = 'dashboardParlamentar()';
                break;
            case User::PERFIL_LEGISLATIVO:
                $dashboard = 'dashboardLegislativo()';
                break;
            case User::PERFIL_PROTOCOLO:
                $dashboard = 'dashboardProtocolo()';
                break;
            case 'EXPEDIENTE':
                $dashboard = 'dashboardExpediente()';
                break;
            case 'ASSESSOR_JURIDICO':
                $dashboard = 'dashboardAssessorJuridico()';
                break;
            case User::PERFIL_RELATOR:
                $dashboard = 'dashboardRelator()';
                break;
            case User::PERFIL_ASSESSOR:
                $dashboard = 'dashboardAssessor()';
                break;
            case User::PERFIL_CIDADAO_VERIFICADO:
                $dashboard = 'dashboardCidadao()';
                break;
            case User::PERFIL_PUBLICO:
            default:
                $dashboard = 'dashboardPublico()';
        }
        
        $this->line("   Dashboard: {$dashboard}");
        $this->newLine();

        // Verificar constantes
        $this->info("📋 Constantes do User:");
        $this->line("   PERFIL_EXPEDIENTE: " . User::PERFIL_EXPEDIENTE);
        $this->line("   PERFIL_PROTOCOLO: " . User::PERFIL_PROTOCOLO);
        $this->newLine();

        // Comparar usuário Protocolo
        $protocoloUser = User::where('email', 'protocolo@camara.gov.br')->first();
        if ($protocoloUser) {
            $this->info("👥 Comparação com usuário Protocolo:");
            $this->line("   ID Protocolo: {$protocoloUser->id}");
            $this->line("   Nome Protocolo: {$protocoloUser->name}");
            $protocoloRoles = $protocoloUser->getRoleNames();
            $this->line("   Roles Protocolo: " . $protocoloRoles->implode(', '));
        }

        return 0;
    }
}