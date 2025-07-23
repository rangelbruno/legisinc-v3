<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Parlamentar;
use Illuminate\Console\Command;

class MigrateParlamentarUsers extends Command
{
    protected $signature = 'parlamentar:migrate-users';
    
    protected $description = 'Migra usuários com perfil PARLAMENTAR para a tabela parlamentars';

    public function handle()
    {
        $this->info('Iniciando migração de usuários parlamentares...');
        
        // Buscar usuários com perfil PARLAMENTAR que não têm registro na tabela parlamentars
        $parlamentarUsers = User::whereHas('roles', function ($query) {
            $query->where('name', User::PERFIL_PARLAMENTAR);
        })->whereDoesntHave('parlamentar')->get();
        
        $this->info("Encontrados {$parlamentarUsers->count()} usuários parlamentares para migrar.");
        
        $migratedCount = 0;
        
        foreach ($parlamentarUsers as $user) {
            try {
                Parlamentar::create([
                    'user_id' => $user->id,
                    'nome' => $user->name,
                    'email' => $user->email,
                    'cpf' => $user->documento,
                    'telefone' => $user->telefone,
                    'data_nascimento' => $user->data_nascimento,
                    'profissao' => $user->profissao,
                    'cargo' => $user->cargo_atual ?? 'Parlamentar',
                    'partido' => $user->partido,
                    'status' => 'ativo',
                ]);
                
                $this->line("✓ Migrado: {$user->name} ({$user->email})");
                $migratedCount++;
                
            } catch (\Exception $e) {
                $this->error("✗ Erro ao migrar {$user->name}: {$e->getMessage()}");
            }
        }
        
        $this->info("Migração concluída! {$migratedCount} usuários migrados com sucesso.");
        
        return 0;
    }
}
