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
        Schema::create('tramitacao_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposicao_id');
            $table->enum('acao', [
                'CRIADO',
                'ENVIADO_PARA_REVISAO',
                'REVISADO',
                'ASSINADO',
                'PROTOCOLADO',
                'PARECER_EMITIDO',
                'INCLUIDO_PAUTA',
                'VOTADO',
                'APROVADO',
                'REJEITADO'
            ]);
            $table->unsignedBigInteger('user_id');
            $table->string('status_anterior')->nullable();
            $table->string('status_novo');
            $table->text('observacoes')->nullable();
            $table->json('dados_adicionais')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('proposicao_id')->references('id')->on('proposicoes');
            $table->foreign('user_id')->references('id')->on('users');
            
            // Índices
            $table->index('proposicao_id');
            $table->index('acao');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tramitacao_logs');
    }
};
