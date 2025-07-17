<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoParametro;

class TipoParametroSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nome' => 'Texto',
                'codigo' => 'string',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'max_length' => 255,
                    'min_length' => 0,
                    'regex' => null
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Texto Longo',
                'codigo' => 'text',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'max_length' => 65535,
                    'min_length' => 0
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Número Inteiro',
                'codigo' => 'integer',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'min' => null,
                    'max' => null,
                    'step' => 1
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Número Decimal',
                'codigo' => 'decimal',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'min' => null,
                    'max' => null,
                    'step' => 0.01,
                    'precision' => 2
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Sim/Não',
                'codigo' => 'boolean',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'default_value' => false,
                    'true_label' => 'Sim',
                    'false_label' => 'Não'
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Data',
                'codigo' => 'date',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'format' => 'Y-m-d',
                    'min_date' => null,
                    'max_date' => null
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Data e Hora',
                'codigo' => 'datetime',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'format' => 'Y-m-d H:i:s',
                    'min_date' => null,
                    'max_date' => null
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Hora',
                'codigo' => 'time',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'format' => 'H:i:s',
                    'min_time' => null,
                    'max_time' => null
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Email',
                'codigo' => 'email',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'multiple' => false,
                    'domains' => []
                ],
                'ativo' => true
            ],
            [
                'nome' => 'URL',
                'codigo' => 'url',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'allowed_protocols' => ['http', 'https'],
                    'allow_ftp' => false
                ],
                'ativo' => true
            ],
            [
                'nome' => 'JSON',
                'codigo' => 'json',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'validate_structure' => false,
                    'required_keys' => []
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Array',
                'codigo' => 'array',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'separator' => ',',
                    'trim_values' => true,
                    'remove_empty' => true
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Cor',
                'codigo' => 'color',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'format' => 'hex',
                    'allow_alpha' => false
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Lista de Opções',
                'codigo' => 'enum',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'options' => [],
                    'multiple' => false
                ],
                'ativo' => true
            ],
            [
                'nome' => 'Senha',
                'codigo' => 'password',
                'classe_validacao' => null,
                'configuracao_padrao' => [
                    'min_length' => 8,
                    'require_uppercase' => true,
                    'require_lowercase' => true,
                    'require_numbers' => true,
                    'require_symbols' => false
                ],
                'ativo' => true
            ]
        ];

        foreach ($tipos as $tipo) {
            TipoParametro::updateOrCreate(
                ['codigo' => $tipo['codigo']],
                $tipo
            );
        }

        $this->command->info('Tipos de parâmetros criados com sucesso!');
    }
}