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
            // Número sequencial da proposição
            $table->string('numero')->nullable()->after('id')
                ->comment('Número sequencial da proposição (ex: 0001/2024)');

            // Variáveis do template em JSON
            $table->json('variaveis_template')->nullable()->after('template_id')
                ->comment('Variáveis editáveis do template preenchidas pelo usuário');

            // Conteúdo processado (template + variáveis)
            $table->longText('conteudo_processado')->nullable()->after('conteudo')
                ->comment('Conteúdo final processado do template com variáveis substituídas');

            // Índices para melhor performance
            $table->index(['tipo', 'ano', 'numero'], 'idx_proposicoes_numeracao');
            $table->index(['status', 'created_at'], 'idx_proposicoes_status_data');
            $table->index('template_id', 'idx_proposicoes_template');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex('idx_proposicoes_numeracao');
            $table->dropIndex('idx_proposicoes_status_data');
            $table->dropIndex('idx_proposicoes_template');
            
            $table->dropColumn([
                'numero',
                'variaveis_template',
                'conteudo_processado'
            ]);
        });
    }
};
