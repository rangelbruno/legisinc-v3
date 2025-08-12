<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Parametro\ParametroModulo;
use App\Models\Parametro\ParametroSubmodulo;
use App\Models\Parametro\ParametroCampo;

class DadosGeraisParametrosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se o módulo já existe
        $moduloExistente = ParametroModulo::where('nome', 'Dados Gerais')->first();
        if ($moduloExistente) {
            $this->command->info('Módulo "Dados Gerais" já existe, pulando criação.');
            return;
        }

        // Criar o módulo principal
        $modulo = ParametroModulo::create([
            'nome' => 'Dados Gerais',
            'descricao' => 'Dados gerais da câmara municipal',
            'icon' => 'ki-bank',
            'ordem' => 1,
            'ativo' => true
        ]);

        // Criar submódulos
        $submodulos = [
            'Identificação' => [
                'descricao' => 'Dados de identificação da câmara',
                'campos' => [
                    'nome_camara' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Nome da Câmara'],
                    'sigla_camara' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Sigla'],
                    'cnpj' => ['tipo' => 'text', 'obrigatorio' => false, 'label' => 'CNPJ'],
                ]
            ],
            'Endereço' => [
                'descricao' => 'Endereço completo da câmara',
                'campos' => [
                    'endereco' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Endereço'],
                    'numero' => ['tipo' => 'text', 'obrigatorio' => false, 'label' => 'Número'],
                    'complemento' => ['tipo' => 'text', 'obrigatorio' => false, 'label' => 'Complemento'],
                    'bairro' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Bairro'],
                    'cidade' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Cidade'],
                    'estado' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Estado'],
                    'cep' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'CEP'],
                ]
            ],
            'Contatos' => [
                'descricao' => 'Informações de contato',
                'campos' => [
                    'telefone' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Telefone Principal'],
                    'telefone_secundario' => ['tipo' => 'text', 'obrigatorio' => false, 'label' => 'Telefone Secundário'],
                    'email_institucional' => ['tipo' => 'email', 'obrigatorio' => true, 'label' => 'E-mail Institucional'],
                    'email_contato' => ['tipo' => 'email', 'obrigatorio' => false, 'label' => 'E-mail de Contato'],
                    'website' => ['tipo' => 'text', 'obrigatorio' => false, 'label' => 'Website'],
                ]
            ],
            'Funcionamento' => [
                'descricao' => 'Horários de funcionamento',
                'campos' => [
                    'horario_funcionamento' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Horário de Funcionamento'],
                    'horario_atendimento' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Horário de Atendimento'],
                ]
            ],
            'Gestão' => [
                'descricao' => 'Informações da gestão atual',
                'campos' => [
                    'presidente_nome' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Nome do Presidente'],
                    'presidente_partido' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Partido do Presidente'],
                    'legislatura_atual' => ['tipo' => 'text', 'obrigatorio' => true, 'label' => 'Legislatura Atual'],
                    'numero_vereadores' => ['tipo' => 'number', 'obrigatorio' => true, 'label' => 'Número de Vereadores'],
                ]
            ]
        ];

        $ordemSubmodulo = 1;
        foreach ($submodulos as $nomeSubmodulo => $dadosSubmodulo) {
            $submodulo = ParametroSubmodulo::create([
                'modulo_id' => $modulo->id,
                'nome' => $nomeSubmodulo,
                'descricao' => $dadosSubmodulo['descricao'],
                'tipo' => 'form',
                'ordem' => $ordemSubmodulo++,
                'ativo' => true
            ]);

            $ordemCampo = 1;
            foreach ($dadosSubmodulo['campos'] as $nomeCampo => $dadosCampo) {
                ParametroCampo::create([
                    'submodulo_id' => $submodulo->id,
                    'nome' => $nomeCampo,
                    'label' => $dadosCampo['label'],
                    'tipo_campo' => $dadosCampo['tipo'],
                    'obrigatorio' => $dadosCampo['obrigatorio'],
                    'ordem' => $ordemCampo++,
                    'ativo' => true
                ]);
            }
        }

        $this->command->info('Módulo e submódulos "Dados Gerais" criados com sucesso.');
    }
}