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
        Schema::create('modelo_projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('tipo_projeto', [
                'projeto_lei_ordinaria',
                'projeto_lei_complementar', 
                'emenda_constitucional',
                'decreto_legislativo',
                'resolucao',
                'indicacao',
                'requerimento'
            ]);
            $table->longText('conteudo_modelo');
            $table->json('campos_variaveis')->nullable(); // Para campos que podem ser substituídos
            $table->boolean('ativo')->default(true);
            $table->foreignId('criado_por')->constrained('users');
            $table->timestamps();
            
            // Índices
            $table->index('tipo_projeto');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelo_projetos');
    }
};
