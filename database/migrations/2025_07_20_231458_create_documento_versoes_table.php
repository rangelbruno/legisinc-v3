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
        Schema::create('documento_versoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instancia_id');
            $table->string('arquivo_path', 500);
            $table->string('arquivo_nome', 255);
            $table->integer('versao');
            $table->unsignedBigInteger('modificado_por');
            $table->text('comentarios')->nullable();
            $table->string('hash_arquivo', 64);
            $table->timestamps();
            
            $table->foreign('instancia_id')->references('id')->on('documento_instancias')->onDelete('cascade');
            $table->foreign('modificado_por')->references('id')->on('users');
            
            $table->index(['instancia_id', 'versao']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_versoes');
    }
};
