<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Verificar se já existe um usuário admin
        $admin = User::where('email', 'admin@admin.com')->first();

        if (!$admin) {
            // Criar usuário admin
            $admin = User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'password' => Hash::make('password'),
                'ativo' => true,
                'ultimo_acesso' => now(),
            ]);
            
            echo "Usuário admin criado com sucesso!\n";
        } else {
            echo "Usuário admin já existe\n";
        }

        echo "Email: admin@admin.com - Senha: password\n";
    }
}