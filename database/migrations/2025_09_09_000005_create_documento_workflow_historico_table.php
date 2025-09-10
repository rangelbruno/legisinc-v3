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
        Schema::create('documento_workflow_historico', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('documento'); // documento_type, documento_id + índice automático
            $table->foreignId('workflow_id')->constrained('workflows');
            $table->foreignId('etapa_atual_id')->constrained('workflow_etapas');
            $table->foreignId('etapa_anterior_id')->nullable()->constrained('workflow_etapas');
            $table->foreignId('usuario_id')->constrained('users');
            $table->string('acao'); // 'criado', 'aprovado', 'reprovado', 'devolvido'
            $table->text('comentario')->nullable();
            $table->timestamp('prazo_limite')->nullable();
            $table->timestamp('processado_em')->useCurrent();
            $table->jsonb('dados_contexto')->nullable(); // Contexto adicional
            $table->timestamps();

            // Índices para consultas de auditoria
            $table->index(['documento_type','documento_id','created_at'], 'idx_historico_doc_data');
            $table->index(['usuario_id','created_at'], 'idx_historico_usuario');
            $table->index(['workflow_id','acao'], 'idx_historico_workflow_acao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_workflow_historico');
    }
};