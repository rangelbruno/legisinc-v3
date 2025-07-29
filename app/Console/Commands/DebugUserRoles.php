<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class DebugUserRoles extends Command
{
    protected $signature = 'debug:user-roles {email}';
    protected $description = 'Debug user roles and permissions';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("User not found: {$email}");
            return;
        }
        
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("ID: {$user->id}");
        
        $this->info("\nRoles:");
        foreach ($user->roles as $role) {
            $this->line("- {$role->name} (ID: {$role->id})");
        }
        
        $this->info("\nMétodos de verificação:");
        $this->line("isAdmin(): " . ($user->isAdmin() ? 'true' : 'false'));
        $this->line("isLegislativo(): " . ($user->isLegislativo() ? 'true' : 'false'));
        $this->line("isParlamentar(): " . ($user->isParlamentar() ? 'true' : 'false'));
        
        $this->info("\nConstantes de perfil:");
        $this->line("PERFIL_ADMIN: " . User::PERFIL_ADMIN);
        $this->line("PERFIL_LEGISLATIVO: " . User::PERFIL_LEGISLATIVO);
        $this->line("PERFIL_PARLAMENTAR: " . User::PERFIL_PARLAMENTAR);
        
        $this->info("\nVerificação hasRole():");
        $this->line("hasRole('LEGISLATIVO'): " . ($user->hasRole('LEGISLATIVO') ? 'true' : 'false'));
        $this->line("hasRole('ADMIN'): " . ($user->hasRole('ADMIN') ? 'true' : 'false'));
        $this->line("hasRole('PARLAMENTAR'): " . ($user->hasRole('PARLAMENTAR') ? 'true' : 'false'));
    }
}