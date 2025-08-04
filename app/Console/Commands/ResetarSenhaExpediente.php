<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetarSenhaExpediente extends Command
{
    protected $signature = 'expediente:resetar-senha';
    protected $description = 'Reseta a senha do usuário EXPEDIENTE para 123456';

    public function handle()
    {
        $user = User::where('name', 'Carlos Expediente')->first();
        
        if (!$user) {
            $this->error('❌ Usuário Carlos Expediente não encontrado');
            return 1;
        }

        $user->password = Hash::make('123456');
        $user->save();

        $this->info('✅ Senha do usuário EXPEDIENTE resetada com sucesso!');
        $this->newLine();
        $this->info('📧 Email: expediente@sistema.gov.br');
        $this->info('🔑 Senha: 123456');
        $this->newLine();
        $this->warn('💡 Faça login com essas credenciais para ver o menu do Expediente');

        return 0;
    }
}