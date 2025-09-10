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
        Schema::create('workflows', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('tipo_documento'); // 'proposicao', 'parecer', 'requerimento', etc.
            $table->boolean('ativo')->default(true);
            $table->boolean('is_default')->default(false);
            $table->integer('ordem')->default(0);
            $table->jsonb('configuracao')->nullable();
            $table->timestamps();

            // Índices de performance
            $table->index(['tipo_documento'], 'idx_workflows_tipo');
            $table->index(['ativo'], 'idx_workflows_ativo');
        });

        // Constraint: apenas um workflow padrão por tipo
        DB::statement('CREATE UNIQUE INDEX uniq_default_workflow 
                       ON workflows (tipo_documento) 
                       WHERE is_default = true');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflows');
    }
};