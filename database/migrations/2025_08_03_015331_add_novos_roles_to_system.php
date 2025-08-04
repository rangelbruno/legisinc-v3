<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Criar novos roles usando Spatie Permission
        $roles = [
            ['name' => 'EXPEDIENTE', 'guard_name' => 'web'],
            ['name' => 'ASSESSOR_JURIDICO', 'guard_name' => 'web'],
        ];
        
        foreach ($roles as $role) {
            \Spatie\Permission\Models\Role::firstOrCreate($role);
        }
        
        // Criar permissões específicas para os novos roles
        $permissions = [
            // Expediente
            ['name' => 'expediente.view', 'guard_name' => 'web'],
            ['name' => 'expediente.create', 'guard_name' => 'web'],
            ['name' => 'expediente.edit', 'guard_name' => 'web'],
            ['name' => 'expediente.delete', 'guard_name' => 'web'],
            ['name' => 'pauta.view', 'guard_name' => 'web'],
            ['name' => 'pauta.create', 'guard_name' => 'web'],
            ['name' => 'pauta.edit', 'guard_name' => 'web'],
            ['name' => 'sessao.view', 'guard_name' => 'web'],
            ['name' => 'sessao.create', 'guard_name' => 'web'],
            ['name' => 'sessao.edit', 'guard_name' => 'web'],
            
            // Assessor Jurídico
            ['name' => 'parecer.view', 'guard_name' => 'web'],
            ['name' => 'parecer.create', 'guard_name' => 'web'],
            ['name' => 'parecer.edit', 'guard_name' => 'web'],
            ['name' => 'parecer.delete', 'guard_name' => 'web'],
            ['name' => 'juridico.view', 'guard_name' => 'web'],
            ['name' => 'juridico.create', 'guard_name' => 'web'],
            ['name' => 'juridico.edit', 'guard_name' => 'web'],
        ];
        
        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate($permission);
        }
        
        // Atribuir permissões aos roles
        $expedienteRole = \Spatie\Permission\Models\Role::where('name', 'EXPEDIENTE')->first();
        if ($expedienteRole) {
            $expedienteRole->givePermissionTo([
                'expediente.view', 'expediente.create', 'expediente.edit',
                'pauta.view', 'pauta.create', 'pauta.edit',
                'sessao.view', 'sessao.create', 'sessao.edit'
            ]);
        }
        
        $assessorRole = \Spatie\Permission\Models\Role::where('name', 'ASSESSOR_JURIDICO')->first();
        if ($assessorRole) {
            $assessorRole->givePermissionTo([
                'parecer.view', 'parecer.create', 'parecer.edit', 'parecer.delete',
                'juridico.view', 'juridico.create', 'juridico.edit'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover permissões dos roles antes de deletar
        $expedienteRole = \Spatie\Permission\Models\Role::where('name', 'EXPEDIENTE')->first();
        if ($expedienteRole) {
            $expedienteRole->revokePermissionTo([
                'expediente.view', 'expediente.create', 'expediente.edit',
                'pauta.view', 'pauta.create', 'pauta.edit',
                'sessao.view', 'sessao.create', 'sessao.edit'
            ]);
        }
        
        $assessorRole = \Spatie\Permission\Models\Role::where('name', 'ASSESSOR_JURIDICO')->first();
        if ($assessorRole) {
            $assessorRole->revokePermissionTo([
                'parecer.view', 'parecer.create', 'parecer.edit', 'parecer.delete',
                'juridico.view', 'juridico.create', 'juridico.edit'
            ]);
        }
        
        // Deletar roles
        \Spatie\Permission\Models\Role::where('name', 'EXPEDIENTE')->delete();
        \Spatie\Permission\Models\Role::where('name', 'ASSESSOR_JURIDICO')->delete();
        
        // Deletar permissões
        $permissionsToDelete = [
            'expediente.view', 'expediente.create', 'expediente.edit', 'expediente.delete',
            'pauta.view', 'pauta.create', 'pauta.edit',
            'sessao.view', 'sessao.create', 'sessao.edit',
            'parecer.view', 'parecer.create', 'parecer.edit', 'parecer.delete',
            'juridico.view', 'juridico.create', 'juridico.edit'
        ];
        
        \Spatie\Permission\Models\Permission::whereIn('name', $permissionsToDelete)->delete();
    }
};
