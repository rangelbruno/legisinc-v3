<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parlamentar;
use App\Models\User;

class ParlamentarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpar parlamentares existentes para garantir apenas um
        Parlamentar::truncate();
        
        // Buscar o usuário parlamentar Jessica Santos criado pelo SystemUsersSeeder
        $userParlamentar = User::where('email', 'jessica@sistema.gov.br')->first();
        
        if ($userParlamentar) {
            // Criar o cadastro de parlamentar para Jessica Santos
            $parlamentar = Parlamentar::create([
                'user_id' => $userParlamentar->id,
                'nome' => $userParlamentar->name,
                'nome_politico' => 'Jessica Santos',
                'partido' => $userParlamentar->partido ?? 'PT',
                'cargo' => $userParlamentar->cargo_atual ?? 'Vereadora',
                'status' => 'ativo',
                'email' => $userParlamentar->email,
                'cpf' => str_replace(['.', '-'], '', $userParlamentar->documento ?? '11111111111'),
                'telefone' => $userParlamentar->telefone ?? '(11) 9111-1111',
                'data_nascimento' => $userParlamentar->data_nascimento ?? '1985-03-15',
                'profissao' => $userParlamentar->profissao ?? 'Advogada',
                'escolaridade' => 'Superior Completo - Direito',
                'comissoes' => [
                    'Comissão de Constituição e Justiça',
                    'Comissão de Finanças e Orçamento',
                    'Comissão de Educação e Cultura'
                ],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ]);
            
            $this->command->info('');
            $this->command->info('✅ PARLAMENTAR CADASTRADA COM SUCESSO!');
            $this->command->info('================================================');
            $this->command->info("👤 Nome: {$parlamentar->nome}");
            $this->command->info("🏛️ Nome Político: {$parlamentar->nome_politico}");
            $this->command->info("🎯 Partido: {$parlamentar->partido}");
            $this->command->info("💼 Cargo: {$parlamentar->cargo}");
            $this->command->info("✅ Status: ATIVO");
            $this->command->info("📧 Email: {$parlamentar->email}");
            $this->command->info("📱 Telefone: {$parlamentar->telefone}");
            $this->command->info("👩‍⚖️ Profissão: {$parlamentar->profissao}");
            $this->command->info("🔑 Vinculado ao usuário ID: {$userParlamentar->id}");
            $this->command->info('');
            $this->command->info('🔐 CREDENCIAIS DE ACESSO:');
            $this->command->info('   Email: jessica@sistema.gov.br');
            $this->command->info('   Senha: 123456');
            $this->command->info('');
            $this->command->info('📌 Esta é a única parlamentar cadastrada no sistema.');
            $this->command->info('   Apenas ela pode criar e editar proposições.');
            $this->command->info('');
        } else {
            $this->command->error('');
            $this->command->error('❌ ERRO: Usuário jessica@sistema.gov.br não encontrado!');
            $this->command->error('   Execute primeiro: php artisan db:seed --class=SystemUsersSeeder');
            $this->command->error('');
        }
    }
}
