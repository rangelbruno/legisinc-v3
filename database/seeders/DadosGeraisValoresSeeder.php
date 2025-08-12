<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\Parametro\ParametroService;

class DadosGeraisValoresSeeder extends Seeder
{
    /**
     * Popula os valores padrão dos dados gerais da Câmara Municipal
     */
    public function run(): void
    {
        $parametroService = app(ParametroService::class);
        
        // Dados de Identificação
        $this->seedIdentificacao($parametroService);
        
        // Dados de Endereço  
        $this->seedEndereco($parametroService);
        
        // Dados de Contatos
        $this->seedContatos($parametroService);
        
        // Dados de Funcionamento
        $this->seedFuncionamento($parametroService);
        
        // Dados de Gestão
        $this->seedGestao($parametroService);
        
        $this->command->info('✅ Valores padrão dos Dados Gerais da Câmara configurados com sucesso!');
        $this->command->info('🏛️ Configure seus dados específicos em: /parametros-dados-gerais-camara');
    }
    
    private function seedIdentificacao(ParametroService $service): void
    {
        $valores = [
            'nome_camara' => 'Câmara Municipal Caraguatatuba',
            'sigla_camara' => 'CMC', 
            'cnpj' => '50.444.108/0001-41',
        ];
        
        foreach ($valores as $campo => $valor) {
            $service->salvarValor('Dados Gerais', 'Identificação', $campo, $valor);
        }
    }
    
    private function seedEndereco(ParametroService $service): void
    {
        $valores = [
            'endereco' => 'Praça da República, 40',
            'numero' => '40',
            'complemento' => '',
            'bairro' => 'Centro',
            'cidade' => 'Caraguatatuba', 
            'estado' => 'SP',
            'cep' => '11660-020',
        ];
        
        foreach ($valores as $campo => $valor) {
            $service->salvarValor('Dados Gerais', 'Endereço', $campo, $valor);
        }
    }
    
    private function seedContatos(ParametroService $service): void
    {
        $valores = [
            'telefone' => '(12) 3882-5588',
            'telefone_secundario' => '(12) 3882-5589',
            'email_institucional' => 'atendimento@camaracaraguatatuba.sp.gov.br',
            'email_contato' => 'presidencia@camaracaraguatatuba.sp.gov.br',
            'website' => 'www.camaracaraguatatuba.sp.gov.br',
        ];
        
        foreach ($valores as $campo => $valor) {
            $service->salvarValor('Dados Gerais', 'Contatos', $campo, $valor);
        }
    }
    
    private function seedFuncionamento(ParametroService $service): void
    {
        $valores = [
            'horario_funcionamento' => 'Segunda a Sexta, 8h às 17h',
            'horario_atendimento' => 'Segunda a Sexta, 8h às 16h',
        ];
        
        foreach ($valores as $campo => $valor) {
            $service->salvarValor('Dados Gerais', 'Funcionamento', $campo, $valor);
        }
    }
    
    private function seedGestao(ParametroService $service): void
    {
        $valores = [
            'presidente_nome' => '',
            'presidente_partido' => '',
            'legislatura_atual' => '2021-2024',
            'numero_vereadores' => 9,
        ];
        
        foreach ($valores as $campo => $valor) {
            $service->salvarValor('Dados Gerais', 'Gestão', $campo, $valor);
        }
    }
}