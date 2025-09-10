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
        Schema::create('workflow_etapas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('workflow_id')->constrained('workflows')->onDelete('cascade');
            $table->string('key'); // 🔑 Slug único para mapeamento Designer → DB
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('role_responsavel')->nullable(); // 'parlamentar', 'legislativo', etc.
            $table->integer('ordem');
            $table->integer('tempo_limite_dias')->nullable();
            $table->boolean('permite_edicao')->default(false);
            $table->boolean('permite_assinatura')->default(false);
            $table->boolean('requer_aprovacao')->default(false);
            $table->jsonb('acoes_possiveis')->nullable(); // ['aprovar', 'reprovar', 'devolver']
            $table->jsonb('condicoes')->nullable(); // Condições para avançar
            $table->unsignedBigInteger('template_notificacao_id')->nullable();
            $table->timestamps();

            // Garantir key única por workflow (essencial para Designer)
            $table->unique(['workflow_id','key'], 'uniq_workflow_etapa_key');
            
            // Garantir ordem única por workflow
            $table->unique(['workflow_id','ordem'], 'uniq_workflow_etapa_ordem');
            
            // Índices de performance
            $table->index(['workflow_id','ordem'], 'idx_etapas_workflow_ordem');
            $table->index(['role_responsavel'], 'idx_etapas_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_etapas');
    }
};