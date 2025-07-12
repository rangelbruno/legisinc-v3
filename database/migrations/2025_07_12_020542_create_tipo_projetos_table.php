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
        Schema::create('tipo_projetos', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Lei Ordinária, Lei Complementar, etc.
            $table->text('descricao')->nullable();
            $table->text('template_conteudo')->nullable(); // Template padrão
            $table->boolean('ativo')->default(true);
            $table->json('metadados')->nullable(); // Configurações específicas
            $table->timestamps();
            
            $table->index('ativo');
            $table->unique('nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_projetos');
    }
};
