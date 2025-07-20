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
        Schema::create('tipo_proposicoes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->comment('Código único do tipo (ex: projeto_lei_ordinaria)');
            $table->string('nome', 200)->comment('Nome completo do tipo (ex: Projeto de Lei Ordinária)');
            $table->text('descricao')->nullable()->comment('Descrição detalhada do tipo de proposição');
            $table->string('icone', 100)->default('ki-document')->comment('Ícone ki-duotone para interface');
            $table->string('cor', 50)->default('primary')->comment('Cor para badges e interface');
            $table->boolean('ativo')->default(true)->comment('Se o tipo está ativo para uso');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->json('configuracoes')->nullable()->comment('Configurações específicas do tipo (JSON)');
            $table->string('template_padrao')->nullable()->comment('Template padrão para este tipo');
            $table->timestamps();
            
            // Índices
            $table->index(['ativo', 'ordem']);
            $table->index('codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_proposicoes');
    }
};