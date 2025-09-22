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
            $table->string('pdf_exportado_path')->nullable()->after('arquivo_pdf_path');
            $table->timestamp('pdf_exportado_em')->nullable()->after('pdf_exportado_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropColumn(['pdf_exportado_path', 'pdf_exportado_em']);
        });
    }
};
