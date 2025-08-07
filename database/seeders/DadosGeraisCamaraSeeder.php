<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DadosGeraisCamaraSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar o módulo Templates
        $moduloTemplates = DB::table('parametros_modulos')
            ->where('nome', 'Templates')
            ->first();

        if (!$moduloTemplates) {
            $this->command->error('Módulo Templates não encontrado');
            return;
        }

        // Criar submódulo "Dados Gerais da Câmara"
        $submoduloId = DB::table('parametros_submodulos')->insertGetId([
            'modulo_id' => $moduloTemplates->id,
            'nome' => 'Dados Gerais da Câmara',
            'descricao' => 'Informações institucionais da Câmara Municipal para uso dinâmico em templates',
            'tipo' => 'form',
            'config' => json_encode(['icon' => 'fa-building', 'collapsible' => true]),
            'ordem' => 1, // Colocar como primeiro submódulo
            'ativo' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Campos do submódulo
        $campos = [
            // Identificação
            [
                'nome' => 'nome_camara_oficial',
                'label' => 'Nome Oficial da Câmara',
                'tipo_campo' => 'text',
                'descricao' => 'Nome completo oficial da Câmara Municipal',
                'obrigatorio' => true,
                'valor_padrao' => 'CÂMARA MUNICIPAL DE SÃO PAULO',
                'placeholder' => 'Ex: CÂMARA MUNICIPAL DE SÃO PAULO',
                'ordem' => 1
            ],
            [
                'nome' => 'nome_camara_abreviado',
                'label' => 'Nome Abreviado',
                'tipo_campo' => 'text',
                'descricao' => 'Nome abreviado para uso em documentos',
                'obrigatorio' => false,
                'valor_padrao' => 'CMSP',
                'placeholder' => 'Ex: CMSP, CM São Paulo',
                'ordem' => 2
            ],
            [
                'nome' => 'municipio_nome',
                'label' => 'Nome do Município',
                'tipo_campo' => 'text',
                'descricao' => 'Nome do município sede da Câmara',
                'obrigatorio' => true,
                'valor_padrao' => 'São Paulo',
                'placeholder' => 'Ex: São Paulo, Santos, Campinas',
                'ordem' => 3
            ],
            [
                'nome' => 'municipio_uf',
                'label' => 'UF',
                'tipo_campo' => 'select',
                'descricao' => 'Unidade Federativa',
                'obrigatorio' => true,
                'valor_padrao' => 'SP',
                'opcoes' => json_encode([
                    ['value' => 'AC', 'label' => 'Acre'],
                    ['value' => 'AL', 'label' => 'Alagoas'],
                    ['value' => 'AP', 'label' => 'Amapá'],
                    ['value' => 'AM', 'label' => 'Amazonas'],
                    ['value' => 'BA', 'label' => 'Bahia'],
                    ['value' => 'CE', 'label' => 'Ceará'],
                    ['value' => 'DF', 'label' => 'Distrito Federal'],
                    ['value' => 'ES', 'label' => 'Espírito Santo'],
                    ['value' => 'GO', 'label' => 'Goiás'],
                    ['value' => 'MA', 'label' => 'Maranhão'],
                    ['value' => 'MT', 'label' => 'Mato Grosso'],
                    ['value' => 'MS', 'label' => 'Mato Grosso do Sul'],
                    ['value' => 'MG', 'label' => 'Minas Gerais'],
                    ['value' => 'PA', 'label' => 'Pará'],
                    ['value' => 'PB', 'label' => 'Paraíba'],
                    ['value' => 'PR', 'label' => 'Paraná'],
                    ['value' => 'PE', 'label' => 'Pernambuco'],
                    ['value' => 'PI', 'label' => 'Piauí'],
                    ['value' => 'RJ', 'label' => 'Rio de Janeiro'],
                    ['value' => 'RN', 'label' => 'Rio Grande do Norte'],
                    ['value' => 'RS', 'label' => 'Rio Grande do Sul'],
                    ['value' => 'RO', 'label' => 'Rondônia'],
                    ['value' => 'RR', 'label' => 'Roraima'],
                    ['value' => 'SC', 'label' => 'Santa Catarina'],
                    ['value' => 'SP', 'label' => 'São Paulo'],
                    ['value' => 'SE', 'label' => 'Sergipe'],
                    ['value' => 'TO', 'label' => 'Tocantins']
                ]),
                'ordem' => 4
            ],

            // Endereço
            [
                'nome' => 'endereco_logradouro',
                'label' => 'Logradouro',
                'tipo_campo' => 'text',
                'descricao' => 'Rua, Avenida, Praça, etc.',
                'obrigatorio' => true,
                'valor_padrao' => 'Viaduto Jacareí, 100',
                'placeholder' => 'Ex: Rua das Flores, 123',
                'ordem' => 5
            ],
            [
                'nome' => 'endereco_bairro',
                'label' => 'Bairro',
                'tipo_campo' => 'text',
                'descricao' => 'Bairro onde está localizada a Câmara',
                'obrigatorio' => false,
                'valor_padrao' => 'Centro',
                'placeholder' => 'Ex: Centro, Vila Nova',
                'ordem' => 6
            ],
            [
                'nome' => 'endereco_cep',
                'label' => 'CEP',
                'tipo_campo' => 'text',
                'descricao' => 'Código de Endereçamento Postal',
                'obrigatorio' => false,
                'valor_padrao' => '01008-902',
                'placeholder' => 'Ex: 01008-902',
                'validacao' => json_encode(['regex' => '/^\d{5}-?\d{3}$/']),
                'ordem' => 7
            ],

            // Contatos
            [
                'nome' => 'telefone_principal',
                'label' => 'Telefone Principal',
                'tipo_campo' => 'text',
                'descricao' => 'Telefone principal da Câmara',
                'obrigatorio' => false,
                'valor_padrao' => '(11) 3396-4000',
                'placeholder' => 'Ex: (11) 3396-4000',
                'ordem' => 8
            ],
            [
                'nome' => 'telefone_protocolo',
                'label' => 'Telefone do Protocolo',
                'tipo_campo' => 'text',
                'descricao' => 'Telefone específico do protocolo',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: (11) 3396-4050',
                'ordem' => 9
            ],
            [
                'nome' => 'email_oficial',
                'label' => 'E-mail Oficial',
                'tipo_campo' => 'email',
                'descricao' => 'E-mail principal da Câmara',
                'obrigatorio' => false,
                'valor_padrao' => 'atendimento@camara.sp.gov.br',
                'placeholder' => 'Ex: contato@camara.municipio.gov.br',
                'ordem' => 10
            ],
            [
                'nome' => 'website',
                'label' => 'Website',
                'tipo_campo' => 'text',
                'descricao' => 'Site oficial da Câmara',
                'obrigatorio' => false,
                'valor_padrao' => 'www.saopaulo.sp.leg.br',
                'placeholder' => 'Ex: www.camara.municipio.gov.br',
                'ordem' => 11
            ],

            // Dados Administrativos
            [
                'nome' => 'cnpj',
                'label' => 'CNPJ',
                'tipo_campo' => 'text',
                'descricao' => 'CNPJ da Câmara Municipal',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: 12.345.678/0001-90',
                'validacao' => json_encode(['regex' => '/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/']),
                'ordem' => 12
            ],
            [
                'nome' => 'presidente_nome',
                'label' => 'Nome do Presidente',
                'tipo_campo' => 'text',
                'descricao' => 'Nome completo do Presidente da Câmara',
                'obrigatorio' => false,
                'valor_padrao' => '',
                'placeholder' => 'Ex: João Silva Santos',
                'ordem' => 13
            ],
            [
                'nome' => 'presidente_tratamento',
                'label' => 'Tratamento do Presidente',
                'tipo_campo' => 'select',
                'descricao' => 'Forma de tratamento oficial',
                'obrigatorio' => false,
                'valor_padrao' => 'Excelentíssimo Senhor',
                'opcoes' => json_encode([
                    ['value' => 'Excelentíssimo Senhor', 'label' => 'Excelentíssimo Senhor'],
                    ['value' => 'Excelentíssima Senhora', 'label' => 'Excelentíssima Senhora'],
                    ['value' => 'Senhor', 'label' => 'Senhor'],
                    ['value' => 'Senhora', 'label' => 'Senhora']
                ]),
                'ordem' => 14
            ],

            // Horários
            [
                'nome' => 'horario_funcionamento',
                'label' => 'Horário de Funcionamento',
                'tipo_campo' => 'text',
                'descricao' => 'Horário de funcionamento da Câmara',
                'obrigatorio' => false,
                'valor_padrao' => 'Segunda a Sexta: 8h às 17h',
                'placeholder' => 'Ex: Segunda a Sexta: 8h às 17h',
                'ordem' => 15
            ],
            [
                'nome' => 'horario_protocolo',
                'label' => 'Horário do Protocolo',
                'tipo_campo' => 'text',
                'descricao' => 'Horário específico de atendimento do protocolo',
                'obrigatorio' => false,
                'valor_padrao' => 'Segunda a Sexta: 9h às 16h',
                'placeholder' => 'Ex: Segunda a Sexta: 9h às 16h',
                'ordem' => 16
            ]
        ];

        // Inserir todos os campos
        foreach ($campos as $campo) {
            DB::table('parametros_campos')->insert(array_merge($campo, [
                'submodulo_id' => $submoduloId,
                'ativo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }

        $this->command->info("✅ Submódulo 'Dados Gerais da Câmara' criado com {" . count($campos) . "} campos");
        $this->command->info("📄 Acesse /admin/parametros/6 para configurar os dados da sua Câmara");
    }
}