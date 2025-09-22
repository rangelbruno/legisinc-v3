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
        Schema::create('document_workflow_logs', function (Blueprint $table) {
            $table->id();

            // Relacionamentos
            $table->foreignId('proposicao_id')->constrained('proposicoes')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // Informações do evento
            $table->string('event_type'); // 'document_created', 'pdf_exported', 'document_signed', 'protocol_assigned', etc.
            $table->string('stage'); // 'creation', 'editing', 'export', 'signature', 'protocol', 'finalized'
            $table->string('action'); // 'create', 'export_pdf', 'sign', 'assign_protocol', etc.
            $table->string('status'); // 'success', 'error', 'pending', 'warning'

            // Detalhes do evento
            $table->text('description'); // Descrição legível do que aconteceu
            $table->json('metadata')->nullable(); // Dados específicos do evento (paths, tamanhos, etc.)

            // Informações de arquivos
            $table->string('file_path')->nullable(); // Caminho do arquivo relacionado
            $table->string('file_type')->nullable(); // 'rtf', 'pdf', 'docx'
            $table->bigInteger('file_size')->nullable(); // Tamanho do arquivo em bytes
            $table->string('file_hash')->nullable(); // Hash do arquivo para integridade

            // Informações específicas do protocolo
            $table->string('protocol_number')->nullable(); // Número do protocolo quando aplicável
            $table->timestamp('protocol_date')->nullable(); // Data do protocolo

            // Informações de assinatura
            $table->string('signature_type')->nullable(); // 'digital', 'eletronic'
            $table->string('certificate_info')->nullable(); // Informações do certificado usado
            $table->timestamp('signature_date')->nullable(); // Data da assinatura

            // IP e User Agent para auditoria
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();

            // Performance e debugging
            $table->integer('execution_time_ms')->nullable(); // Tempo de execução em ms
            $table->text('error_message')->nullable(); // Mensagem de erro se houver
            $table->text('stack_trace')->nullable(); // Stack trace para debugging

            $table->timestamps();

            // Índices para performance
            $table->index(['proposicao_id', 'created_at']);
            $table->index(['event_type', 'status']);
            $table->index(['stage', 'created_at']);
            $table->index('protocol_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_workflow_logs');
    }
};
