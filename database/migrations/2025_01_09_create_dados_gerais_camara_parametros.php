<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Buscar o módulo Templates
        $moduloTemplates = DB::table('parametros_modulos')
            ->where('nome', 'Templates')
            ->first();

        if (!$moduloTemplates) {
            // Criar módulo Templates se não existir
            $moduloId = DB::table('parametros_modulos')->insertGetId([
                'nome' => 'Templates',
                'descricao' => 'Configurações de Templates de Proposições',
                'ativo' => true,
                'ordem' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $moduloId = $moduloTemplates->id;
        }

        // Criar submódulo "Dados Gerais da Câmara"
        $submoduloId = DB::table('parametros_submodulos')->insertGetId([
            'modulo_id' => $moduloId,
            'nome' => 'Dados Gerais da Câmara',
            'descricao' => 'Informações institucionais da Câmara Municipal',
            'tipo' => 'form',
            'ordem' => 5,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Campos do submódulo
        $campos = [
            // Identificação
            ['nome' => 'nome_camara_oficial', 'label' => 'Nome Oficial da Câmara', 'tipo_campo' => 'text', 'obrigatorio' => true, 'valor_padrao' => 'CÂMARA MUNICIPAL DE SÃO PAULO', 'ordem' => 1],
            ['nome' => 'nome_camara_abreviado', 'label' => 'Nome Abreviado', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => 'CMSP', 'ordem' => 2],
            ['nome' => 'cnpj', 'label' => 'CNPJ', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => '', 'ordem' => 3],
            
            // Localização
            ['nome' => 'municipio_nome', 'label' => 'Nome do Município', 'tipo_campo' => 'text', 'obrigatorio' => true, 'valor_padrao' => 'São Paulo', 'ordem' => 4],
            ['nome' => 'municipio_uf', 'label' => 'UF', 'tipo_campo' => 'text', 'obrigatorio' => true, 'valor_padrao' => 'SP', 'ordem' => 5],
            
            // Endereço
            ['nome' => 'endereco_logradouro', 'label' => 'Logradouro', 'tipo_campo' => 'text', 'obrigatorio' => true, 'valor_padrao' => 'Viaduto Jacareí, 100', 'ordem' => 6],
            ['nome' => 'endereco_bairro', 'label' => 'Bairro', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => 'Bela Vista', 'ordem' => 7],
            ['nome' => 'endereco_cep', 'label' => 'CEP', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => '01319-900', 'ordem' => 8],
            
            // Contatos
            ['nome' => 'telefone_principal', 'label' => 'Telefone Principal', 'tipo_campo' => 'text', 'obrigatorio' => true, 'valor_padrao' => '(11) 3396-4000', 'ordem' => 9],
            ['nome' => 'telefone_protocolo', 'label' => 'Telefone do Protocolo', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => '(11) 3396-4100', 'ordem' => 10],
            ['nome' => 'email_oficial', 'label' => 'E-mail Oficial', 'tipo_campo' => 'email', 'obrigatorio' => false, 'valor_padrao' => 'contato@camara.sp.gov.br', 'ordem' => 11],
            ['nome' => 'website', 'label' => 'Website', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => 'www.camara.sp.gov.br', 'ordem' => 12],
            
            // Dados administrativos
            ['nome' => 'presidente_nome', 'label' => 'Nome do Presidente', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => '', 'ordem' => 13],
            ['nome' => 'presidente_tratamento', 'label' => 'Tratamento do Presidente', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => 'Excelentíssimo Senhor', 'ordem' => 14],
            
            // Horários
            ['nome' => 'horario_funcionamento', 'label' => 'Horário de Funcionamento', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => 'Segunda a Sexta: 8h às 17h', 'ordem' => 15],
            ['nome' => 'horario_protocolo', 'label' => 'Horário do Protocolo', 'tipo_campo' => 'text', 'obrigatorio' => false, 'valor_padrao' => 'Segunda a Sexta: 9h às 16h', 'ordem' => 16],
        ];

        // Inserir campos
        foreach ($campos as $campo) {
            $campoId = DB::table('parametros_campos')->insertGetId([
                'submodulo_id' => $submoduloId,
                'nome' => $campo['nome'],
                'label' => $campo['label'],
                'tipo_campo' => $campo['tipo_campo'],
                'obrigatorio' => $campo['obrigatorio'],
                'valor_padrao' => $campo['valor_padrao'],
                'ordem' => $campo['ordem'],
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Inserir valor padrão
            if (!empty($campo['valor_padrao'])) {
                DB::table('parametros_valores')->insert([
                    'campo_id' => $campoId,
                    'valor' => $campo['valor_padrao'],
                    'tipo_valor' => 'string',
                    'user_id' => null,
                    'valido_ate' => null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Buscar submódulo
        $submodulo = DB::table('parametros_submodulos')
            ->where('nome', 'Dados Gerais da Câmara')
            ->first();

        if ($submodulo) {
            // Remover valores
            DB::table('parametros_valores')
                ->whereIn('campo_id', function($query) use ($submodulo) {
                    $query->select('id')
                        ->from('parametros_campos')
                        ->where('submodulo_id', $submodulo->id);
                })
                ->delete();

            // Remover campos
            DB::table('parametros_campos')
                ->where('submodulo_id', $submodulo->id)
                ->delete();

            // Remover submódulo
            DB::table('parametros_submodulos')
                ->where('id', $submodulo->id)
                ->delete();
        }
    }
};