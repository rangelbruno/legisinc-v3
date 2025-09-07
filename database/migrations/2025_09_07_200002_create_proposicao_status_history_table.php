<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela para auditoria de mudanças de status (State Machine)
     */
    public function up(): void
    {
        Schema::create('proposicao_status_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('proposicao_id')->constrained('proposicoes')->onDelete('cascade');
            $table->string('status_anterior', 50)->nullable()->comment('Status antes da transição');
            $table->string('status_novo', 50)->comment('Status após a transição');
            $table->foreignId('user_id')->constrained('users')->comment('Usuário responsável pela mudança');
            $table->text('observacoes')->nullable()->comment('Observações sobre a transição');
            $table->string('ip_address', 45)->nullable()->comment('IP do usuário');
            $table->string('user_agent')->nullable()->comment('User agent do browser');
            $table->timestamps();
            
            // Índices para consultas frequentes
            $table->index(['proposicao_id', 'created_at'], 'idx_proposicao_status_history_timeline');
            $table->index(['status_anterior', 'status_novo'], 'idx_proposicao_status_transitions');
            $table->index(['user_id'], 'idx_proposicao_status_user');
            $table->index(['created_at'], 'idx_proposicao_status_created');
        });
    }

    /**
     * Remove a tabela de histórico
     */
    public function down(): void
    {
        Schema::dropIfExists('proposicao_status_history');
    }
};