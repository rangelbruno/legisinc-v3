<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParametrosModulosFixedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Garante que os módulos de parâmetros sejam criados com IDs fixos
     */
    public function run(): void
    {
        // Limpar tabelas relacionadas para evitar conflitos
        // Para PostgreSQL, usar TRUNCATE com CASCADE
        DB::statement('TRUNCATE TABLE parametros_modulos CASCADE');

        // Criar módulos com IDs fixos
        $modulos = [
            [
                'id' => 1,
                'nome' => 'Dados Gerais',
                'descricao' => 'Configurações gerais da Câmara Municipal',
                'icon' => 'ki-bank',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'nome' => 'Templates',
                'descricao' => 'Configurações de templates de documentos',
                'icon' => 'ki-document',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'nome' => 'IA',
                'descricao' => 'Configurações de Inteligência Artificial',
                'icon' => 'ki-brain',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('parametros_modulos')->insert($modulos);

        // Criar submódulos para Templates (ID 2)
        $submodulosTemplates = [
            [
                'modulo_id' => 2,
                'nome' => 'Cabeçalho',
                'descricao' => 'Configurações do cabeçalho dos documentos',
                'tipo' => 'form',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 2,
                'nome' => 'Rodapé',
                'descricao' => 'Configurações do rodapé dos documentos',
                'tipo' => 'form',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 2,
                'nome' => 'Variáveis Dinâmicas',
                'descricao' => 'Variáveis dinâmicas disponíveis nos templates',
                'tipo' => 'form',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 2,
                'nome' => 'Formatação',
                'descricao' => 'Configurações de formatação dos documentos',
                'tipo' => 'form',
                'ordem' => 4,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('parametros_submodulos')->insert($submodulosTemplates);

        // Criar submódulos para Dados Gerais (ID 1)
        $submodulosDadosGerais = [
            [
                'modulo_id' => 1,
                'nome' => 'Informações da Câmara',
                'descricao' => 'Dados institucionais da Câmara Municipal',
                'tipo' => 'form',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 1,
                'nome' => 'Endereço',
                'descricao' => 'Endereço completo da Câmara',
                'tipo' => 'form',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 1,
                'nome' => 'Contatos',
                'descricao' => 'Informações de contato',
                'tipo' => 'form',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 1,
                'nome' => 'Gestão Atual',
                'descricao' => 'Informações sobre a gestão atual',
                'tipo' => 'form',
                'ordem' => 4,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('parametros_submodulos')->insert($submodulosDadosGerais);

        // Criar submódulos para IA (ID 3)
        $submodulosIA = [
            [
                'modulo_id' => 3,
                'nome' => 'Provedores',
                'descricao' => 'Configurações dos provedores de IA',
                'tipo' => 'custom',
                'ordem' => 1,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 3,
                'nome' => 'Modelos',
                'descricao' => 'Configurações dos modelos de IA',
                'tipo' => 'custom',
                'ordem' => 2,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'modulo_id' => 3,
                'nome' => 'Preferências',
                'descricao' => 'Preferências gerais de IA',
                'tipo' => 'form',
                'ordem' => 3,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('parametros_submodulos')->insert($submodulosIA);

        $this->command->info('✅ Módulos de parâmetros criados com IDs fixos:');
        $this->command->info('   - ID 1: Dados Gerais');
        $this->command->info('   - ID 2: Templates (com 4 submódulos)');
        $this->command->info('   - ID 3: IA');
    }
}