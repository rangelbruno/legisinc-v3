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
        Schema::create('item_pautas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sessao_id');
            $table->unsignedBigInteger('proposicao_id');
            $table->integer('ordem');
            $table->enum('momento', ['EXPEDIENTE', 'ORDEM_DO_DIA']);
            $table->enum('status', ['AGUARDANDO', 'EM_DISCUSSAO', 'VOTADO', 'ADIADO'])->default('AGUARDANDO');
            $table->enum('resultado_votacao', ['APROVADO', 'REJEITADO', 'EMENDADO'])->nullable();
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('sessao_id')->references('id')->on('sessao_plenarias');
            $table->foreign('proposicao_id')->references('id')->on('proposicoes');
            
            // Índices
            $table->index(['sessao_id', 'ordem']);
            $table->index('momento');
            $table->index('status');
            
            // Constraint para evitar duplicatas
            $table->unique(['sessao_id', 'proposicao_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_pautas');
    }
};
