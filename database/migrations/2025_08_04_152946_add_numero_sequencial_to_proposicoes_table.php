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
            $table->integer('numero_sequencial')->nullable()->after('numero_protocolo')
                ->comment('Número sequencial usado para controle interno');
            
            // Índice para busca rápida do último sequencial
            $table->index(['tipo', 'numero_sequencial']);
            $table->index(['data_protocolo', 'numero_sequencial']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex(['tipo', 'numero_sequencial']);
            $table->dropIndex(['data_protocolo', 'numero_sequencial']);
            $table->dropColumn('numero_sequencial');
        });
    }
};