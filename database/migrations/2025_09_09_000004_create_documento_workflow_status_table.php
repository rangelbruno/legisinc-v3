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
        Schema::create('documento_workflow_status', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('documento'); // documento_type, documento_id + índice automático
            $table->foreignId('workflow_id')->constrained('workflows');
            $table->foreignId('etapa_atual_id')->constrained('workflow_etapas');
            $table->string('status')->default('em_andamento'); // em_andamento, pausado, finalizado, cancelado
            $table->timestamp('prazo_atual')->nullable();
            $table->timestamp('iniciado_em')->useCurrent();
            $table->timestamp('finalizado_em')->nullable();
            $table->jsonb('dados_workflow')->nullable(); // Estado específico
            $table->unsignedInteger('version')->default(0); // ⚡ Lock otimista para concorrência
            $table->timestamps();

            // Garantir um workflow por documento
            $table->unique(['documento_id','documento_type','workflow_id'], 'uniq_doc_workflow');
            
            // Índices críticos de performance
            $table->index(['status','prazo_atual'], 'idx_status_prazo_vencido');
            $table->index(['workflow_id','status'], 'idx_workflow_ativo');
        });

        // Constraint: estados válidos apenas
        DB::statement("ALTER TABLE documento_workflow_status
                       ADD CONSTRAINT chk_wf_status
                       CHECK (status IN ('em_andamento','pausado','finalizado','cancelado'))");

        // Índice parcial para documentos em andamento
        DB::statement('CREATE INDEX idx_doc_workflow_ativo 
                       ON documento_workflow_status (workflow_id, status) 
                       WHERE status IN (\'em_andamento\', \'pausado\')');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_workflow_status');
    }
};