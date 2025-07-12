<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parlamentar;

class ParlamentarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parlamentares = [
            [
                'nome' => 'João Silva Santos',
                'partido' => 'PT',
                'status' => 'ativo',
                'cargo' => 'Vereador',
                'telefone' => '(11) 98765-4321',
                'email' => 'joao.silva@camara.gov.br',
                'data_nascimento' => '1975-03-15',
                'profissao' => 'Advogado',
                'escolaridade' => 'Superior Completo',
                'comissoes' => ['Educação', 'Saúde'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Maria Santos Oliveira',
                'partido' => 'PSDB',
                'status' => 'ativo',
                'cargo' => 'Vereadora',
                'telefone' => '(11) 97654-3210',
                'email' => 'maria.santos@camara.gov.br',
                'data_nascimento' => '1980-07-22',
                'profissao' => 'Professora',
                'escolaridade' => 'Pós-Graduação',
                'comissoes' => ['Educação', 'Cultura'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Carlos Eduardo Pereira',
                'partido' => 'MDB',
                'status' => 'ativo',
                'cargo' => 'Presidente da Câmara',
                'telefone' => '(11) 96543-2109',
                'email' => 'carlos.pereira@camara.gov.br',
                'data_nascimento' => '1965-11-08',
                'profissao' => 'Empresário',
                'escolaridade' => 'Superior Completo',
                'comissoes' => ['Mesa Diretora', 'Finanças'],
                'mandatos' => [
                    ['ano_inicio' => 2017, 'ano_fim' => 2020, 'status' => 'anterior'],
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Ana Paula Costa',
                'partido' => 'PSL',
                'status' => 'licenciada',
                'cargo' => 'Vereadora',
                'telefone' => '(11) 95432-1098',
                'email' => 'ana.costa@camara.gov.br',
                'data_nascimento' => '1988-02-14',
                'profissao' => 'Médica',
                'escolaridade' => 'Pós-Graduação',
                'comissoes' => ['Saúde'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Roberto Mendes Lima',
                'partido' => 'PDT',
                'status' => 'ativo',
                'cargo' => 'Vice-Presidente',
                'telefone' => '(11) 94321-0987',
                'email' => 'roberto.mendes@camara.gov.br',
                'data_nascimento' => '1972-09-30',
                'profissao' => 'Engenheiro',
                'escolaridade' => 'Superior Completo',
                'comissoes' => ['Mesa Diretora', 'Obras'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Fernanda Alves Rodrigues',
                'partido' => 'PP',
                'status' => 'ativo',
                'cargo' => '1º Secretário',
                'telefone' => '(11) 93210-8765',
                'email' => 'fernanda.alves@camara.gov.br',
                'data_nascimento' => '1983-05-12',
                'profissao' => 'Jornalista',
                'escolaridade' => 'Superior Completo',
                'comissoes' => ['Mesa Diretora', 'Comunicação'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'José Antonio Silva',
                'partido' => 'PSOL',
                'status' => 'ativo',
                'cargo' => 'Vereador',
                'telefone' => '(11) 92109-7654',
                'email' => 'jose.antonio@camara.gov.br',
                'data_nascimento' => '1970-12-03',
                'profissao' => 'Metalúrgico',
                'escolaridade' => 'Ensino Médio',
                'comissoes' => ['Trabalho', 'Direitos Humanos'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Claudia Regina Sousa',
                'partido' => 'DEM',
                'status' => 'ativo',
                'cargo' => '2º Secretário',
                'telefone' => '(11) 91098-6543',
                'email' => 'claudia.regina@camara.gov.br',
                'data_nascimento' => '1976-08-19',
                'profissao' => 'Contadora',
                'escolaridade' => 'Superior Completo',
                'comissoes' => ['Mesa Diretora', 'Finanças'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Eduardo Santos Ferreira',
                'partido' => 'PL',
                'status' => 'ativo',
                'cargo' => 'Vereador',
                'telefone' => '(11) 90987-5432',
                'email' => 'eduardo.santos@camara.gov.br',
                'data_nascimento' => '1985-04-25',
                'profissao' => 'Comerciante',
                'escolaridade' => 'Superior Incompleto',
                'comissoes' => ['Desenvolvimento Econômico'],
                'mandatos' => [
                    ['ano_inicio' => 2021, 'ano_fim' => 2024, 'status' => 'atual']
                ]
            ],
            [
                'nome' => 'Patricia Lima Nascimento',
                'partido' => 'PCdoB',
                'status' => 'inativo',
                'cargo' => 'Vereadora',
                'telefone' => '(11) 98876-4321',
                'email' => 'patricia.lima@camara.gov.br',
                'data_nascimento' => '1979-01-07',
                'profissao' => 'Assistente Social',
                'escolaridade' => 'Pós-Graduação',
                'comissoes' => ['Assistência Social'],
                'mandatos' => [
                    ['ano_inicio' => 2017, 'ano_fim' => 2020, 'status' => 'anterior']
                ]
            ]
        ];

        foreach ($parlamentares as $parlamentar) {
            Parlamentar::create($parlamentar);
        }
    }
}
