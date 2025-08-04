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
            // Campos para novo fluxo de tramitação
            $table->timestamp('enviado_revisao_em')->nullable()->after('data_protocolo');
            $table->unsignedBigInteger('revisor_id')->nullable()->after('enviado_revisao_em');
            $table->timestamp('revisado_em')->nullable()->after('revisor_id');
            $table->string('pdf_path')->nullable()->after('revisado_em');
            $table->string('pdf_assinado_path')->nullable()->after('pdf_path');
            $table->enum('momento_sessao', ['EXPEDIENTE', 'ORDEM_DO_DIA', 'NAO_CLASSIFICADO'])->nullable()->after('pdf_assinado_path');
            $table->boolean('tem_parecer')->default(false)->after('momento_sessao');
            $table->unsignedBigInteger('parecer_id')->nullable()->after('tem_parecer');
            
            // Foreign keys
            $table->foreign('revisor_id')->references('id')->on('users');
            // Nota: foreign key para parecer_juridicos será criada após a tabela ser criada
            
            // Índices
            $table->index('momento_sessao');
            $table->index('tem_parecer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropForeign(['revisor_id']);
            $table->dropIndex(['momento_sessao']);
            $table->dropIndex(['tem_parecer']);
            
            $table->dropColumn([
                'enviado_revisao_em',
                'revisor_id',
                'revisado_em',
                'pdf_path',
                'pdf_assinado_path',
                'momento_sessao',
                'tem_parecer',
                'parecer_id'
            ]);
        });
    }
};
