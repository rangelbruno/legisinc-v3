<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documento_modelos', function (Blueprint $table) {
            // Adicionar campos para suportar templates
            $table->boolean('is_template')->default(false)->after('tipo_proposicao_id')
                ->comment('Indica se é um template padrão do sistema');
            
            $table->string('template_id')->nullable()->after('is_template')
                ->comment('ID único do template (ex: projeto_lei_ordinaria, resolucao_mesa)');
            
            $table->string('categoria')->nullable()->after('template_id')
                ->comment('Categoria do template (ex: legislativo, administrativo, etc)');
            
            $table->integer('ordem')->default(0)->after('categoria')
                ->comment('Ordem de exibição na lista');
                
            $table->json('metadata')->nullable()->after('variaveis')
                ->comment('Metadados adicionais do template');
                
            // Índices
            $table->index(['is_template', 'tipo_proposicao_id']);
            $table->index('template_id');
            $table->index(['categoria', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_modelos', function (Blueprint $table) {
            $table->dropIndex(['is_template', 'tipo_proposicao_id']);
            $table->dropIndex('template_id');
            $table->dropIndex(['categoria', 'ordem']);
            
            $table->dropColumn([
                'is_template',
                'template_id',
                'categoria',
                'ordem',
                'metadata'
            ]);
        });
    }
};
