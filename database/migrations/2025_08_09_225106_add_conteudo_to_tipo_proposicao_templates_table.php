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
        Schema::table('tipo_proposicao_templates', function (Blueprint $table) {
            $table->text('conteudo')->nullable()->after('arquivo_path')
                ->comment('Conteúdo RTF/HTML do template armazenado diretamente no banco');
            $table->string('formato', 10)->default('rtf')->after('conteudo')
                ->comment('Formato do conteúdo: rtf, html, docx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_proposicao_templates', function (Blueprint $table) {
            $table->dropColumn(['conteudo', 'formato']);
        });
    }
};