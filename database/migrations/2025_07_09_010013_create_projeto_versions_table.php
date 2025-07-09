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
        Schema::create('projeto_versions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->integer('version_number');
            
            // Conteúdo da versão
            $table->longText('conteudo'); // HTML/JSON do editor
            $table->text('changelog')->nullable(); // O que mudou nesta versão
            $table->text('comentarios')->nullable(); // Comentários sobre a versão
            
            // Metadados da versão
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->enum('tipo_alteracao', [
                'criacao',
                'revisao',
                'emenda', 
                'correcao',
                'formatacao'
            ])->default('revisao');
            
            $table->boolean('is_current')->default(false); // Versão atual
            $table->boolean('is_published')->default(false); // Versão publicada
            
            $table->json('diff_data')->nullable(); // Dados do diff com versão anterior
            $table->integer('tamanho_bytes')->nullable(); // Tamanho do conteúdo
            
            $table->timestamps();
            
            // Índices
            $table->unique(['projeto_id', 'version_number']);
            $table->index(['projeto_id', 'is_current']);
            $table->index(['author_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projeto_versions');
    }
};