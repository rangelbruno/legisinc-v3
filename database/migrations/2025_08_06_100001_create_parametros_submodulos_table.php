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
        Schema::create('parametros_submodulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained('parametros_modulos')->cascadeOnDelete();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->enum('tipo', ['form', 'checkbox', 'select', 'toggle', 'custom']);
            $table->json('config')->nullable(); // Configurações específicas
            $table->integer('ordem')->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->index(['modulo_id', 'ativo', 'ordem']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_submodulos');
    }
};