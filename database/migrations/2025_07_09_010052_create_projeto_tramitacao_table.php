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
        Schema::create('projeto_tramitacao', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('projeto_id')->constrained('projetos')->onDelete('cascade');
            $table->foreignId('usuario_id')->constrained('users')->onDelete('cascade');
            
            // Status anterior e atual conforme documento
            $table->enum('status_anterior', [
                'rascunho', 'enviado', 'em_analise', 'aprovado', 'rejeitado', 
                'assinado', 'protocolado', 'em_sessao', 'votado'
            ])->nullable();
            
            $table->enum('status_atual', [
                'rascunho', 'enviado', 'em_analise', 'aprovado', 'rejeitado', 
                'assinado', 'protocolado', 'em_sessao', 'votado'
            ]);
            
            // Ação realizada conforme documento
            $table->enum('acao', [
                'criou', 'enviou', 'analisou', 'aprovou', 'rejeitou', 
                'assinou', 'protocolou', 'incluiu_sessao', 'votou'
            ]);
            
            $table->text('observacoes')->nullable();
            
            $table->timestamps();
            
            // Índices
            $table->index(['projeto_id', 'created_at']);
            $table->index(['usuario_id', 'acao']);
            $table->index(['status_atual']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projeto_tramitacao');
    }
};