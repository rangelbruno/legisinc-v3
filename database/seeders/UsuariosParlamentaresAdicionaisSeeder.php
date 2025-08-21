<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsuariosParlamentaresAdicionaisSeeder extends Seeder
{
    public function run(): void
    {
        // Criar alguns usuários parlamentares sem cadastro de parlamentar vinculado
        $parlamentaresSemCadastro = [
            [
                'name' => 'Carlos Deputado Silva',
                'email' => 'carlos.deputado@camara.gov.br',
                'password' => Hash::make('123456'),
                'documento' => '123.456.789-01',
                'telefone' => '(11) 99999-1001',
                'data_nascimento' => '1975-03-15',
                'profissao' => 'Advogado',
                'partido' => 'PSDB',
                'cargo_atual' => 'Vereador',
                'ativo' => true,
            ],
            [
                'name' => 'Ana Vereadora Costa',
                'email' => 'ana.vereadora@camara.gov.br',
                'password' => Hash::make('123456'),
                'documento' => '987.654.321-02',
                'telefone' => '(11) 99999-1002',
                'data_nascimento' => '1982-07-22',
                'profissao' => 'Professora',
                'partido' => 'PT',
                'cargo_atual' => 'Vereadora',
                'ativo' => true,
            ],
            [
                'name' => 'Roberto Relator Souza',
                'email' => 'roberto.relator@camara.gov.br',
                'password' => Hash::make('123456'),
                'documento' => '456.789.123-03',
                'telefone' => '(11) 99999-1003',
                'data_nascimento' => '1968-12-10',
                'profissao' => 'Médico',
                'partido' => 'PMDB',
                'cargo_atual' => 'Relator',
                'ativo' => true,
            ],
        ];

        $parlamentarRole = Role::where('name', User::PERFIL_PARLAMENTAR)->first();
        $relatorRole = Role::where('name', User::PERFIL_RELATOR)->first();

        foreach ($parlamentaresSemCadastro as $index => $userData) {
            // Criar usuário
            $user = User::create($userData);
            
            // Atribuir role baseado no cargo
            if ($userData['cargo_atual'] === 'Relator') {
                $user->assignRole($relatorRole);
            } else {
                $user->assignRole($parlamentarRole);
            }
            
            // Nota: Propositalmente NÃO criar registro de parlamentar
            // para que estes usuários apareçam na lista de "usuários sem parlamentar"
        }

        $this->command->info('✅ Criados 3 usuários parlamentares sem cadastro de parlamentar vinculado');
    }
}