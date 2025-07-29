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
            $table->text('observacoes_edicao')->nullable()->after('conteudo');
            $table->text('observacoes_retorno')->nullable()->after('observacoes_edicao');
            $table->timestamp('data_retorno_legislativo')->nullable()->after('observacoes_retorno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropColumn(['observacoes_edicao', 'observacoes_retorno', 'data_retorno_legislativo']);
        });
    }
};
