<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametros_campos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submodulo_id')->constrained('parametros_submodulos')->cascadeOnDelete();
            $table->string('nome');
            $table->string('label');
            $table->enum('tipo_campo', ['text', 'email', 'number', 'textarea', 'select', 'checkbox', 'radio', 'file', 'date', 'datetime']);
            $table->text('descricao')->nullable();
            $table->boolean('obrigatorio')->default(false);
            $table->text('valor_padrao')->nullable();
            $table->json('opcoes')->nullable(); // Para select, radio, etc.
            $table->json('validacao')->nullable(); // Regras de validação
            $table->string('placeholder')->nullable();
            $table->string('classe_css')->nullable();
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['submodulo_id', 'ativo', 'ordem']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_campos');
    }
};