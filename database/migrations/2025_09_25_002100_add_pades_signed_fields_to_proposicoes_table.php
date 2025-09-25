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
            // PAdES signed PDF fields
            $table->string('pdf_s3_path_signed')->nullable()->after('pdf_size_bytes')
                ->comment('Caminho do PDF assinado no AWS S3');

            $table->text('pdf_s3_url_signed')->nullable()->after('pdf_s3_path_signed')
                ->comment('URL assinada temporária do PDF assinado no S3');

            $table->bigInteger('pdf_size_bytes_signed')->nullable()->after('pdf_s3_url_signed')
                ->comment('Tamanho do PDF assinado em bytes');

            $table->timestamp('pdf_signed_at')->nullable()->after('pdf_size_bytes_signed')
                ->comment('Data/hora da assinatura digital PAdES');

            // Verification and metadata fields
            $table->string('pades_verification_uuid')->nullable()->after('pdf_signed_at')
                ->comment('UUID para verificação pública da assinatura');

            $table->text('pades_metadata')->nullable()->after('pades_verification_uuid')
                ->comment('Metadados da assinatura PAdES (JSON)');

            // Index for verification
            $table->index('pades_verification_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropIndex(['pades_verification_uuid']);
            $table->dropColumn([
                'pdf_s3_path_signed',
                'pdf_s3_url_signed',
                'pdf_size_bytes_signed',
                'pdf_signed_at',
                'pades_verification_uuid',
                'pades_metadata'
            ]);
        });
    }
};