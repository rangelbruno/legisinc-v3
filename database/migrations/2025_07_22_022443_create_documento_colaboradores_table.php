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
        Schema::create('documento_colaboradores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instancia_id')->constrained('documento_instancias')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('permissao', ['view', 'comment', 'edit', 'admin'])->default('view');
            $table->boolean('ativo')->default(true);
            $table->timestamp('ultimo_acesso')->nullable();
            $table->foreignId('adicionado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->unique(['instancia_id', 'user_id']);
            $table->index(['user_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_colaboradores');
    }
};
