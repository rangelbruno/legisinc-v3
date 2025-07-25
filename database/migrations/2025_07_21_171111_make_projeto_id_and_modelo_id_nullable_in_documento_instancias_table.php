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
        Schema::table('documento_instancias', function (Blueprint $table) {
            // Remove foreign key constraints
            $table->dropForeign(['projeto_id']);
            $table->dropForeign(['modelo_id']);
            
            // Make the columns nullable
            $table->unsignedBigInteger('projeto_id')->nullable()->change();
            $table->unsignedBigInteger('modelo_id')->nullable()->change();
            
            // Add foreign key constraints back
            $table->foreign('projeto_id')->references('id')->on('projetos')->onDelete('cascade');
            $table->foreign('modelo_id')->references('id')->on('documento_modelos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documento_instancias', function (Blueprint $table) {
            // Remove foreign key constraints
            $table->dropForeign(['projeto_id']);
            $table->dropForeign(['modelo_id']);
            
            // Make the columns NOT NULL again
            $table->unsignedBigInteger('projeto_id')->nullable(false)->change();
            $table->unsignedBigInteger('modelo_id')->nullable(false)->change();
            
            // Add foreign key constraint back only for modelo_id (the one that existed)
            $table->foreign('modelo_id')->references('id')->on('documento_modelos');
        });
    }
};
