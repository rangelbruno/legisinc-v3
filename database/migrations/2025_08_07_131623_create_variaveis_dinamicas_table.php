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
        Schema::create('variaveis_dinamicas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->unique();
            $table->text('valor');
            $table->string('descricao', 255)->nullable();
            $table->enum('tipo', ['texto', 'numero', 'data', 'boolean', 'url', 'email'])->default('texto');
            $table->enum('escopo', ['global', 'documentos', 'templates', 'sistema'])->default('global');
            $table->string('formato', 100)->nullable();
            $table->string('validacao', 200)->nullable();
            $table->boolean('sistema')->default(false);
            $table->boolean('ativo')->default(true);
            $table->integer('ordem')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index('nome');
            $table->index('escopo');
            $table->index('ativo');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variaveis_dinamicas');
    }
};
