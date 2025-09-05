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
            $table->timestamp('pdf_gerado_em')->nullable()->after('arquivo_pdf_path');
            $table->string('pdf_conversor_usado', 50)->nullable()->after('pdf_gerado_em');
            $table->bigInteger('pdf_tamanho')->nullable()->after('pdf_conversor_usado');
            $table->text('pdf_erro_geracao')->nullable()->after('pdf_tamanho');
            $table->timestamp('pdf_tentativa_em')->nullable()->after('pdf_erro_geracao');
            
            $table->index(['status', 'pdf_gerado_em']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex(['status', 'pdf_gerado_em']);
            $table->dropColumn([
                'pdf_gerado_em',
                'pdf_conversor_usado', 
                'pdf_tamanho',
                'pdf_erro_geracao',
                'pdf_tentativa_em'
            ]);
        });
    }
};
