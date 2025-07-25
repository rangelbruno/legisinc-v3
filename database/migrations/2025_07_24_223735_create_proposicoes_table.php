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
        Schema::create('proposicoes', function (Blueprint $table) {
            $table->id();
            $table->string('tipo'); // projeto_lei_ordinaria, etc.
            $table->text('ementa');
            $table->longText('conteudo')->nullable();
            $table->string('arquivo_path')->nullable(); // Caminho do arquivo DOCX
            $table->unsignedBigInteger('autor_id'); // ID do usuário autor
            $table->enum('status', ['rascunho', 'em_edicao', 'salvando', 'enviado_legislativo', 'retornado_legislativo', 'assinado', 'protocolado'])->default('rascunho');
            $table->integer('ano');
            $table->string('modelo_id')->nullable(); // ID do modelo usado
            $table->string('template_id')->nullable(); // ID do template usado
            $table->timestamp('ultima_modificacao')->nullable();
            $table->timestamps();
            
            // Índices
            $table->index(['autor_id', 'status']);
            $table->index(['tipo', 'ano']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposicoes');
    }
};
