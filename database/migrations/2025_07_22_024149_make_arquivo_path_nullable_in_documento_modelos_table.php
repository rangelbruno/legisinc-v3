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
            $table->string('arquivo_path', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_modelos', function (Blueprint $table) {
            $table->string('arquivo_path', 500)->nullable(false)->change();
        });
    }
};
