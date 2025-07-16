<?php

namespace App\Console\Commands;

use App\Models\ScreenPermission;
use Illuminate\Console\Command;

class ClearDefaultPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:clear-defaults';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove todas as permissões padrão, forçando configuração explícita pelo administrador';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Limpando permissões padrão...');
            
            // Contar permissões antes
            $totalBefore = ScreenPermission::count();
            
            // Remover todas as permissões configuradas (exceto ADMIN se existir)
            ScreenPermission::where('role_name', '!=', 'ADMIN')->delete();
            
            // Contar após
            $totalAfter = ScreenPermission::count();
            $removed = $totalBefore - $totalAfter;
            
            $this->info("Removidas {$removed} permissões padrão.");
            $this->info('Agora apenas permissões explicitamente configuradas pelo administrador serão válidas.');
            $this->warn('Usuários não-admin só terão acesso ao Dashboard até que o administrador configure suas permissões.');
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Erro ao executar comando: ' . $e->getMessage());
            return 1;
        }
    }
}
