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
        Schema::create('workflow_transicoes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('workflow_id')->constrained('workflows')->onDelete('cascade');
            $table->foreignId('etapa_origem_id')->constrained('workflow_etapas')->onDelete('cascade');
            $table->foreignId('etapa_destino_id')->constrained('workflow_etapas')->onDelete('cascade');
            $table->string('acao'); // 'aprovar', 'reprovar', 'devolver'
            $table->jsonb('condicao')->nullable(); // Condições específicas
            $table->boolean('automatica')->default(false);
            $table->timestamps();

            // Evita fan-out confuso: uma ação por etapa origem
            $table->unique(['workflow_id','etapa_origem_id','acao'], 'uniq_transicao_acao');
            
            // Índices de performance
            $table->index(['etapa_origem_id','acao'], 'idx_transicao_busca');
        });

        // Constraint: evita auto-loops
        DB::statement('ALTER TABLE workflow_transicoes 
                       ADD CONSTRAINT chk_no_self_loop 
                       CHECK (etapa_origem_id != etapa_destino_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transicoes');
    }
};