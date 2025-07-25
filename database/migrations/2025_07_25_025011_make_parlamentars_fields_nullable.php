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
        Schema::table('parlamentars', function (Blueprint $table) {
            // Tornar campos opcionais
            $table->string('telefone', 20)->nullable()->change();
            $table->date('data_nascimento')->nullable()->change();
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parlamentars', function (Blueprint $table) {
            // Reverter para NOT NULL
            $table->string('telefone', 20)->nullable(false)->change();
            $table->date('data_nascimento')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};
