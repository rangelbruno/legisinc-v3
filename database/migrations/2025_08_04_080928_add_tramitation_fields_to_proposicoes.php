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
            // Campos para o processo de tramitação
            if (!Schema::hasColumn('proposicoes', 'observacoes_legislativo')) {
                $table->text('observacoes_legislativo')->nullable();
            }
            if (!Schema::hasColumn('proposicoes', 'arquivo_pdf')) {
                $table->string('arquivo_pdf')->nullable();
            }
            if (!Schema::hasColumn('proposicoes', 'assinado')) {
                $table->boolean('assinado')->default(false);
            }
            if (!Schema::hasColumn('proposicoes', 'tem_parecer_juridico')) {
                $table->boolean('tem_parecer_juridico')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropColumn([
                'observacoes_legislativo',
                'arquivo_pdf',
                'assinado',
                'tem_parecer_juridico'
            ]);
        });
    }
};
