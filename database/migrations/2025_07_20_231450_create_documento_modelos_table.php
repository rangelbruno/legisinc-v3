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
        Schema::create('documento_modelos', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->text('descricao')->nullable();
            $table->unsignedBigInteger('tipo_proposicao_id')->nullable();
            $table->string('arquivo_path', 500);
            $table->string('arquivo_nome', 255);
            $table->bigInteger('arquivo_size');
            $table->json('variaveis')->nullable();
            $table->string('versao', 50)->default('1.0');
            $table->boolean('ativo')->default(true);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['tipo_proposicao_id', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_modelos');
    }
};
