<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL requires dropping and recreating the constraint
        DB::statement("ALTER TABLE proposicoes DROP CONSTRAINT proposicoes_status_check");
        
        DB::statement("ALTER TABLE proposicoes ADD CONSTRAINT proposicoes_status_check CHECK (status IN ('rascunho', 'em_edicao', 'salvando', 'enviado_legislativo', 'em_revisao', 'aguardando_aprovacao_autor', 'devolvido_edicao', 'retornado_legislativo', 'aprovado_assinatura', 'devolvido_correcao', 'assinado', 'enviado_protocolo', 'protocolado', 'aprovado', 'rejeitado'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original constraint
        DB::statement("ALTER TABLE proposicoes DROP CONSTRAINT proposicoes_status_check");
        
        DB::statement("ALTER TABLE proposicoes ADD CONSTRAINT proposicoes_status_check CHECK (status IN ('rascunho', 'em_edicao', 'salvando', 'enviado_legislativo', 'retornado_legislativo', 'assinado', 'protocolado'))");
    }
};