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
        // Verificar se existem os grupos e tipos necessários
        $grupoEditor = DB::table('grupo_parametros')->where('codigo', 'editor')->first();
        $tipoBoolean = DB::table('tipo_parametros')->where('codigo', 'boolean')->first();

        // Se não existir o grupo Editor, criar um básico
        if (!$grupoEditor) {
            $grupoEditorId = DB::table('grupo_parametros')->insertGetId([
                'nome' => 'Editor',
                'codigo' => 'editor',
                'descricao' => 'Configurações do editor OnlyOffice',
                'ativo' => true,
                'ordem' => 100,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            $grupoEditorId = $grupoEditor->id;
        }

        // Se não existir o tipo boolean, usar ID padrão
        $tipoBooleanId = $tipoBoolean ? $tipoBoolean->id : 1;

        // Adicionar o parâmetro para controlar a exibição do botão de exportar PDF
        DB::table('parametros')->updateOrInsert(
            ['codigo' => 'editor.exibir_botao_exportar_pdf_s3'],
            [
                'nome' => 'Exibir Botão Exportar PDF para S3',
                'codigo' => 'editor.exibir_botao_exportar_pdf_s3',
                'descricao' => 'Controla a exibição do botão de exportar PDF para S3 no editor OnlyOffice',
                'grupo_parametro_id' => $grupoEditorId,
                'tipo_parametro_id' => $tipoBooleanId,
                'valor' => '1',
                'valor_padrao' => '1',
                'obrigatorio' => false,
                'editavel' => true,
                'visivel' => true,
                'ativo' => true,
                'ordem' => 1,
                'help_text' => 'Quando habilitado, exibe o botão de exportar PDF no editor OnlyOffice',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('parametros')->where('codigo', 'editor.exibir_botao_exportar_pdf_s3')->delete();
        // Opcional: remover o grupo se foi criado por esta migração
        DB::table('grupo_parametros')->where('codigo', 'editor')->delete();
    }
};