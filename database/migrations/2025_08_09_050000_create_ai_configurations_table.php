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
        Schema::create('ai_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nome amigável da configuração (ex: OpenAI Principal, Claude Backup)');
            $table->string('provider')->comment('Provedor da API (openai, anthropic, google, local)');
            $table->string('api_key')->nullable()->comment('Chave da API (criptografada)');
            $table->string('model')->comment('Modelo específico (gpt-4, claude-3.5-sonnet, etc)');
            $table->string('base_url')->nullable()->comment('URL base personalizada (para APIs locais ou proxies)');
            $table->integer('max_tokens')->default(2000)->comment('Máximo de tokens por requisição');
            $table->decimal('temperature', 3, 2)->default(0.70)->comment('Temperatura para criatividade (0.0 a 1.0)');
            $table->text('custom_prompt')->nullable()->comment('Prompt personalizado para este provedor');
            $table->integer('priority')->default(1)->comment('Prioridade para fallback (1 = maior prioridade)');
            $table->boolean('is_active')->default(true)->comment('Se esta configuração está ativa');
            $table->integer('daily_token_limit')->nullable()->comment('Limite diário de tokens (null = sem limite)');
            $table->integer('daily_tokens_used')->default(0)->comment('Tokens usados hoje');
            $table->date('last_reset_date')->nullable()->comment('Última data de reset do contador diário');
            $table->decimal('cost_per_1k_tokens', 8, 6)->nullable()->comment('Custo por 1000 tokens em USD');
            $table->json('additional_parameters')->nullable()->comment('Parâmetros adicionais específicos do provedor');
            $table->timestamp('last_tested_at')->nullable()->comment('Última vez que a conexão foi testada');
            $table->boolean('last_test_success')->default(false)->comment('Se o último teste foi bem-sucedido');
            $table->text('last_test_error')->nullable()->comment('Erro do último teste (se houver)');
            $table->timestamps();
            
            // Índices
            $table->index(['provider', 'is_active']);
            $table->index(['priority', 'is_active']);
            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_configurations');
    }
};