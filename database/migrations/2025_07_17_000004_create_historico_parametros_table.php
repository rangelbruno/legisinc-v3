<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historico_parametros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parametro_id')->comment('Parâmetro relacionado');
            $table->unsignedBigInteger('user_id')->comment('Usuário que fez a alteração');
            $table->enum('acao', ['create', 'update', 'delete'])->comment('Tipo de ação');
            $table->text('valor_anterior')->nullable()->comment('Valor anterior');
            $table->text('valor_novo')->nullable()->comment('Valor novo');
            $table->json('dados_contexto')->nullable()->comment('Dados de contexto da alteração');
            $table->string('ip_address', 45)->nullable()->comment('IP do usuário');
            $table->text('user_agent')->nullable()->comment('User agent do usuário');
            $table->timestamp('data_acao')->useCurrent()->comment('Data da ação');
            $table->timestamps();

            // Índices
            $table->index(['parametro_id', 'data_acao']);
            $table->index('user_id');
            $table->index('acao');
            $table->index('data_acao');
            
            // Foreign keys
            $table->foreign('parametro_id')
                  ->references('id')
                  ->on('parametros')
                  ->onDelete('cascade');
                  
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historico_parametros');
    }
};