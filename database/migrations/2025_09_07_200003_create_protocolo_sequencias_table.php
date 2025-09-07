<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabela para controle sequencial de números de protocolo por ano
     */
    public function up(): void
    {
        Schema::create('protocolo_sequencias', function (Blueprint $table) {
            $table->integer('ano')->primary()->comment('Ano da sequência');
            $table->integer('proximo_numero')->default(1)->comment('Próximo número a ser usado');
            $table->timestamps();
            
            // Índice para consultas por ano (já é PK, mas deixando explícito)
            $table->comment('Controle sequencial de numeração de protocolo por ano');
        });
        
        // Inserir sequência para o ano atual
        DB::table('protocolo_sequencias')->insert([
            'ano' => date('Y'),
            'proximo_numero' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Remove a tabela de sequências
     */
    public function down(): void
    {
        Schema::dropIfExists('protocolo_sequencias');
    }
};