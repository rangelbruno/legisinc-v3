<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDadosGeraisCamposSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar o módulo Dados Gerais
        $modulo = DB::table('parametros_modulos')
            ->where('nome', 'Dados Gerais')
            ->first();

        if (!$modulo) {
            $this->command->error('Módulo Dados Gerais não encontrado');
            return;
        }

        // Buscar os submódulos existentes
        $submodulos = DB::table('parametros_submodulos')
            ->where('modulo_id', $modulo->id)
            ->get()
            ->keyBy('nome');

        // Criar campos para cada submódulo
        $this->criarCamposIdentificacao($submodulos->get('Informações da Câmara'));
        $this->criarCamposEndereco($submodulos->get('Endereço'));
        $this->criarCamposContatos($submodulos->get('Contatos'));
        $this->criarCamposGestao($submodulos->get('Gestão Atual'));

        $this->command->info("✅ Campos do módulo 'Dados Gerais' criados com sucesso!");
    }

    private function criarCamposIdentificacao($submodulo)
    {
        if (!$submodulo) {
            $this->command->warn('Submódulo Informações da Câmara não encontrado');
            return;
        }

        $campos = [
            [
                'nome' => 'nome_camara',
                'label' => 'Nome da Câmara',
                'tipo_campo' => 'text',
                'descricao' => 'Nome completo da Câmara Municipal',
                'obrigatorio' => true,
                'valor_padrao' => 'Câmara Municipal',
                'placeholder' => 'Ex: Câmara Municipal de São Paulo',
                'ordem' => 1
            ],
            [
                'nome' => 'sigla_camara',
                'label' => 'Sigla',
                'tipo_campo' => 'text',
                'descricao' => 'Sigla da Câmara',
                'obrigatorio' => true,
                'valor_padrao' => 'CM',
                'placeholder' => 'Ex: CMSP',
                'ordem' => 2
            ],
            [
                'nome' => 'cnpj',
                'label' => 'CNPJ',
                'tipo_campo' => 'text',
                'descricao' => 'CNPJ da Câmara Municipal',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: 00.000.000/0001-00',
                'ordem' => 3
            ]
        ];

        $this->inserirCampos($submodulo->id, $campos);
    }

    private function criarCamposEndereco($submodulo)
    {
        if (!$submodulo) {
            $this->command->warn('Submódulo Endereço não encontrado');
            return;
        }

        $campos = [
            [
                'nome' => 'endereco',
                'label' => 'Endereço',
                'tipo_campo' => 'text',
                'descricao' => 'Logradouro completo',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: Rua das Flores',
                'ordem' => 1
            ],
            [
                'nome' => 'numero',
                'label' => 'Número',
                'tipo_campo' => 'text',
                'descricao' => 'Número do endereço',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: 123',
                'ordem' => 2
            ],
            [
                'nome' => 'complemento',
                'label' => 'Complemento',
                'tipo_campo' => 'text',
                'descricao' => 'Complemento do endereço',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: Sala 201',
                'ordem' => 3
            ],
            [
                'nome' => 'bairro',
                'label' => 'Bairro',
                'tipo_campo' => 'text',
                'descricao' => 'Bairro',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: Centro',
                'ordem' => 4
            ],
            [
                'nome' => 'cidade',
                'label' => 'Cidade',
                'tipo_campo' => 'text',
                'descricao' => 'Cidade',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: São Paulo',
                'ordem' => 5
            ],
            [
                'nome' => 'estado',
                'label' => 'Estado',
                'tipo_campo' => 'text',
                'descricao' => 'Estado (UF)',
                'obrigatorio' => true,
                'valor_padrao' => 'SP',
                'placeholder' => 'Ex: SP',
                'ordem' => 6
            ],
            [
                'nome' => 'cep',
                'label' => 'CEP',
                'tipo_campo' => 'text',
                'descricao' => 'CEP',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: 00000-000',
                'ordem' => 7
            ]
        ];

        $this->inserirCampos($submodulo->id, $campos);
    }

    private function criarCamposContatos($submodulo)
    {
        if (!$submodulo) {
            $this->command->warn('Submódulo Contatos não encontrado');
            return;
        }

        $campos = [
            [
                'nome' => 'telefone',
                'label' => 'Telefone Principal',
                'tipo_campo' => 'text',
                'descricao' => 'Telefone principal da Câmara',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: (11) 0000-0000',
                'ordem' => 1
            ],
            [
                'nome' => 'telefone_secundario',
                'label' => 'Telefone Secundário',
                'tipo_campo' => 'text',
                'descricao' => 'Telefone secundário',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: (11) 0000-0000',
                'ordem' => 2
            ],
            [
                'nome' => 'email_institucional',
                'label' => 'E-mail Institucional',
                'tipo_campo' => 'email',
                'descricao' => 'E-mail principal da Câmara',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: contato@camara.gov.br',
                'ordem' => 3
            ],
            [
                'nome' => 'email_contato',
                'label' => 'E-mail de Contato',
                'tipo_campo' => 'email',
                'descricao' => 'E-mail secundário de contato',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: protocolo@camara.gov.br',
                'ordem' => 4
            ],
            [
                'nome' => 'website',
                'label' => 'Website',
                'tipo_campo' => 'text',
                'descricao' => 'Site oficial da Câmara',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: www.camara.gov.br',
                'ordem' => 5
            ],
            [
                'nome' => 'horario_funcionamento',
                'label' => 'Horário de Funcionamento',
                'tipo_campo' => 'text',
                'descricao' => 'Horário de funcionamento geral',
                'obrigatorio' => true,
                'valor_padrao' => 'Segunda a Sexta, 8h às 17h',
                'placeholder' => 'Ex: Segunda a Sexta, 8h às 17h',
                'ordem' => 6
            ],
            [
                'nome' => 'horario_atendimento',
                'label' => 'Horário de Atendimento',
                'tipo_campo' => 'text',
                'descricao' => 'Horário de atendimento ao público',
                'obrigatorio' => true,
                'valor_padrao' => 'Segunda a Sexta, 8h às 16h',
                'placeholder' => 'Ex: Segunda a Sexta, 8h às 16h',
                'ordem' => 7
            ]
        ];

        $this->inserirCampos($submodulo->id, $campos);
    }

    private function criarCamposGestao($submodulo)
    {
        if (!$submodulo) {
            $this->command->warn('Submódulo Gestão Atual não encontrado');
            return;
        }

        $campos = [
            [
                'nome' => 'presidente_nome',
                'label' => 'Nome do Presidente',
                'tipo_campo' => 'text',
                'descricao' => 'Nome completo do Presidente da Câmara',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: João Silva',
                'ordem' => 1
            ],
            [
                'nome' => 'presidente_partido',
                'label' => 'Partido do Presidente',
                'tipo_campo' => 'text',
                'descricao' => 'Partido político do Presidente',
                'obrigatorio' => true,
                'valor_padrao' => '',
                'placeholder' => 'Ex: PSDB',
                'ordem' => 2
            ],
            [
                'nome' => 'legislatura_atual',
                'label' => 'Legislatura Atual',
                'tipo_campo' => 'text',
                'descricao' => 'Período da legislatura atual',
                'obrigatorio' => true,
                'valor_padrao' => '2021-2024',
                'placeholder' => 'Ex: 2021-2024',
                'ordem' => 3
            ],
            [
                'nome' => 'numero_vereadores',
                'label' => 'Número de Vereadores',
                'tipo_campo' => 'number',
                'descricao' => 'Quantidade total de vereadores',
                'obrigatorio' => true,
                'valor_padrao' => '9',
                'placeholder' => 'Ex: 9',
                'ordem' => 4
            ]
        ];

        $this->inserirCampos($submodulo->id, $campos);
    }

    private function inserirCampos($submoduloId, $campos)
    {
        foreach ($campos as $campo) {
            // Verificar se o campo já existe
            $existe = DB::table('parametros_campos')
                ->where('submodulo_id', $submoduloId)
                ->where('nome', $campo['nome'])
                ->exists();

            if (!$existe) {
                DB::table('parametros_campos')->insert(array_merge($campo, [
                    'submodulo_id' => $submoduloId,
                    'ativo' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]));
                
                $this->command->info("  ✓ Campo '{$campo['nome']}' criado");
            } else {
                $this->command->warn("  → Campo '{$campo['nome']}' já existe");
            }
        }
    }
}