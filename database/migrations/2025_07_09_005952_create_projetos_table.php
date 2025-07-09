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
        Schema::create('projetos', function (Blueprint $table) {
            $table->id();
            
            // Identificação
            $table->string('titulo');
            $table->string('numero')->unique();
            $table->year('ano');
            $table->enum('tipo', [
                'projeto_lei_ordinaria',
                'projeto_lei_complementar', 
                'emenda_constitucional',
                'decreto_legislativo',
                'resolucao',
                'indicacao',
                'requerimento'
            ]);
            
            // Relacionamentos
            $table->foreignId('autor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('relator_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('comissao_id')->nullable(); // TODO: Adicionar foreign key quando tabela comissoes existir
            
            // Status e Controle
            $table->enum('status', [
                'rascunho',
                'protocolado',
                'em_tramitacao',
                'na_comissao',
                'em_votacao',
                'aprovado',
                'rejeitado',
                'retirado',
                'arquivado'
            ])->default('rascunho');
            
            $table->enum('urgencia', [
                'normal',
                'urgente',
                'urgentissima'
            ])->default('normal');
            
            // Conteúdo
            $table->text('resumo')->nullable();
            $table->text('ementa');
            $table->longText('conteudo')->nullable(); // Conteúdo HTML/JSON do editor
            $table->integer('version_atual')->default(1);
            
            // Metadados
            $table->text('palavras_chave')->nullable();
            $table->text('observacoes')->nullable();
            $table->date('data_protocolo')->nullable();
            $table->date('data_limite_tramitacao')->nullable();
            
            // Controle
            $table->boolean('ativo')->default(true);
            $table->json('metadados')->nullable(); // Campos extras flexíveis
            
            $table->timestamps();
            
            // Índices
            $table->index(['status', 'tipo']);
            $table->index(['autor_id', 'ano']);
            $table->index(['comissao_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projetos');
    }
};