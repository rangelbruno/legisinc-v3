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
        Schema::table('projetos', function (Blueprint $table) {
            // Adicionar campos específicos para tramitação
            if (!Schema::hasColumn('projetos', 'numero_protocolo')) {
                $table->string('numero_protocolo')->nullable()->after('data_protocolo');
            }
            if (!Schema::hasColumn('projetos', 'data_assinatura')) {
                $table->timestamp('data_assinatura')->nullable()->after('data_protocolo');
            }
            
            // Índices
            if (!Schema::hasColumn('projetos', 'numero_protocolo') || !DB::getSchemaBuilder()->hasIndex('projetos', 'projetos_numero_protocolo_index')) {
                $table->index('numero_protocolo');
            }
        });
        
        // Atualizar o enum status para incluir novos valores
        DB::statement("UPDATE projetos SET status = 'rascunho' WHERE status NOT IN ('rascunho', 'aprovado', 'rejeitado')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projetos', function (Blueprint $table) {
            if (Schema::hasColumn('projetos', 'numero_protocolo')) {
                $table->dropIndex(['numero_protocolo']);
                $table->dropColumn(['numero_protocolo', 'data_assinatura']);
            }
        });
    }
};
