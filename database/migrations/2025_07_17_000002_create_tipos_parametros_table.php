<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 100)->comment('Nome do tipo');
            $table->string('codigo', 50)->unique()->comment('Código identificador único');
            $table->string('classe_validacao', 255)->nullable()->comment('Classe para validação customizada');
            $table->json('configuracao_padrao')->nullable()->comment('Configurações padrão do tipo');
            $table->boolean('ativo')->default(true)->comment('Status ativo/inativo');
            $table->timestamps();

            // Índices
            $table->index(['ativo']);
            $table->index('codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_parametros');
    }
};