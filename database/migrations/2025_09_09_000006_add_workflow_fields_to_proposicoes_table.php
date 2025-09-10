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
        Schema::table('proposicoes', function (Blueprint $table) {
            // Campos para acesso rápido (a verdade fica em documento_workflow_status)
            $table->foreignId('workflow_id')->nullable()->constrained('workflows');
            $table->foreignId('etapa_workflow_atual_id')->nullable()->constrained('workflow_etapas');
            $table->boolean('fluxo_personalizado')->default(false);
            
            // Índices para consultas frequentes
            $table->index(['workflow_id'], 'idx_proposicoes_workflow');
            $table->index(['etapa_workflow_atual_id'], 'idx_proposicoes_etapa_atual');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropForeign(['workflow_id']);
            $table->dropForeign(['etapa_workflow_atual_id']);
            $table->dropIndex(['workflow_id']);
            $table->dropIndex(['etapa_workflow_atual_id']);
            $table->dropColumn(['workflow_id', 'etapa_workflow_atual_id', 'fluxo_personalizado']);
        });
    }
};