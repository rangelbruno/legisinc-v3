<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BasicUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Criar usuário básico se não existir
        User::updateOrCreate(
            ['email' => 'bruno@sistema.gov.br'],
            [
                'id' => 5,
                'name' => 'Bruno Administrador',
                'email' => 'bruno@sistema.gov.br',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        // Criar usuário admin adicional se não existir
        User::updateOrCreate(
            ['email' => 'admin@sistema.gov.br'],
            [
                'name' => 'Administrador do Sistema',
                'email' => 'admin@sistema.gov.br',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Usuários básicos criados com sucesso!');
    }
}