<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupos_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->comment('Nome do grupo');
            $table->string('codigo', 50)->unique()->comment('Código identificador único');
            $table->text('descricao')->nullable()->comment('Descrição do grupo');
            $table->string('icone', 50)->nullable()->comment('Ícone do grupo');
            $table->string('cor', 7)->nullable()->comment('Cor do grupo (hex)');
            $table->integer('ordem')->default(0)->comment('Ordem de exibição');
            $table->boolean('ativo')->default(true)->comment('Status ativo/inativo');
            $table->unsignedBigInteger('grupo_pai_id')->nullable()->comment('Grupo pai (hierarquia)');
            $table->timestamps();

            // Índices
            $table->index(['ativo', 'ordem']);
            $table->index('codigo');
            $table->index('grupo_pai_id');
            
            // Foreign keys
            $table->foreign('grupo_pai_id')
                  ->references('id')
                  ->on('grupos_parametros')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupos_parametros');
    }
};