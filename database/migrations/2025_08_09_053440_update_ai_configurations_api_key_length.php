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
        Schema::table('ai_configurations', function (Blueprint $table) {
            // Aumentar o tamanho do campo api_key para 1000 caracteres para acomodar chaves criptografadas
            $table->string('api_key', 1000)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_configurations', function (Blueprint $table) {
            // Voltar ao tamanho original
            $table->string('api_key', 255)->nullable()->change();
        });
    }
};
