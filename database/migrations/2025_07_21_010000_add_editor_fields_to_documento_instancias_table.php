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
        Schema::table('documento_instancias', function (Blueprint $table) {
            $table->string('titulo')->nullable()->after('modelo_id');
            $table->string('arquivo_gerado_path')->nullable()->after('arquivo_nome');
            $table->string('arquivo_gerado_nome')->nullable()->after('arquivo_gerado_path');
            $table->longText('conteudo_personalizado')->nullable()->after('arquivo_gerado_nome');
            $table->json('variaveis_personalizadas')->nullable()->after('conteudo_personalizado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_instancias', function (Blueprint $table) {
            $table->dropColumn([
                'titulo',
                'arquivo_gerado_path',
                'arquivo_gerado_nome',
                'conteudo_personalizado',
                'variaveis_personalizadas'
            ]);
        });
    }
};