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
        Schema::create('auditoria_parametros', function (Blueprint $table) {
            $table->id();
            $table->string('entidade', 50)->index(); // modulo, submodulo, campo, valor
            $table->unsignedBigInteger('entidade_id')->index();
            $table->string('acao', 50)->index(); // created, updated, deleted, expired
            $table->json('dados_antigos')->nullable();
            $table->json('dados_novos')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // session_id, request_id, etc.
            $table->timestamp('created_at')->index();
            
            // Índices compostos para consultas mais eficientes
            $table->index(['entidade', 'entidade_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['acao', 'created_at']);
            $table->index(['created_at', 'entidade']);
            
            // Índice para consultas de relatório
            $table->index(['created_at', 'acao', 'entidade']);
            
            // Foreign key para usuário (opcional, pode ser null para ações do sistema)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
        
        // Adicionar comentário na tabela
        DB::statement("ALTER TABLE auditoria_parametros COMMENT = 'Tabela de auditoria para todas as operações do sistema de parâmetros'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_parametros');
    }
};