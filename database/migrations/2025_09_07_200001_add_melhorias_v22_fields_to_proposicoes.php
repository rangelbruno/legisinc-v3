<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona campos das melhorias v2.2: três camadas de PDF, hash de conteúdo e controle por timestamp
     */
    public function up(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            // Três camadas de PDF
            $table->string('arquivo_pdf_para_assinatura')->nullable()->after('arquivo_pdf_path');
            $table->string('arquivo_pdf_assinado')->nullable()->after('arquivo_pdf_para_assinatura');
            $table->string('arquivo_pdf_protocolado')->nullable()->after('arquivo_pdf_assinado');
            
            // Controle de versão por hash
            $table->string('arquivo_hash', 64)->nullable()->after('arquivo_pdf_protocolado')
                  ->comment('SHA-256 do arquivo fonte para detectar mudanças');
            $table->string('pdf_base_hash', 64)->nullable()->after('arquivo_hash')
                  ->comment('Hash do arquivo usado para gerar o PDF atual');
            
            // Timestamp específico de conteúdo (não qualquer update)
            $table->timestamp('conteudo_updated_at')->nullable()->after('pdf_base_hash')
                  ->comment('Última modificação do conteúdo do documento');
            
            // Metadados do conversor
            $table->string('pdf_conversor_usado', 50)->default('onlyoffice')->after('conteudo_updated_at')
                  ->comment('Conversor usado: onlyoffice, unoconv, fallback');
            
            // Índices para performance
            $table->index(['arquivo_hash'], 'idx_proposicoes_arquivo_hash');
            $table->index(['conteudo_updated_at'], 'idx_proposicoes_conteudo_updated');
            $table->index(['pdf_conversor_usado'], 'idx_proposicoes_pdf_conversor');
        });
    }

    /**
     * Remove os campos adicionados
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex('idx_proposicoes_pdf_conversor');
            $table->dropIndex('idx_proposicoes_conteudo_updated');
            $table->dropIndex('idx_proposicoes_arquivo_hash');
            
            $table->dropColumn([
                'arquivo_pdf_para_assinatura',
                'arquivo_pdf_assinado', 
                'arquivo_pdf_protocolado',
                'arquivo_hash',
                'pdf_base_hash',
                'conteudo_updated_at',
                'pdf_conversor_usado'
            ]);
        });
    }
};