<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class EnsureRolesExist extends Command
{
    protected $signature = 'roles:ensure';
    protected $description = 'Ensure all required roles exist in the database';

    public function handle()
    {
        $this->info('Checking and creating required roles...');

        // Roles necessários
        $requiredRoles = [
            User::PERFIL_ADMIN => 'Administrador do Sistema',
            User::PERFIL_ASSESSOR_JURIDICO => 'Assessor Jurídico',
            User::PERFIL_LEGISLATIVO => 'Servidor Legislativo',
            User::PERFIL_EXPEDIENTE => 'Expediente',
            User::PERFIL_PARLAMENTAR => 'Parlamentar',
            User::PERFIL_RELATOR => 'Relator',
            User::PERFIL_PROTOCOLO => 'Protocolo',
            User::PERFIL_ASSESSOR => 'Assessor',
            User::PERFIL_CIDADAO_VERIFICADO => 'Cidadão Verificado',
            User::PERFIL_PUBLICO => 'Público',
        ];

        foreach ($requiredRoles as $roleName => $description) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);

            if ($role->wasRecentlyCreated) {
                $this->info("✓ Role '{$roleName}' created successfully");
            } else {
                $this->line("- Role '{$roleName}' already exists");
            }
        }

        $this->info('All required roles are now available!');
        
        // Listar roles existentes
        $this->info("\nCurrent roles in database:");
        $roles = Role::pluck('name')->toArray();
        foreach ($roles as $role) {
            $this->line("  - {$role}");
        }

        return 0;
    }
}