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
        Schema::create('proposicoes_historico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('proposicao_id');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->string('acao'); // 'create', 'edit', 'status_change', 'callback'
            $table->enum('tipo_alteracao', ['arquivo', 'conteudo', 'status', 'metadados'])->default('arquivo');
            
            // Snapshots dos dados principais
            $table->string('status_anterior')->nullable();
            $table->string('status_novo')->nullable();
            $table->string('arquivo_path_anterior')->nullable();
            $table->string('arquivo_path_novo')->nullable();
            $table->text('conteudo_anterior')->nullable();
            $table->text('conteudo_novo')->nullable();
            
            // Metadados da alteração
            $table->json('metadados')->nullable(); // IP, user agent, OnlyOffice data, etc.
            $table->string('origem')->default('onlyoffice'); // 'onlyoffice', 'web', 'api', 'system'
            $table->text('observacoes')->nullable();
            
            // Performance - diff em vez de conteúdo completo
            $table->longText('diff_conteudo')->nullable(); // JSON com mudanças específicas
            $table->integer('tamanho_anterior')->nullable(); // bytes
            $table->integer('tamanho_novo')->nullable(); // bytes
            
            // Auditoria
            $table->timestamp('data_alteracao');
            $table->string('ip_usuario')->nullable();
            $table->text('user_agent')->nullable();
            
            $table->timestamps();
            
            // Índices para performance
            $table->foreign('proposicao_id')->references('id')->on('proposicoes')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('set null');
            $table->index(['proposicao_id', 'created_at'], 'idx_historico_proposicao_data');
            $table->index(['usuario_id', 'acao'], 'idx_historico_usuario_acao');
            $table->index(['origem', 'tipo_alteracao'], 'idx_historico_origem_tipo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposicoes_historico');
    }
};
