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
        Schema::table('parametros_campos', function (Blueprint $table) {
            $table->text('help_text')->nullable()->after('ordem')->comment('Texto de ajuda para o campo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parametros_campos', function (Blueprint $table) {
            $table->dropColumn('help_text');
        });
    }
};
