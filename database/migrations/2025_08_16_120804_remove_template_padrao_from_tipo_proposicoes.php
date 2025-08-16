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
        Schema::table('tipo_proposicoes', function (Blueprint $table) {
            // Remover coluna template_padrao - sistema agora usa apenas templates específicos
            if (Schema::hasColumn('tipo_proposicoes', 'template_padrao')) {
                $table->dropColumn('template_padrao');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipo_proposicoes', function (Blueprint $table) {
            $table->string('template_padrao')->nullable()
                ->after('cor')
                ->comment('Template padrão para este tipo');
        });
    }
};