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
        Schema::table('documento_modelos', function (Blueprint $table) {
            $table->string('document_key')->unique()->after('tipo_proposicao_id');
            $table->string('icon')->nullable()->after('versao');
            
            $table->index('document_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_modelos', function (Blueprint $table) {
            $table->dropIndex(['document_key']);
            $table->dropColumn(['document_key', 'icon']);
        });
    }
};
