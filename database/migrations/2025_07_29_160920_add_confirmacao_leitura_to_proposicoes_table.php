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
            $table->boolean('confirmacao_leitura')->default(false)->after('status');
            $table->string('assinatura_digital')->nullable()->after('confirmacao_leitura');
            $table->text('certificado_digital')->nullable()->after('assinatura_digital');
            $table->timestamp('data_assinatura')->nullable()->after('certificado_digital');
            $table->string('ip_assinatura')->nullable()->after('data_assinatura');
            $table->timestamp('data_aprovacao_autor')->nullable()->after('ip_assinatura');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposicoes', function (Blueprint $table) {
            $table->dropColumn([
                'confirmacao_leitura',
                'assinatura_digital',
                'certificado_digital',
                'data_assinatura',
                'ip_assinatura',
                'data_aprovacao_autor'
            ]);
        });
    }
};