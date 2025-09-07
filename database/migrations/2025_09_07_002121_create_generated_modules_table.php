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
        Schema::create('generated_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Nome do módulo
            $table->string('slug')->unique(); // Slug para URLs
            $table->text('description')->nullable(); // Descrição
            $table->string('table_name')->unique(); // Nome da tabela gerada
            $table->json('fields_config'); // Configuração dos campos
            $table->json('relationships')->nullable(); // Relacionamentos
            $table->text('business_logic')->nullable(); // Código personalizado
            $table->boolean('has_crud')->default(true); // Gerar CRUD
            $table->boolean('has_permissions')->default(true); // Sistema de permissões
            $table->string('icon')->default('ki-element-11'); // Ícone do menu
            $table->string('color')->default('primary'); // Cor do tema
            $table->boolean('active')->default(true); // Módulo ativo
            $table->json('menu_config')->nullable(); // Configuração do menu
            $table->foreignId('created_by')->constrained('users'); // Criado por
            $table->timestamp('generated_at')->nullable(); // Data da geração
            $table->json('generated_files')->nullable(); // Arquivos gerados
            $table->string('status')->default('draft'); // draft, generated, error
            $table->text('generation_log')->nullable(); // Log da geração
            $table->timestamps();
            
            // Índices para performance
            $table->index(['active', 'status']);
            $table->index('created_by');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('generated_modules');
    }
};
