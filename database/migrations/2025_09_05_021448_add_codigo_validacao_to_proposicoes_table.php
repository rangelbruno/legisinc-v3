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
            // Código de validação único para verificar assinatura
            $table->string('codigo_validacao', 20)->nullable()->after('data_assinatura');
            
            // URL para validação da assinatura
            $table->string('url_validacao')->nullable()->after('codigo_validacao');
            
            // QR Code em base64 para validação
            $table->text('qr_code_validacao')->nullable()->after('url_validacao');
            
            // Dados adicionais da assinatura para validação
            $table->json('dados_assinatura_validacao')->nullable()->after('qr_code_validacao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_validacao',
                'url_validacao', 
                'qr_code_validacao',
                'dados_assinatura_validacao'
            ]);
        });
    }
};