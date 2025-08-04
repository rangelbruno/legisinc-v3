<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CriarUsuarioExpediente extends Command
{
    protected $signature = 'user:criar-expediente';
    protected $description = 'Cria usuário de exemplo para perfil EXPEDIENTE';

    public function handle()
    {
        $this->info('👤 Criando usuário EXPEDIENTE...');

        // Verificar se role EXPEDIENTE existe
        $role = Role::where('name', 'EXPEDIENTE')->first();
        if (!$role) {
            $this->error('❌ Role EXPEDIENTE não encontrada. Execute primeiro o seeder de roles.');
            return 1;
        }

        // Criar usuário EXPEDIENTE
        $user = User::updateOrCreate(
            ['email' => 'expediente@sistema.gov.br'],
            [
                'name' => 'Carlos Expediente',
                'email' => 'expediente@sistema.gov.br',
                'password' => Hash::make('123456'),
                'email_verified_at' => now(),
            ]
        );

        // Atribuir role
        $user->syncRoles(['EXPEDIENTE']);

        $this->info('✅ Usuário EXPEDIENTE criado com sucesso!');
        $this->newLine();
        $this->info('📧 Email: expediente@sistema.gov.br');
        $this->info('🔑 Senha: 123456');
        $this->info('👥 Role: EXPEDIENTE');
        $this->newLine();
        $this->warn('💡 Use estas credenciais para testar o sistema de Expediente');

        return 0;
    }
}