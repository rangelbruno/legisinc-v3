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
        Schema::table('proposicoes', function (Blueprint $table) {
            // Campo para armazenar informações de validação ABNT
            $table->json('validacao_abnt')->nullable()->after('conteudo_processado');
            
            // Campo para indicar qual template foi usado
            $table->string('template_usado')->nullable()->after('validacao_abnt');
            
            // Índice para buscar proposições por template usado
            $table->index('template_usado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex(['template_usado']);
            $table->dropColumn(['validacao_abnt', 'template_usado']);
        });
    }
};