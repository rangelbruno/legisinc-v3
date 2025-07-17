<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 150)->comment('Nome do parâmetro');
            $table->string('codigo', 100)->unique()->comment('Código identificador único');
            $table->text('descricao')->nullable()->comment('Descrição do parâmetro');
            $table->unsignedBigInteger('grupo_parametro_id')->comment('Grupo do parâmetro');
            $table->unsignedBigInteger('tipo_parametro_id')->comment('Tipo do parâmetro');
            $table->text('valor')->nullable()->comment('Valor atual do parâmetro');
            $table->text('valor_padrao')->nullable()->comment('Valor padrão do parâmetro');
            $table->json('configuracao')->nullable()->comment('Configurações específicas do parâmetro');
            $table->json('regras_validacao')->nullable()->comment('Regras de validação específicas');
            $table->boolean('obrigatorio')->default(false)->comment('Parâmetro obrigatório');
            $table->boolean('editavel')->default(true)->comment('Parâmetro editável');
            $table->boolean('visivel')->default(true)->comment('Parâmetro visível na interface');
            $table->boolean('ativo')->default(true)->comment('Status ativo/inativo');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->text('help_text')->nullable()->comment('Texto de ajuda');
            $table->timestamps();

            // Índices
            $table->index(['ativo', 'visivel', 'ordem']);
            $table->index('codigo');
            $table->index('grupo_parametro_id');
            $table->index('tipo_parametro_id');
            $table->index(['grupo_parametro_id', 'ordem']);
            
            // Foreign keys
            $table->foreign('grupo_parametro_id')
                  ->references('id')
                  ->on('grupos_parametros')
                  ->onDelete('cascade');
                  
            $table->foreign('tipo_parametro_id')
                  ->references('id')
                  ->on('tipos_parametros')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};