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
        // Create AI providers table
        Schema::create('ai_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'openai', 'anthropic', 'google'
            $table->string('label'); // e.g., 'OpenAI', 'Anthropic (Claude)', 'Google AI'
            $table->text('description');
            $table->string('icon')->default('ki-brain'); // Keenicons icon class
            $table->string('base_url');
            $table->string('default_model');
            $table->json('supported_models')->nullable(); // Array of supported models
            $table->json('config_template')->nullable(); // Configuration form template
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_active')->default(false); // Only one can be active at a time
            $table->timestamps();
        });

        // Create AI provider configurations table
        Schema::create('ai_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->string('config_key'); // e.g., 'api_key', 'organization', 'base_url'
            $table->text('config_value'); // encrypted values
            $table->boolean('is_encrypted')->default(false);
            $table->timestamps();

            $table->foreign('provider_id')->references('id')->on('ai_providers')->onDelete('cascade');
            $table->unique(['provider_id', 'config_key']);
        });

        // Create AI token usage tracking table
        Schema::create('ai_token_usage', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->string('model');
            $table->integer('prompt_tokens')->default(0);
            $table->integer('completion_tokens')->default(0);
            $table->integer('total_tokens')->default(0);
            $table->string('operation_type')->nullable(); // e.g., 'completion', 'chat', 'embedding'
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('cost_usd', 10, 6)->default(0); // Cost in USD
            $table->timestamp('used_at');
            $table->json('metadata')->nullable(); // Additional context data
            $table->timestamps();

            $table->foreign('provider_id')->references('id')->on('ai_providers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            // Index for performance
            $table->index(['provider_id', 'used_at']);
            $table->index('used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_token_usage');
        Schema::dropIfExists('ai_provider_configs');
        Schema::dropIfExists('ai_providers');
    }
};