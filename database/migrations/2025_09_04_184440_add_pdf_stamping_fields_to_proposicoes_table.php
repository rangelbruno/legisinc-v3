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
            // PDF protocolado (com stamp de protocolo aplicado)
            $table->text('arquivo_pdf_protocolado')->nullable()->after('arquivo_pdf_assinado');
            
            // Controle do processo de stamping
            $table->boolean('pdf_protocolo_aplicado')->default(false)->after('arquivo_pdf_protocolado');
            $table->timestamp('data_aplicacao_protocolo')->nullable()->after('pdf_protocolo_aplicado');
            
            // Adicionar índices para performance
            $table->index('pdf_protocolo_aplicado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex(['pdf_protocolo_aplicado']);
            $table->dropColumn([
                'arquivo_pdf_protocolado',
                'pdf_protocolo_aplicado', 
                'data_aplicacao_protocolo'
            ]);
        });
    }
};