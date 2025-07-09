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
        Schema::create('projeto_anexos', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            
            // Informações do arquivo
            $table->string('nome_original');
            $table->string('nome_arquivo'); // Nome único no storage
            $table->string('path'); // Caminho no storage
            $table->string('mime_type');
            $table->integer('tamanho'); // em bytes
            
            // Categorização
            $table->enum('tipo', [
                'documento_base',
                'emenda',
                'parecer',
                'justificativa',
                'estudo_tecnico',
                'manifestacao',
                'outro'
            ])->default('outro');
            
            $table->text('descricao')->nullable();
            $table->integer('ordem')->default(0); // Para ordenação
            
            // Controle
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->boolean('publico')->default(true); // Se é público ou restrito
            $table->boolean('ativo')->default(true);
            
            // Metadados
            $table->json('metadados')->nullable(); // Dados extras do arquivo
            $table->string('hash_arquivo')->nullable(); // Para verificar integridade
            
            $table->timestamps();
            
            // Índices
            $table->index(['projeto_id', 'tipo']);
            $table->index(['uploaded_by', 'created_at']);
            $table->index('hash_arquivo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projeto_anexos');
    }
};