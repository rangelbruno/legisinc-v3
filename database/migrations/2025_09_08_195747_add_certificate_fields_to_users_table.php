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
        Schema::table('users', function (Blueprint $table) {
            $table->string('certificado_digital_path')->nullable()->after('password');
            $table->string('certificado_digital_nome')->nullable()->after('certificado_digital_path');
            $table->timestamp('certificado_digital_upload_em')->nullable()->after('certificado_digital_nome');
            $table->date('certificado_digital_validade')->nullable()->after('certificado_digital_upload_em');
            $table->string('certificado_digital_cn')->nullable()->comment('Common Name do certificado')->after('certificado_digital_validade');
            $table->boolean('certificado_digital_ativo')->default(true)->after('certificado_digital_cn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'certificado_digital_path',
                'certificado_digital_nome',
                'certificado_digital_upload_em',
                'certificado_digital_validade',
                'certificado_digital_cn',
                'certificado_digital_ativo'
            ]);
        });
    }
};
