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
            
            // Etapa da tramitação
            $table->enum('etapa', [
                'protocolo',
                'distribuicao',
                'analise_comissao',
                'relatoria',
                'parecer',
                'emenda',
                'votacao_comissao',
                'plenario',
                'votacao_plenario',
                'sancao',
                'promulgacao',
                'publicacao',
                'arquivamento'
            ]);
            
            $table->enum('acao', [
                'criado',
                'enviado',
                'recebido',
                'analisado',
                'aprovado',
                'rejeitado',
                'emendado',
                'devolvido',
                'arquivado',
                'desarquivado'
            ]);
            
            // Responsáveis
            $table->foreignId('responsavel_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('comissao_id')->nullable()->constrained('comissoes')->onDelete('set null');
            $table->string('orgao_destino')->nullable(); // Para tramitação externa
            
            // Detalhes da movimentação
            $table->text('observacoes')->nullable();
            $table->text('despacho')->nullable();
            $table->date('prazo')->nullable(); // Prazo para conclusão
            
            // Controle temporal
            $table->timestamp('data_inicio');
            $table->timestamp('data_fim')->nullable();
            $table->integer('dias_tramitacao')->nullable(); // Calculado automaticamente
            
            // Status
            $table->enum('status', [
                'pendente',
                'em_andamento', 
                'concluido',
                'cancelado'
            ])->default('pendente');
            
            $table->boolean('urgente')->default(false);
            $table->integer('ordem')->default(0); // Para ordenar historico
            
            // Metadados
            $table->json('dados_complementares')->nullable(); // Dados específicos da etapa
            
            $table->timestamps();
            
            // Índices
            $table->index(['projeto_id', 'ordem']);
            $table->index(['projeto_id', 'etapa', 'status']);
            $table->index(['responsavel_id', 'status']);
            $table->index(['comissao_id', 'status']);
            $table->index('data_inicio');
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