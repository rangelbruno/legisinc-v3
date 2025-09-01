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
        Schema::create('template_universal', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->comment('Nome do template universal');
            $table->text('descricao')->nullable()->comment('Descrição do template');
            $table->string('document_key')->unique()->comment('Chave única do documento para OnlyOffice');
            $table->string('arquivo_path')->nullable()->comment('Caminho do arquivo do template');
            $table->longText('conteudo')->nullable()->comment('Conteúdo RTF/HTML do template');
            $table->string('formato', 10)->default('rtf')->comment('Formato do template (rtf, docx, html)');
            $table->json('variaveis')->nullable()->comment('Lista de variáveis disponíveis');
            $table->boolean('ativo')->default(true)->comment('Template ativo');
            $table->boolean('is_default')->default(false)->comment('Template padrão do sistema');
            $table->foreignId('updated_by')->nullable()->constrained('users')->comment('Usuário que atualizou');
            $table->timestamps();

            $table->index(['ativo', 'is_default']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_universal');
    }
};
