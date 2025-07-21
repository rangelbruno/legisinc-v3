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
        Schema::create('documento_instancias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('projeto_id');
            $table->unsignedBigInteger('modelo_id');
            $table->string('arquivo_path', 500)->nullable();
            $table->string('arquivo_nome', 255)->nullable();
            $table->enum('status', ['rascunho', 'parlamentar', 'legislativo', 'finalizado'])->default('rascunho');
            $table->integer('versao')->default(1);
            $table->json('metadados')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->foreign('projeto_id')->references('id')->on('projetos')->onDelete('cascade');
            $table->foreign('modelo_id')->references('id')->on('documento_modelos');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->index(['projeto_id', 'status']);
            $table->index(['modelo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_instancias');
    }
};
