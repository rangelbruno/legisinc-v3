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
            $table->string('pdf_s3_path')->nullable()->after('pdf_exportado_em')->comment('Caminho do PDF no AWS S3');
            $table->text('pdf_s3_url')->nullable()->after('pdf_s3_path')->comment('URL assinada temporária do PDF no S3');
            $table->bigInteger('pdf_size_bytes')->nullable()->after('pdf_s3_url')->comment('Tamanho do PDF em bytes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropColumn(['pdf_s3_path', 'pdf_s3_url', 'pdf_size_bytes']);
        });
    }
};
